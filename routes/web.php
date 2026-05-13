<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('posauth')->group(function () {
    Route::get('/pos', [ProductController::class, 'index'])->name('pos.index');
    Route::get('/productos/lista', [ProductController::class, 'list'])->name('products.list');
    Route::post('/ventas/{product}', [SaleController::class, 'store'])->name('sales.store');

    Route::middleware('admin')->group(function () {
        Route::post('/productos', [ProductController::class, 'store'])->name('products.store');
        Route::match(['put', 'post'], '/productos/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/productos/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });
});
