<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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

Route::view('/', 'welcome');

//Route::get('/testsync', [\App\Http\Controllers\DealerController::class, 'sync']);

/* Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard'); */

Route::view('profile', 'livewire.profile.index')
    ->middleware(['auth'])
    ->name('profile');

//Logout
Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::middleware(['auth', 'verified'])->group(function () {
   //Volt::route('profile', 'profile.index')->name('profile');
    Volt::route('dashboard', 'dashboard')->name('dashboard');
//    Volt::route('boats', 'boats.index')->name('boats'); TROCAR SE FOR PRECISO DINAMISMO NA PAGINA

    Route::prefix('boats')->group(function(){
        Route::view('', 'livewire.boats.index')->name('boats');
        Volt::route('/order', 'boats.order')->name('boats.order');
        Volt::route('/{boatRegistration}', 'boats.show')->name('boats.show');
    });

    Route::prefix('paddle-lab')->group(function(){
        Volt::route('', 'paddlelab.index')->name('paddle-lab');
        Volt::route('order/{order}', 'paddlelab.show')->name('paddle-lab.order');
    });

    Route::prefix('membership')->group(function (){
       Volt::route('', 'membership.index')->name('membership');
    });

    Route::prefix('coach')->group(function (){
        Volt::route('', 'coach.index')->name('coach');
        Volt::route('{session}', 'coach.show')->name('coach.show');
    });

    Route::get('testing', [\App\Http\Controllers\TestingController::class, 'index']);
});

/**
 * Boat registratiion validation
 */
Route::get('/register/validate/{boatregistration}/{hash}', [\App\Http\Controllers\BoatRegistrationController::class, 'validateRegistration']);
Route::get('/register/cancel/{boatregistration}/{hash}', [\App\Http\Controllers\BoatRegistrationController::class, 'cancelRegistration']);

/**
 * QR Code
 */
Route::get('boatid/{boat_id}', \App\Livewire\Boats\ShowPublic::class)->name('boats.public');
Route::get('pool/{boat_id}', \App\Livewire\Boats\ShowPublic::class)->name('boats.pool');



/**
 * Error pages
 */
Route::view('/boat-not-found', 'errors.qrcode-notfound')->name('boat-not-found');

require __DIR__.'/auth.php';
