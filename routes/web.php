<?php

use App\Http\Controllers\Dashboard;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StationController;
use App\Http\Livewire\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('https://www.projectenportfolio.nl/wiki/index.php/PR_00315');
})->name('home');

Route::get('/dashboard', Dashboard::class)->name('dashboard');

Route::get('/stations/{station}/cast', [StationController::class, 'cast']);

Route::middleware('auth')->group(function () {
    Route::resource('stations', StationController::class);
    Route::put('stations/{station}', [StationController::class, 'enable'])
        ->name('stations.enable');

    Route::get('users', UsersIndex::class)->name('users.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
