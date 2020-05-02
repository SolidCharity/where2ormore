<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = \App\Service::all();
        return view('welcome', ['services' => $services]);
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
            'name' => 'required|max:255',
            'service_id' => 'required|integer',
            'count_adults' => 'required|integer',
            'count_children' => 'integer',
        ]);

        $count = \DB::table('participants')
                ->where('service_id', $data['service_id'])
                ->sum('count_adults');
        $count += \DB::table('participants')
                ->where('service_id', $data['service_id'])
                ->sum('count_children');
        $count += $data['count_children'] + $data['count_adults'];

        $service_name = \DB::table('services')->where('id', $data['service_id'])->value('description');

        if ($count > 15)
        {
            return redirect()
                ->back()
                ->withInput()
                ->withAlert(__('messages.error_service_full', ['name' => $service_name]));
        }

        $participant = tap(new \App\Participant($data))->save();

        return redirect('/')->
            withAlert(__('messages.success_participant_added', 
                ['name' => $service_name,
                 'count' => $data['count_children'] + $data['count_adults']]));
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
