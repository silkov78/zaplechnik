<?php

namespace App\Http\Controllers;

use App\Http\Resources\StoreVisitResource;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitsController extends Controller
{
    /**
     * Stores new visit record.
     * Each visit has unique pair of user_id and campground_id.
     */
    public function store(Request $request): StoreVisitResource
    {
        $data = $request->validate([
            'campground_id' => 'required|integer|exists:campgrounds',
            'visit_date' => 'date:Y-m-d',
        ]);

        $data['user_id'] = $request->user()->user_id;

        $visit = Visit::create($data);

        return new StoreVisitResource($visit);
    }

    /**
     * Destroys visit record.
     * Accepts required campground_id from query string.
     */
    public function destroy(Request $request): JsonResponse
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
