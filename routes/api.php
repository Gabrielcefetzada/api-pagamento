<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('logout', [UserController::class, 'logout']);
});

Route::post('register-store-keeper', [UserController::class, 'registerStoreKeeper']);
Route::post('login', [UserController::class, 'login']);
