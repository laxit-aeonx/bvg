<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1
|--------------------------------------------------------------------------
|
| Version 1 of Backend APIs
|
*/

Route::controller(\App\Http\Controllers\Api\V1\RoleController::class)->group(function () {
    Route::get('/role', 'list');
    Route::get('/role/all', 'all');
});

Route::controller(\App\Http\Controllers\Api\V1\ProjectController::class)->group(function () {
    Route::get('/project', 'list');
    Route::get('/project/{project}', 'details');
});
