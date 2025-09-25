<?php

use App\Models\Campground;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('campgrounds', function () {
    it('returns geojson feature collection', function () {
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
});
