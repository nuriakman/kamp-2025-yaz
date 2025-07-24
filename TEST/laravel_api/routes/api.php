<?php

// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

// Kategori ve Ürünler için API kaynak rotaları
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);

