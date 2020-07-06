<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('ping', 'Api\PingController@handle');

Route::get('store', 'Api\MeasurementController@store');

Route::get('getMeasurement/inputFormat={inputFormat?}&startDate={startDate?}&endDate={endDate?}&station={station?}&outputFormat={outputFormat?}&columns={columns?}', 'Api\MeasurementController@getJSON');

