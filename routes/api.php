<?php

use App\Http\Controllers\API\CustomerController;
use Illuminate\Support\Facades\Route;

Route::post('/customer/register', [CustomerController::class, 'register'])->name('customer.register');
Route::post('/customer/login', [CustomerController::class, 'login'])
    ->name('customer.login');

Route::middleware('auth:sanctum')->get('/customer/logout', [CustomerController::class,'logout'])
    ->name('customer.logout');
