<?php

use App\Http\Controllers\API\CustomerController;
use Illuminate\Support\Facades\Route;


Route::prefix('customer')->name('customer.')
    ->group(function(){
        Route::post('/register', [CustomerController::class, 'register'])
            ->name('register');
        Route::post('/login', [CustomerController::class, 'login'])
            ->name('login');
        Route::get('/logout', [CustomerController::class,'logout'])
            ->middleware('auth:sanctum')
            ->name('logout');
});



