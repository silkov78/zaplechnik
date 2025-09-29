<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileShowResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Shows profile data of authenticated user.
     */
    public function show(Request $request): ProfileShowResource
    {
        return new ProfileShowResource($request->user());
    }

    /**
     * Updates user's data. Handles attached avatar photo.
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'string|max:50|min:1',
            'email' => 'string|email|max:255',
//            'avatar' =>
            'telegram' => 'string|starts_with:@|max:100|min:2',
        ]);

        if (!$data) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'fields' => [
                        [
                            'code' => 'empty',
                            'message' => 'At least one field must be provided.',
                        ]
                    ],
                ],
            ], 422);
        }

        if (isset($data['name'])) {
            if (User::where('name', $data['name'])->exists()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'name' => [
                            [
                                'code' => 'unique',
                                'message' => 'The :attribute has already been taken.',
                            ]
                        ],
                    ],
                ], 400);
            }
        }

        if (isset($data['email'])) {
            if ($data['email'] && User::where('email', $data['email'])->exists()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'email' => [
                            [
                                'code' => 'unique',
                                'message' => 'The :attribute has already been taken.',
                            ]
                        ],
                    ],
                ], 400);
            }
        }

        return response()->json(['how']);
//        $data = $request->validated();
//        $user = $request->user();
//
//        if ($request->hasFile('avatar')) {
//            if ($user->avatar && Storage::exists('avatars/' . $user->avatar)) {
//                Storage::delete('avatars/' . $user->avatar);
//            }
//
//            $avatarFile = $request->file('avatar');
//            $avatarFileName = $avatarFile->hashName();
//            $avatarFile->storeAs('avatars', $avatarFileName);
//
//            $data['avatar'] = $avatarFileName;
//        }
//
//        $request->user()->update($data);
//
//        if (isset($data['avatar'])) {
//            unset($data['avatar']);
//            $data['avatarUrl'] = $user->avatarUrl;
//        }
//
//        return response()->json(['data' => $data]);
    }

    /**
     * Deletes user's data (account). Delete avatar photo from storage.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = $user->user_id;

        if ($user->avatar && Storage::exists('avatars/' . $user->avatar)) {
            Storage::delete('avatars/' . $user->avatar);
        }

        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'message' => 'User successfully deleted an account.',
            'info' => ['user_id' => $userId],
        ]);
    }
}
