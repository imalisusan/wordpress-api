<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerDetailsController;
use App\Http\Controllers\PaymentDetailsController;
use App\Http\Controllers\OrderDetailsController;


Route::post('/customer-details', [CustomerDetailsController::class, 'store']);
Route::get('/customer-details', [CustomerDetailsController::class, 'index']);
Route::post('/payment-details', [PaymentDetailsController::class, 'store']);
Route::get('/payment-details', [PaymentDetailsController::class, 'index']);
Route::post('/order-details', [OrderDetailsController::class, 'store']);
Route::get('/order-details', [OrderDetailsController::class, 'index']);
