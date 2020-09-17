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

Auth::routes();

Route::get('/', function () {
    return view('welcome');
});



Route::get('dashboard', 'DashboardController@index');

Route::get('/home', 'HomeController@index')->name('home');
