<?php

use App\Models\Campground;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    Campground::factory(3)->create();
    $this->campArray = Campground::all()->toArray();
});

describe('visits', function () {
    it('rejects not authenticated user', function () {
        $response = $this->postJson('/api/v1/visits');
        $response->assertStatus(401);
    });

    it('rejects invalid campground_id', function ($invalidCampgroundId) {
        Sanctum::actingAs($this->user);

        $data = [
            'campground_id' => $invalidCampgroundId,
            'visit_date' => '2025-01-01',
        ];

        $response = $this->postJson('/api/v1/visits', $data);

        dump($response->json());

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'campground_id' => [
                        'code',
                        'message',
                    ],
                ],
            ]);
    })->with([
        'empty campground_id' => '',
        'string campground_id' => 'fdsfsadf',
        'float campground_id' => 2.0,
        'negative campground_id' => -2,
        'zero campground_id' => 0,
    ]);
});
