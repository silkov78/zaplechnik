<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesGeoJsonArray;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\StoreVisitResource;
use App\Models\Visit;
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

    /**
     * Stores new visit record
     * Each visit has unique pair of user_id and campground_id
     */
    public function storeVisit(Request $request): StoreVisitResource|JsonResponse
    {
        $data = $request->validate([
            'campground_id' => 'required|integer|exists:campgrounds',
            'visit_date' => 'date',
        ]);

        $data['user_id'] = $request->user()->user_id;

        $visit = Visit::create($data);

        return new StoreVisitResource($visit);
    }

    /**
     * Destroys visit record
     * Accepts required campground_id from query string
     */
    public function destroyVisit(Request $request): JsonResponse
    {
        $userId = $request->user()->user_id;
        $query = $request->validate([
            'campground_id' => 'required|integer|exists:campgrounds',
        ]);

        $visit = Visit::where([
            'user_id' => $userId,
            'campground_id' => $query['campground_id'],
        ]);

        if (!$visit->exists()) {
            return response()->json([
                'message' => 'Visit with provided user_id and campground_id not found.',
            ]);
        }

        $visit->delete();

        return response()->json([
            'message' => 'User successfully deleted a visit',
            'info' => [
                'user_id' => $userId,
                'campground_id' => $query['campground_id'],
            ],
        ]);
    }
}
