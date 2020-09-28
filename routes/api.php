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

Route::middleware('auth:api')->group(function() {
    Route::get('user', 'Api\UserController@user');
});

Route::get('ping', 'Api\PingController@handle');

Route::get('store', 'Api\MeasurementController@store');

Route::get('measurement/startDate={startDate?}&endDate={endDate?}&stations={stations?}&grouping={grouping?}&aggregation={aggregation?}&columns={columns?}&order={order?}', 'Api\MeasurementController@getJSON');

