<?php

use App\Http\Controllers\API\Admin\CustomerController as AdminCustomer;
use App\Http\Controllers\API\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

// Admin action routes.
Route::name('admin.')
    // ->middleware(['auth:sanctum'])
    ->controller(AdminCustomer::class)
    ->group(function () {
        Route::post('/admin/customer', 'store')->name('customer.store');
    });

// Customer action routes.
Route::post('/customer/register', [CustomerController::class, 'register'])->name('customer.register');
