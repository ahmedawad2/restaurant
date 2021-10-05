<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verifyOTP', [AuthController::class, 'verifyOTP']);
    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::group(['middleware' => ['AuthenticateGuardCustomers']], function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
