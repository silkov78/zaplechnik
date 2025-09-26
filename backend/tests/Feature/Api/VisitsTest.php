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

    /**
     * Test allows to pass such values: 2.0, '2', '3.0'.
     */
    it('rejects creation of new visit with invalid campground_id', function ($invalidCampgroundId) {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/visits', [
            'campground_id' => $invalidCampgroundId,
            'visit_date' => '2025-01-01',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'campground_id' => [
                        [
                            'code',
                            'message',
                        ]
                    ],
                ],
            ])
            ->assertJsonFragment([
                'message' => 'The given data was invalid.',
            ]);
    })->with([
        'empty campground_id' => '',
        'string campground_id' => 'twenty-five',
        'negative campground_id' => -2,
        'zero campground_id' => 0,
        'float (not like 2.0)' => 3.2,
    ]);
});
