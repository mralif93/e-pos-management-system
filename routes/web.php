<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Pos\App; // Import the Livewire component
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
    Route::get('/pos', App::class)->name('pos.home')->middleware('can:access-pos'); // Add a gate for POS access
});