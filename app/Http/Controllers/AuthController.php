<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        private const TOKEN_NAME = 'auth_token';

        $validated = $request->validated();

        //Explicitly hash the password before database insertion
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $token = $user->createToken(TOKEN_NAME)->plainTextToken;

       return response()->json([
            'success' => true,
            'user'    => new UserResource($user),
            'token'   => $token,
        ], 210)
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:12|max:16',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'Message' => 'Incorrect Credentials',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'Success' => true,
            'User' => new UserResource($user),
            'Token' => $token,
        ]);
    }
}
