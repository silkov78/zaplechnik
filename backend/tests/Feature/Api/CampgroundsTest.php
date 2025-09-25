<?php

use App\Models\Campground;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

describe('campgrounds', function () {
    it('returns campgrounds feature collection', function () {
        Campground::factory(10)->create();

        $response = $this->getJson('/api/v1/campgrounds');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'type',
                'features' => [
                    [
                        'type',
                        'properties' => [],
                        'geometry' => [
                            'type',
                            'coordinates' => [],
                        ],
                    ]
                ],
            ])
            ->assertJsonFragment([
                'type' => 'FeatureCollection',
            ]);
    });

    it('returns campgrounds visited by user', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Campground::factory()->create();

        Visit::factory()->create([
            'user_id' => $user->user_id,
            'campground_id' => 13,
        ]);

        $response = $this->getJson('/api/v1/me/visited-campgrounds');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'type',
                'features' => [
                    [
                        'type',
                        'properties' => [],
                        'geometry' => [
                            'type',
                            'coordinates' => [],
                        ],
                    ]
                ],
            ])
            ->assertJsonFragment([
                'type' => 'FeatureCollection',
            ]);
    });
});
