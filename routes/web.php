<?php


use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Response::json([
        '🚀' => 'Build Something Amazing'
    ], 200); // Status code here
});

Route::fallback(function () {
    return response()->json([
        'error' => 'URL Not Found (￣﹏￣；) ',
        'message' => 'Incase You are looking for API endpoint, kindly add /api after domain name'
    ], 404);
});
