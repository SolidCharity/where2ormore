<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $services = \App\Service::all();
    return view('welcome', ['services' => $services]);
});

Route::post('/submitParticipant', function (Request $request) {
    $data = $request->validate([
        'name' => 'required|max:255',
        'service_id' => 'required|integer',
        'count_adults' => 'required|integer',
        'count_children' => 'integer',
    ]);

    $participant = tap(new App\Participant($data))->save();

    return redirect('/');
});
