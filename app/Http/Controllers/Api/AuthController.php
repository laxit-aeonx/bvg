<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\{LoginRequest, LogoutRequest};

class AuthController extends Controller
{
    function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['These credentials do not match our records.']
            ], 403);
        }

        $token = $user->createToken('login-token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    function logout(LogoutRequest $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => ['Logout Successful']
        ], 200);
    }
}
