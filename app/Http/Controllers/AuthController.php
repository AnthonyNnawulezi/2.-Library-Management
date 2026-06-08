<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private const TOKEN_NAME = 'auth_token';

    public function register(RegisterRequest $request): JsonResponse
    {

        $validated = $request->validated();

        //Explicitly hash the password before database insertion, the method in 1. library is correct too
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $token = $user->createToken(self::TOKEN_NAME)->plainTextToken;

        return response()->json([
            'success' => true,
            'user'    => new UserResource($user),
            'token'   => $token,
        ], 210);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Incorrect Credentials',
            ], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken(self::TOKEN_NAME)->plainTextToken;

        return response()->json([
            'success' => true,
            'user'    => new UserResource($user),
            'token'   => $token,
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.'
        ], 200);
    }
}
