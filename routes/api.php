<?php

use App\Http\Controllers\Api\MeasurementController;
use App\Http\Controllers\Api\Ping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('ping', Ping::class);

Route::get('store', [MeasurementController::class, 'store']);

Route::get(
    'stations/{station}/measurements',
    [MeasurementController::class, 'getChartTimeSeries']
);
