<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ServiceController;

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

Route::delete('participants1/{service_id?}',
    function ($service_id = null) {
        return AdminController::dropAllParticipants($service_id);
    })->name('dropAllParticipants');
Route::get('/report/{service_id?}',
   function ($service_id=null) {
       return AdminController::report($service_id);
   })->name('report');
Route::patch('serviceToggleActivation/{service_id?}',
   function ($service_id = null) {
       return ServiceController::toggleActivation($service_id);
   })->name('serviceToggleActivation');

Route::patch('tenants', 'AdminController@updateChurchName')->name('updateChurchName');
Route::patch('tenants2', 'AdminController@updateCollectContactDetails')->name('updateCollectContactDetails');
Route::patch('tenants3', 'AdminController@updateOptionToReportContactDetails')->name('updateOptionToReportContactDetails');
Route::patch('tenants4', 'AdminController@updateTextForSignupForClosedEvent')->name('updateTextForSignupForClosedEvent');
Route::patch('tenants5', 'AdminController@updateOptionForSeparateFirstname')->name('updateOptionForSeparateFirstname');
Route::patch('tenants6', 'AdminController@updateOptionToDeclare2g')->name('updateOptionToDeclare2g');
Route::patch('tenants7', 'AdminController@updateOptionFor3GSignatures')->name('updateOptionFor3GSignatures');
Route::patch('tenants8', 'AdminController@updateTextFor3GRulesDescription')->name('updateTextFor3GRulesDescription');
Route::delete('participants2', 'FrontendController@cancelregistration')->name('cancelregistration');

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
