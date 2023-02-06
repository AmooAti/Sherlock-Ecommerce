<?php

use App\Http\Controllers\API\CustomerController;
use Illuminate\Support\Facades\Route;

Route::post('/customer/register', [CustomerController::class, 'register'])->name('customer.register');
