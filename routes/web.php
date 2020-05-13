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


Route::apiResource('frontend', 'FrontendController');
Route::apiResource('services', 'ServiceController');
Route::apiResource('participants', 'ParticipantController');

Route::delete('participants', 'AdminController@dropAllParticipants')->name('dropAllParticipants');
Route::patch('tenants', 'AdminController@updateChurchName')->name('updateChurchName');

# only allow register if there is no user yet
$allow_register = false;
// avoid accessing the database when running initial artisan migrate
if (!app()->runningInConsole()) {
    // only allow registering of users, if there is not a tenant with 1.
    // for multi tenant, create first tenant, then delete it in the database:
    // delete from tenants where id=1;
    // delete from users where tenant_id=1;
    $allow_register = DB::table('users')->where('tenant_id', "=", 1)->count() == 0;
}

Auth::routes(['register' => $allow_register]);

Route::get('/', 'FrontendController@index')->name('frontend');
Route::get('/home', 'AdminController@index')->name('home');
Route::get('/admin', 'AdminController@index')->name('admin');
Route::get('/report', 'AdminController@report')->name('report');
