<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'update']);
    Route::post('/profile/image', [UserController::class, 'updateImage']);
    Route::put('/profile/password', [UserController::class, 'updatePassword']);

    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{userId}', [OrderController::class, 'index']);
    Route::get('/orders/{userId}/{orderId}', [OrderController::class, 'show']);
    Route::put('/orders/{userId}/{orderId}/cancel', [OrderController::class, 'cancel']);

    Route::post('/orders/{userId}/{orderId}/pay', [PaymentController::class, 'pay']);
    Route::post('/orders/{userId}/{orderId}/capture', [PaymentController::class, 'capture']);
});