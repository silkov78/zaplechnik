<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'name' => $this->name,
            'rank' => $this->getRank(),
            'email' => $this->email,
            'avatarUrl' => $this->avatarUrl,
            'gender' => $this->gender?->value,
            'telegram' => $this->telegram,
            'bio' => $this->bio,
            'info' => [
                'visits_count' => $this->visits_count,
                'created_at' => $this->created_at,
            ],
        ];
    }
}
