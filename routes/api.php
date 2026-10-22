<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('books', BookController::class)
        ->only(['index', 'show']);

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('books', BookController::class)
            ->only(['store', 'update', 'destroy']);
    });
});
