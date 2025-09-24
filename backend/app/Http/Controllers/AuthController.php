<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'message' => 'User successfully registered.',
            'user' => $user->only('user_id', 'name', 'email'),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'code' => 'exists',
                    'email' => 'Invalid email or password.',
                ]
            ], 400);
        }

        $token = $user->createToken(
            'auth_token', ['*'], now()->addHours(1)
        )->plainTextToken;

        return response()->json([
            'message' => 'User successfully logged in.',
            'info' => [
                'token' => $token,
                'user_id' => $user->user_id,
                'expires_in' => 3600,
            ],
        ], 201);
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
