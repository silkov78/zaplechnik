<?php

use App\Models\Campground;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);

    Campground::factory(10)->create();
    $this->fistCampground = Campground::first();

    $this->fistVisit = Visit::factory()->create([
        'user_id' => $this->user->user_id,
        'campground_id' => $this->fistCampground->campground_id,
    ]);
});

describe('campgrounds', function () {
    it('returns campgrounds feature collection', function () {
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
                'campground_id' => $this->fistCampground->campground_id,
                'osm_id' => $this->fistCampground->osm_id,
            ]);
    });

    it('returns campgrounds visited by user', function () {
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
                'campground_id' => $this->fistCampground->campground_id,
                'osm_id' => $this->fistCampground->osm_id,
            ]);
    });

    it('returns empty feature collection for home user', function () {
        $response = $this->getJson('/api/v1/me/visited-campgrounds');

        $response->assertStatus(200)
            ->assertJson([
                'type' => 'FeatureCollection',
                'features' => [],
            ]);
    });
});
