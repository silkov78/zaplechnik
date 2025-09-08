<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): ProfileResource
    {
        return new ProfileResource($request->user());
    }

    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $request->user()->update($data);

        return response()->json([
            'data' => $data,
        ]);
    }
}
