<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TestDataController;
use App\Http\Controllers\Api\AuthController;

// Authentication routes
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'userProfile']);
});

// Protected routes
Route::group([
    'middleware' => 'auth:api'
], function () {
    // Kategori ve Ürünler için API kaynak rotaları
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    
    // Test verileri oluşturmak için rotası
    Route::post('/test-data', [TestDataController::class, 'createTestData']);
});
