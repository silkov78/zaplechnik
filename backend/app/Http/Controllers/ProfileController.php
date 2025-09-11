<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesGeoJsonArray;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Campground;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use GeneratesGeoJsonArray;

    public function show(Request $request): ProfileResource
    {
        return new ProfileResource($request->user());
    }

    /**
     * Updates user's data
     * Handles attached avatar photo
     */
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

    /**
     * Deletes user's data (account)
     * Delete avatar photo from storage
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

    /**
     * Returns campgrounds visited by authenticated user
     */
    public function visitedCampgrounds(Request $request): JsonResponse
    {
        $visitedCampgroundsArray = $this->getFeatureCollectionArray(
            $request->user()->campgrounds, 'osm_geometry'
        );

        return response()->json($visitedCampgroundsArray);
    }
}
