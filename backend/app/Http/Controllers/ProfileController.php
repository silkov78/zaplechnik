<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileShowResource;
use App\Models\User;
use Illuminate\Http\UploadedFile;
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
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

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

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->handleAvatarUpload(
                $user, $request->file('avatar')
            );
        }

        $user->update($data);

        return response()->json([
            'data' => $this->formatUserData($user, $data),
        ]);
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

    protected function handleAvatarUpload(User $user, UploadedFile $avatarFile): string
    {
        if ($user->avatar && Storage::exists('avatars/' . $user->avatar)) {
            Storage::delete('avatars/' . $user->avatar);
        }

        $avatarFileName = $avatarFile->hashName();
        $avatarFile->storeAs('avatars', $avatarFileName);

        return $avatarFileName;
    }

    protected function formatUserData(User $user, array $data): array
    {
        if (isset($data['avatar'])) {
            $data['avatarUrl'] = $user->avatarUrl;
            unset($data['avatar']);
        }

        return $data;
    }
}
