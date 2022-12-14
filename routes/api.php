<?php

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

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'v1'], function () {

    include 'v1.php'; // API Routes : v1

    Route::controller(\App\Http\Controllers\Api\AuthController::class)->group(function () {
        Route::post('/logout', 'logout');
    }); // Protected API Logout route

});

Route::controller(\App\Http\Controllers\Api\AuthController::class)->group(function () {
    Route::post('/login', 'login');
});

Route::fallback(function () {
    return response()->json([
        'error' => 'Resource Not Found ㄟ( ▔, ▔ )ㄏ ',
        'message' => 'Make sure you have entered correct Route|URL'
    ], 404);
});
