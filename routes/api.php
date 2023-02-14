<?php

use App\Http\Controllers\API\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\API\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

// Admin action routes.
Route::prefix('admin')->name('admin.customer.')
    // ->middleware(['auth:sanctum'])
    ->controller(AdminCustomerController::class)
    ->group(function () {
        Route::post('/customer', 'store')->name('store');
        Route::get('/customers', 'index')->name('index');
    });

// Customer action routes.
Route::prefix('customer')->name('customer.')
    ->controller(CustomerController::class)
    ->group(function () {
        Route::post('/register', 'store')->name('register');
        Route::post('/login', 'login')->name('login');
        Route::get('/logout', 'logout')
            ->middleware('auth:sanctum')->name('logout');
    });
