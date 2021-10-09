<?php

use App\Http\Controllers\API\OrdersController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/orders'], function () {
    Route::group(['middleware' => ['AuthenticateGuardCustomers']], function () {
        Route::post('/', [OrdersController::class, 'store']);
        Route::post('/checkout', [OrdersController::class, 'checkout']);
    });
});
