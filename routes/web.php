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

    $count = DB::table('participants')
                ->where('service_id', $data['service_id'])
                ->sum('count_adults');
    $count += DB::table('participants')
                ->where('service_id', $data['service_id'])
                ->sum('count_children');
    $count += $data['count_children'] + $data['count_adults'];

    if ($count > 15)
    {
        $service_name = date('H:i', strtotime(DB::table('services')->where('id', $data['service_id'])->value('starting_at')));
        return redirect()
                ->back()
                ->withInput()
                ->withAlert(__('messages.error_service_full', ['service_time' => $service_name]));
    }

    $participant = tap(new App\Participant($data))->save();

    return redirect('/');
});

Route::apiResource('services', 'ServiceController');
Route::apiResource('participants', 'ParticipantController');

# only allow register if there is no user yet
$allow_register = false;
if (!app()->runningInConsole()) {
    // avoid accessing the database when running initial artisan migrate
    $allow_register = DB::table('users')->count() == 0;
}

Auth::routes(['register' => $allow_register]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/admin', 'HomeController@index')->name('admin');
