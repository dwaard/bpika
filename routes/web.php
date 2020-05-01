<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});
//$router->get('/', function () use ($router) {
//    return $router->app->version();
//});


Route::get('api/ping', 'Api\PingController@handle');
//$router->get('api/ping', 'Api\PingController@handle');

Route::get('api/store', 'Api\MeasurementController@store');
//$router->get('api/store', 'Api\MeasurementController@store');
