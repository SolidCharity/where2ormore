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

        $visitor_link = $request->getSchemeAndHttpHost();
        // this is not a single instance installation
        if (\DB::table('users')->where('tenant_id', "=", 1)->count() == 0)
        {
            $visitor_link .= "/?uuid=".$tenant->uuid;
        }

        return view('admin', ['services' => $services,
            'participants' => $participants, 'link_visitors' => $visitor_link]);
    }

    /// print a report with the visitors for each service
    public function report()
    {
        $tenant_id = Auth::user()->tenant_id;
        $services = \App\Service::where('tenant_id', $tenant_id)->get();
        $participants = \App\Participant::where('tenant_id', $tenant_id)->get();

        return view('report', ['services' => $services,
            'participants' => $participants]);
    }

    /// drop all participants, as preparation for next week's Sunday!
    public function dropAllParticipants()
    {
        $tenant_id = Auth::user()->tenant_id;
        $participants = \App\Participant::where('tenant_id', $tenant_id)->get();

        foreach ($participants as $participant)
        {
            $participant->delete();
        }

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
