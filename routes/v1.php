<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Api\V1\RoleController::class)->group(function () {
    Route::get('/role', 'list');
    Route::get('/role/all', 'all');
});
