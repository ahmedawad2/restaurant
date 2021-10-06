<?php

use App\Http\Controllers\API\ItemsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/items'], function () {
    Route::get('/', [ItemsController::class, 'index']);


    Route::group(['middleware' => ['AuthenticateGuardCustomers']], function () {

    });
});
