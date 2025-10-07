<?php

use App\Models\Campground;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();

    $this->user = User::factory()->create();

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

        $this->assertTrue(Cache::has('campgrounds_geojson'));

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

    it('clears cache after creating new campground', function () {
        $this->getJson('/api/v1/campgrounds');

        $this->assertTrue(Cache::has('campgrounds_geojson'));

        Campground::factory()->create();

        $this->assertFalse(Cache::has('campgrounds_geojson'));
    });

    it('returns campgrounds visited by user', function () {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/campgrounds/visited');

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
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/campgrounds/visited');

        $response->assertStatus(200)
            ->assertJson([
                'type' => 'FeatureCollection',
                'features' => [],
            ]);
    });

    it('rejects not authenticated user', function () {
        $response = $this->getJson('/api/v1/campgrounds/visited');

        $response->assertStatus(401);
    });

    it('rejects requests by lite rate limit (100)', function () {
        $this->actingAs($this->user);

        for ($i = 0; $i < 100; $i++) {
            $response = $this->get('/api/v1/campgrounds');
            expect($response->status())->not()->toBe(429);
        }

        $response = $this->get('/api/v1/campgrounds');

        $response->assertStatus(429)
            ->assertJsonStructure(['message', 'errors' => ['rate-limit' => ['code', 'message']]])
            ->assertJsonFragment(['code' => 'rate-limit']);
    });

    it('rejects requests by medium limit (100) for visited', function () {
        $this->actingAs($this->user);

        for ($i = 0; $i < 40; $i++) {
            $response = $this->get('/api/v1/campgrounds/visited');
            expect($response->status())->not()->toBe(429);
        }

        $response = $this->get('/api/v1/campgrounds/visited');

        $response->assertStatus(429)
            ->assertJsonStructure(['message', 'errors' => ['rate-limit' => ['code', 'message']]])
            ->assertJsonFragment(['code' => 'rate-limit']);
    });
});
