<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TenderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('tenders', TenderController::class);
    Route::post('/tenders/{id}/attach-product', [TenderController::class, 'attachProduct']);
    Route::post('/tenders/{id}/detach-product', [TenderController::class, 'detachProduct']);
});

