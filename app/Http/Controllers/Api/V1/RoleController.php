<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Role\RoleResource;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function list()
    {
        $roles = Role::all();
        return response($roles, 200);
    }

    public function all()
    {
        $roles = Role::all();
        return RoleResource::collection($roles);
    }
}
