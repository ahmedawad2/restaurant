<?php

use App\Http\Controllers\API\ReservationsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/reservations'], function () {
    Route::group(['middleware' => ['AuthenticateGuardCustomers']], function () {
        Route::post('/', [ReservationsController::class, 'store']);
    });
});
