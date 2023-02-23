<?php

use App\Http\Controllers\API\Admin\AuthController;
use App\Http\Controllers\API\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\API\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

// Admin routes.
Route::prefix('admin')->name('admin.')
    ->middleware(['auth:admin'])
    ->group(function () {
        // Admin login/logout route.
        Route::controller(AuthController::class)
            ->group(function () {
                Route::post('login', 'login')->name('login')
                    ->withoutMiddleware(['auth:admin']);
                Route::get('logout', 'logout')->name('logout');
            });

        // Admin customer-action routes.
        Route::name('customer.')
            ->controller(AdminCustomerController::class)
            ->group(function () {
                Route::post('customer', 'store')->name('store');
                Route::get('customers', 'index')->name('index');
                Route::put('customer/{customer}', 'update')->name('update');
                Route::delete('customer/{customer}', 'destroy')->name('destroy');
            });
    });

// Customer routes.
Route::prefix('customer')->name('customer.')
    ->controller(CustomerController::class)
    ->group(function () {
        Route::post('register', 'store')->name('register');
        Route::post('login', 'login')->name('login');
        Route::get('logout', 'logout')->name('logout')
            ->middleware(['auth:customer']);
    });
