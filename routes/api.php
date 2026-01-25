<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('pos')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/products', [PosController::class, 'searchProducts'])->name('api.pos.products');
    Route::post('/sales', [PosController::class, 'processSale'])->name('api.pos.sales');
    Route::get('/history', [PosController::class, 'history'])->name('api.pos.history');

    Route::get('/sales/{id}/void', [PosController::class, 'voidSale'])->name('api.pos.void');
    Route::get('/categories', [PosController::class, 'getCategories'])->name('api.pos.categories');

    Route::post('/verify-pin', [PosController::class, 'verifyPinEndpoint'])->name('api.pos.verify-pin');
    Route::get('/customers', [PosController::class, 'searchCustomer'])->name('api.pos.customers.search');
    Route::post('/customers', [PosController::class, 'createCustomer'])->name('api.pos.customers.create');

    // POS Manager Auth
    Route::post('/verify-pin', [PosController::class, 'verifyPin'])->name('api.pos.verify-pin');

    // POS Settings
    Route::get('/settings', [PosController::class, 'getSettings'])->name('api.pos.settings');
});
