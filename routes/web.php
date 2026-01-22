<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POSController;

Route::get('/', function () {
    return view('welcome');
});

// Outlet Switcher Route
Route::post('/outlet/switch', function () {
    $outletId = request()->input('outlet_id');
    session(['selected_outlet_id' => $outletId]);
    return redirect()->back();
})->middleware('auth')->name('outlet.switch');

// POS Routes
Route::middleware(['auth', App\Http\Middleware\CheckPOSAccess::class])->prefix('pos')->group(function () {
    Route::get('/', [POSController::class, 'index'])->name('pos.index');

    // AJAX APIs
    Route::get('/api/products', [POSController::class, 'searchProducts'])->name('pos.api.products');
    Route::get('/api/customer/{phone}', [POSController::class, 'searchCustomer'])->name('pos.api.customer');
    Route::post('/api/customer', [POSController::class, 'createCustomer'])->name('pos.api.customer.create');
    Route::post('/api/transaction', [POSController::class, 'createTransaction'])->name('pos.api.transaction');

    // Receipt
    Route::get('/receipt/{transaction}', [POSController::class, 'printReceipt'])->name('pos.receipt');
});
