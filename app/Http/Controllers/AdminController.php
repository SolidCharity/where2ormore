<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $tenant_id = Auth::user()->tenant_id;

        // get all services
        $services = \App\Service::where('tenant_id', $tenant_id)->get();
        $participants = \App\Participant::where('tenant_id', $tenant_id)->get();
        $tenant = \DB::table('tenants')->where('id', $tenant_id)->first();

        if (!empty($tenant->external_url))
        {
            $visitor_link = $tenant->external_url;
        }
        else
        {
            $visitor_link = $request->getSchemeAndHttpHost();
            // this is not a single instance installation
            if (\DB::table('users')->where('tenant_id', "=", 1)->count() == 0)
            {
                $visitor_link .= "/?uuid=".$tenant->uuid;
            }
        }

        $churchname = $tenant->name;

        return view('admin', ['services' => $services,
            'participants' => $participants, 'link_visitors' => $visitor_link, 'churchname' => $churchname]);
    }

    /// print a report with the visitors for each service
    static public function report($service_id)
    {
        if (empty(Auth::user())) {
            return redirect('/login');
        }

        $tenant_id = Auth::user()->tenant_id;

        if (empty($service_id)) {
            $participants = \App\Participant::where('tenant_id', $tenant_id)->get();
            $services = \App\Service::where('tenant_id', $tenant_id)->get();
        } else {
            $participants = \App\Participant::where([['tenant_id', $tenant_id],['service_id',$service_id]])->get();
            $services = \App\Service::where([['tenant_id', $tenant_id],['id',$service_id]])->get();
        }

        return view('report', ['services' => $services,
            'participants' => $participants]);
    }

    /// drop all participants, as preparation for next week's Sunday!
    static public function dropAllParticipants($service_id)
    {
        if (empty(Auth::user())) {
            return redirect('/login');
        }

        $tenant_id = Auth::user()->tenant_id;

        if (empty($service_id)) {
            $participants = \App\Participant::where('tenant_id', $tenant_id)->get();
        } else {
            $participants = \App\Participant::where([['tenant_id', $tenant_id],['service_id',$service_id]])->get();
        }

        foreach ($participants as $participant)
        {
            $participant->delete();
        }

        return redirect('/admin');
    }

    /// update the name of the current tenant
    public function updateChurchname(Request $request)
    {
        $tenant_id = Auth::user()->tenant_id;

        $data = $request->validate([
            'churchname' => 'nullable|string',
        ]);

        if (empty($data['churchname'])) {
		$data = array('churchname' => '');
        }

        $tenant = \App\Tenant::
            where('id',$tenant_id)->first();
        $tenant->name = $data['churchname'];
        $tenant->save();

        return redirect('/admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminHome()
    {
        return view('adminHome');
    }
}
