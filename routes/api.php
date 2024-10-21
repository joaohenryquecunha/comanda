<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

Route::apiResource('product', ProductController::class);
Route::apiResource('order', OrderController::class);
