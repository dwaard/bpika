<?php

use Illuminate\Support\Facades\Auth;
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

/**
 * Public routes.
 */
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');


/**
 * Authentication routes.
 * Users must not be allowed to register themselves.
 */
Auth::routes(['register' => true]);


/**
 * Route group for all routes that are only allowed to authenticated and
 * verified users.
 */
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('home', 'HomeController@index')->name('home');
    Route::resource('users', 'UserController');

    Route::resource('stations', 'StationController');

    Route::get('profile','AccountController@edit')->name('account.edit');
    Route::patch('profile', 'AccountController@update')->name('account.update');
});
