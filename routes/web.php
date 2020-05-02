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

# only allow register if there is no user yet
$allow_register = false;
if (!app()->runningInConsole()) {
    // avoid accessing the database when running initial artisan migrate
    $allow_register = DB::table('users')->count() == 0;
}

Auth::routes(['register' => $allow_register]);

Route::get('/', 'FrontendController@index')->name('frontend');
Route::get('/home', 'AdminController@index')->name('home');
Route::get('/admin', 'AdminController@index')->name('admin');
Route::get('/report', 'AdminController@report')->name('report');
