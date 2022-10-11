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

Route::controller(\App\Http\Controllers\Api\V1\UserController::class)->group(function () {
    Route::get('/user', 'list');
});

Route::controller(\App\Http\Controllers\Api\V1\RoleController::class)->group(function () {
    Route::get('/role', 'list');
});

Route::controller(\App\Http\Controllers\Api\V1\ProjectController::class)->group(function () {
    Route::get('/project', 'list');
    Route::get('/project/{project}', 'details');
    Route::post('/project/create', 'create');
    Route::delete('/project/{project}', 'delete');
});
