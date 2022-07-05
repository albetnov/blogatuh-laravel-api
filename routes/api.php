<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('blogs', BlogController::class)->scoped(['blog' => 'slug']);
    Route::apiResource('categories', CategoryController::class)->scoped(['category' => 'slug']);
});


Route::post("/login", [AuthController::class, 'login']);
