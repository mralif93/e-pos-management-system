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

// Debug Route
Route::get('/debug-scope', function () {
    $user = auth()->user();
    $outletId = $user ? $user->outlet_id : 'NULL';

    $products = \App\Models\Product::where('is_active', true)
        ->when($outletId, function ($q) use ($outletId) {
            $q->whereHas('prices', function ($pq) use ($outletId) {
                $pq->where('outlet_id', $outletId);
            });
        })->get();

    return [
        'user' => $user ? $user->name : 'GUEST',
        'outlet_id' => $outletId,
        'visible_products' => $products->pluck('name'),
        'all_prices_db' => \App\Models\ProductOutletPrice::all()
    ];
});

// Alias the 'login' route to 'pos.login' for Laravel's default redirection
Route::get('/login', function () {
    return redirect()->route('pos.login');
})->name('login');


Route::middleware(['auth:web', 'pos.lock.check'])->group(function () { // Use 'web' guard for session-based auth
    Route::get('/pos', [PosController::class, 'index'])->name('pos.home')->middleware('can:access-pos'); // Add a gate for POS access
    Route::get('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout')->middleware('can:access-pos');
    Route::get('/pos/lock', [PosController::class, 'lock'])->name('pos.lock')->middleware('can:access-pos');
    Route::post('/pos/verify-pin', [PosController::class, 'verifyPinEndpoint'])->name('pos.verify-pin')->middleware('can:access-pos');
    Route::get('/pos/sales/{id}/receipt-pdf', [PosController::class, 'generateReceiptPdf'])->name('pos.sales.receipt-pdf')->middleware('can:access-pos');
});