<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreVisitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => 'User successfully made a visit.',
            'info' => [
                'user_id' => $this->user_id,
                'campground_id' => $this->campground_id,
                'visit_date' => $this->visit_date?->format('Y-m-d'),
            ],
        ];
    }
}
