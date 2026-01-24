<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController; // Add this
use App\Http\Controllers\PosLoginController;

Route::get('/', function () {
    return view('welcome');
});

// POS Login routes
Route::get('/pos/login', [PosLoginController::class, 'create'])->name('pos.login');
Route::post('/pos/login', [PosLoginController::class, 'store']);
Route::post('/pos/logout', [PosLoginController::class, 'destroy'])->name('pos.logout');

// Alias the 'login' route to 'pos.login' for Laravel's default redirection
Route::get('/login', function () {
    return redirect()->route('pos.login');
})->name('login');


Route::middleware(['auth:web'])->group(function () { // Use 'web' guard for session-based auth
    Route::get('/pos', [PosController::class, 'index'])->name('pos.home')->middleware('can:access-pos'); // Add a gate for POS access
    Route::get('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout')->middleware('can:access-pos');
});