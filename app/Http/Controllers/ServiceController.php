<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class ServiceController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string',
        ]);

        $tenant_id = Auth::user()->tenant_id;
        $data['tenant_id'] = $tenant_id;

        $service = tap(new \App\Service($data))->save();

        return redirect('/admin');
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
        $data = $request->validate([
            'description' => 'required|string',
            'max_visitors' => 'required|integer',
        ]);

        $tenant_id = Auth::user()->tenant_id;
        $service = \App\Service::
            where([['id',$id],['tenant_id', $tenant_id]])->first();
        $service->description = $data['description'];
        $service->max_visitors = $data['max_visitors'];
        $service->save();

        return redirect('/admin');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // check for participants
        $tenant_id = Auth::user()->tenant_id;
        $count = \DB::table('participants')
                ->where([['tenant_id', $tenant_id],
                         ['service_id', $id]])
                ->sum('count_adults');

        if ($count > 0) {
            $participants = \App\Participant::where([['tenant_id', $tenant_id],['service_id', $id]])->get();
            foreach ($participants as $participant)
            {
                $participant->delete();
            }
        }

        $service = \App\Service::
            where([['id', $id],['tenant_id', $tenant_id]])->first();
        $service->delete();

        return redirect('/admin');
    }
}
