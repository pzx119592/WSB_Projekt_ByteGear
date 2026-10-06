<?php

use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('catalog');
Route::get('/produkty/{product}', [CatalogController::class, 'show'])->name('products.show');
Route::get('/produkty/{product}/euro', [CatalogController::class, 'euro'])->name('products.euro');
Route::get('/koszyk', [CartController::class, 'index'])->name('cart');
Route::post('/koszyk/{product}', [CartController::class, 'add'])->name('cart.add')->block();
Route::patch('/koszyk/{product}', [CartController::class, 'update'])->name('cart.update')->block();
Route::delete('/koszyk/{product}', [CartController::class, 'destroy'])->name('cart.destroy')->block();
Route::middleware('guest')->group(function () {
    Route::get('/logowanie', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/logowanie', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.store');
    Route::get('/rejestracja', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/rejestracja', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.store');
});
Route::middleware('auth')->group(function () {
    Route::post('/wyloguj', [AuthController::class, 'logout'])->name('logout');
    Route::get('/konto', [OrderController::class, 'dashboard'])->name('dashboard');
    Route::get('/zamowienie', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/zamowienie', [OrderController::class, 'store'])->name('checkout.store')->block(10, 10);
    Route::get('/zamowienia', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/zamowienia/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::prefix('panel')->name('admin.')->middleware('role:admin,moderator')->group(function () {
        Route::resource('products', AdminProductController::class)->except('show');
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
        Route::resource('users', AdminUserController::class)->middleware('role:admin');
    });
});
