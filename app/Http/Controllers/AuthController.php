<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|password|string|max:16',
        ]);

        $user = User::create($validated);

        $token = $user->createToken('auth_token')->plainTextToken();

        return response()->json([
            'Success' => true,
            'User' => new UserResource($user),
            'Token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|password|string|max:16',
        ]);

        $user = User::where('email', $request->email);
        $password = User::check('password', decrypt($request->password));

        if (!$user && !$password) {
            return response()->json([
                'Message' => 'Incorrect Credentials',
            ]);
        }


        $user->create($validated);

        $token = $user->createToken('auth_token')->plainTextToken();

        return response()->json([
            'Success' => true,
            'User' => new UserResource($user),
            'Token' => $token,
        ]);
    }
}
