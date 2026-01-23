<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('pos')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/products', [PosController::class, 'searchProducts'])->name('api.pos.products');
    Route::post('/sales', [PosController::class, 'processSale']);
});
