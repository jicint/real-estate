<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\ComparisonShareController;
use App\Http\Controllers\ComparisonController;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Property routes
Route::apiResource('properties', PropertyController::class);
Route::get('properties/featured', [PropertyController::class, 'featured']);
Route::post('/properties/details', [PropertyController::class, 'getPropertyDetails']);
Route::get('/properties/shared', [PropertyController::class, 'getSharedProperties']);
Route::get('/properties', [PropertyController::class, 'index']);

// Category routes
Route::apiResource('categories', CategoryController::class);

Route::post('/share-comparison', [ComparisonShareController::class, 'shareViaEmail']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/comparisons', [ComparisonController::class, 'store']);
    Route::get('/comparisons', [ComparisonController::class, 'index']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Add a test route to verify routing is working
Route::get('/test-log', function() {
    Log::info('Test route accessed');
    return 'Test log written';
});
