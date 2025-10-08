<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitStoreRequest;
use App\Http\Resources\StoreVisitResource;
use App\Http\Responses\ErrorResponse;
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
    public function store(VisitStoreRequest $request): StoreVisitResource|ErrorResponse
    {
        $data = $request->validated();

        if (!Campground::where('campground_id', $data['campground_id'])->exists()) {
            return new ErrorResponse(
                'campground_id',
                'exists',
                'Campground with provided campground_id does not exist.',
                400
            );
        }

        $data['user_id'] = $request->user()->user_id;

        $visit = Visit::create($data);

        return new StoreVisitResource($visit);
    }

    /**
     * Destroys visit record.
     * Accepts required campground_id from query string.
     */
    public function destroy(Request $request): JsonResponse|ErrorResponse
    {
        $data = $request->validate([
            'campground_id' => 'required|decimal:0|gt:0'
        ]);

        $user = auth()->user();

        $visit = Visit::where([
            'campground_id' => $data['campground_id'],
            'user_id' => $user->user_id,
        ])->first();

        if (!$visit) {
            return new ErrorResponse(
                'campground_id',
                'exists',
                'Visit with provided campground_id and user_id does not exist.',
                404
            );
        }

        $visit->delete();

        return response()->json([
            'message' => 'User successfully deleted a visit.',
            'info' => [
                'user_id' => $visit->user_id,
                'visit_id' => $visit->visit_id,
            ],
        ]);
    }
}
