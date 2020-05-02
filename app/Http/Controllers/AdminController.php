<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    public function index()
    {
        // get all services
        $services = \App\Service::all();
        $participants = \App\Participant::all();

        return view('admin', ['services' => $services,
            'participants' => $participants]);
    }

    /// drop all participants, as preparation for next week's Sunday!
    public function dropAllParticipants()
    {
        $participants = \App\Participant::all();

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
