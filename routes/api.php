<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\ComparisonShareController;

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

// Category routes
Route::apiResource('categories', CategoryController::class);

Route::post('/share-comparison', [ComparisonShareController::class, 'shareViaEmail']);
