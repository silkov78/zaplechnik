<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitStoreRequest;
use App\Http\Resources\StoreVisitResource;
use App\Models\Campground;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitsController extends Controller
{
    /**
     * Stores new visit record.
     * Each visit has unique pair of user_id and campground_id.
     *
     * It's possible to pass in campground_di such values as "2", 8.0, "12.0".
     */
    public function store(VisitStoreRequest $request): StoreVisitResource|JsonResponse
    {
        $data = $request->validated();

        if (!Campground::where('campground_id', $data['campground_id'])->exists()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'campground_id' => [
                        [
                            'code' => 'exists',
                            'message' => 'Campground with provided campground_id does not exist.',
                        ]
                    ],
                ],
            ], 400);
        }

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
        $data = $request->validate([
            'campground_id' => 'required|decimal:0|gt:0'
        ]);
        return response()->json([
            'message' => 'User successfully deleted a visit.',
        ]);
    }
}
