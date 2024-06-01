<?php

use App\Http\Controllers\TransferenceController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureStoreKeeperCantTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('transfer', [TransferenceController::class, 'store'])->middleware(EnsureStoreKeeperCantTransfer::class);
    ;
    Route::post('logout', [UserController::class, 'logout']);
});

Route::post('register-common-user', [UserController::class, 'registerCommonUser']);
Route::post('register-store-keeper', [UserController::class, 'registerStoreKeeper']);
Route::post('login', [UserController::class, 'login']);
