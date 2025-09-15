<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->only('user_id', 'name', 'email'),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid request',
                'errors' => [
                    'email' => 'Parameter “email” is required. The entered email does not exist',
                ]
            ], 400);
        }

        if (!Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid request',
                'errors' => 'Parameter “password” is required. Incorrect password entered',
            ], 400);
        }

        $token = $user->createToken(
            'auth_token', ['*'], now()->addHours(1)
        )->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request): JsonResponse
    {
        $userId = $request->user()->user_id;

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'User logged out successfully.',
            'info' => [
                'user_id' => $userId,
            ],
        ]);
    }
}
