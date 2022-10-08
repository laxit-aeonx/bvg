<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Role\RoleResource;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function list()
    {
        return RoleResource::collection(Role::all());
    }
}
