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
    Route::view('dashboard', 'dashboard')->name('dashboard');
//    Volt::route('boats', 'boats.index')->name('boats'); TROCAR SE FOR PRECISO DINAMISMO NA PAGINA

    Route::view('boats', 'livewire.boats.index')->name('boats');
    Volt::route('boats/{boatRegistration}', 'boats.show')->name('boats.show');

    Route::view('paddle-lab', 'livewire.paddlelab.index')->name('paddle-lab');

    Route::view('coach', 'livewire.coach.index')->name('coach');

    Route::get('testing', [\App\Http\Controllers\TestingController::class, 'index']);
    Route::get('retry_sync/{boatRegistration}', [\App\Http\Controllers\BoatRegistrationController::class, 'retrySync']);
});

Route::get('/register/validate/{boatregistration}/{hash}', [\App\Http\Controllers\BoatRegistrationController::class, 'validateRegistration']);
Route::get('/register/cancel/{boatregistration}/{hash}', [\App\Http\Controllers\BoatRegistrationController::class, 'cancelRegistration']);

require __DIR__.'/auth.php';
