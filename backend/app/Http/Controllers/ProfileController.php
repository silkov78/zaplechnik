<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request): ProfileResource
    {
        return new ProfileResource($request->user());
    }

    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::exists('avatars/' . $user->avatar)) {
                Storage::delete('avatars/' . $user->avatar);
            }

            $avatarFile = $request->file('avatar');
            $avatarFileName = $avatarFile->hashName();
            $avatarFile->storeAs('avatars', $avatarFileName);

            $data['avatar'] = $avatarFileName;
        }

        $request->user()->update($data);

        if (isset($data['avatar'])) {
            unset($data['avatar']);
            $data['avatarUrl'] = $user->avatarUrl;
        }

        return response()->json(['data' => $data]);
    }

    public function destroy(Request $request): JsonResponse
    {
        // TODO: config deleting avatar in laravel storage
        $user = $request->user();
        $userId = $user->user_id;

        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'message' => 'User successfully deleted an account.',
            'info' => ['user_id' => $userId],
        ]);
    }
}
