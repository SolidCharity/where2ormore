<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth;

class FrontendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $request->validate([
            'uuid' => 'uuid',
        ]);

        // uuid is passed as GET parameter
        if (!empty($data['uuid']))
        {
            $tenant = \DB::table('tenants')->where('uuid', $data['uuid'])->first();
            if ($tenant)
            {
                $tenant_id = $tenant->id;
                $churchname = $tenant->name;
                $uuid = $data['uuid'];

                if (!empty($tenant->external_url))
                {
                    return redirect($tenant->external_url);
                }
            }
        }

        if (empty($uuid))
        {
            $tenant = \DB::table('tenants')->where('subdomain', $_SERVER['SERVER_NAME'])->first();
            if ($tenant)
            {
                $uuid = $tenant->uuid;
                $tenant_id = $tenant->id;
                $churchname = $tenant->name;
            }
        }

        // admin is logged in. this might actually be confusing...
        /*
        if (empty($uuid) && (Auth::user() != null))
        {
            $tenant_id = Auth::user()->tenant_id;
            $uuid = \DB::table('tenants')->where('id', $tenant_id)->first()->uuid;
        }
        */

        // this is a single instance installation
        if (empty($uuid) && (\DB::table('users')->where('tenant_id', "=", 1)->count() == 1))
        {
            $tenant_id = 1;
            $uuid = \DB::table('tenants')->where('id', 1)->first()->uuid;
            $churchname = '';
        }

        if (empty($uuid))
        {
            // no tenant has been selected
            return redirect('https://wo2odermehr.de');
        }

        $display = array();
        $display['uuid'] = $uuid;
        $display['services'] = \App\Service::where('tenant_id', $tenant_id)->get();
        $display['churchname'] = $churchname;
        $display['hidechurchname'] = empty($churchname)?'hidden':'';
        $display['hideselectservice'] = (count($display['services']) == 1)?'hidden':'';
        $display['checkedservice'] = (count($display['services']) == 1)?'checked':'';
        $display['registered'] = array();

        $registered_service = "";
        if (isset($_COOKIE['where2ormore_registration']) && !empty($_COOKIE['where2ormore_registration']))
        {
            // get all registrations of this visitor
            $participants = \DB::table('participants')->where([['tenant_id',$tenant_id],
                ['cookieid', $_COOKIE['where2ormore_registration']]])->get();

            foreach ($participants as $participant)
            {
                $service = \App\Service::where([['id',$participant->service_id], ['tenant_id',$tenant_id]])->first();
                $display['registered'][] = array(
                    'name' => $participant->name,
                    'participant_id' => $participant->id,
                    'service' => $service->description,
                    'count' => $participant->count_children + $participant->count_adults,
                );
            }
        }

        return view('frontend', $display);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    function redirect_url($uuid, $tenant_id)
    {
        $url = '/';
        if ($tenant_id != 1)
        {
            $tenant = \DB::table('tenants')->where('uuid', $uuid)->first();
            if (!empty($tenant->external_url))
            {
                $url = $tenant->external_url.'/';
            }
            else
            {
                $url .= '?uuid='.$uuid;
            }
        }

        return redirect($url);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'service_id' => 'required|integer',
            'uuid' => 'required|uuid',
            'count_adults' => 'required|integer',
            'count_children' => 'integer',
        ]);

        if (isset($_COOKIE['where2ormore_registration']) && !empty($_COOKIE['where2ormore_registration']))
        {
            // reuse existing cookie
            $data['cookieid'] = $_COOKIE['where2ormore_registration'];
        }
        else
        {
            $data['cookieid'] = (string) Str::uuid();
        }

        $tenant_id = \DB::table('tenants')->where('uuid', $data['uuid'])->first()->id;
        $data['tenant_id'] = $tenant_id;
        $count = \DB::table('participants')
                ->where([['service_id', $data['service_id']], ['tenant_id',$tenant_id]])
                ->sum('count_adults');
        $count += \DB::table('participants')
                ->where([['service_id', $data['service_id']], ['tenant_id',$tenant_id]])
                ->sum('count_children');
        $count += $data['count_children'] + $data['count_adults'];

        $service = \App\Service::where([['id',$data['service_id']], ['tenant_id',$tenant_id]])->first();

        if ($count > $service->max_visitors)
        {
            return redirect()
                ->back()
                ->withInput()
                ->withAlert(__('messages.error_service_full', ['name' => $service->description]));
        }

        if (!$service->registration_open)
        {
            return redirect()
                ->back()
                ->withInput()
                ->withAlert(__('messages.error_registration_closed', ['name' => $service->description]));
        }

        $participant = tap(new \App\Participant($data))->save();

        // keep the cookie for a week
        setcookie ( 'where2ormore_registration', $participant->cookieid, array('expires'=>time()+60*60*24*7, 'samesite'=>'strict', 'httponly'=>true));

        return $this->redirect_url($data['uuid'], $tenant_id);
    }

    public function cancelregistration(Request $request)
    {
        $data = $request->validate([
            'uuid' => 'required|uuid',
            'participant_id' => 'required|integer',
        ]);

        $tenant_id = \DB::table('tenants')->where('uuid', $data['uuid'])->first()->id;

        if (isset($_COOKIE['where2ormore_registration']) && !empty($_COOKIE['where2ormore_registration']))
        {
            // find the participant
            $participant = \DB::table('participants')->where(
                [['cookieid', $_COOKIE['where2ormore_registration']],
                 ['id', $data['participant_id']],
                 ['tenant_id', $tenant_id]]
                )->delete();
        }

        return $this->redirect_url($data['uuid'], $tenant_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
