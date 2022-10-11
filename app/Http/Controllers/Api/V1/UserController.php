<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\User\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function list()
    {
        return UserResource::collection(User::all());
    }
}
