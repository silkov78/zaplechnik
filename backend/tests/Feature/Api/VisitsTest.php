<?php

use App\Models\Campground;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->campground = Campground::factory()->create();
});

describe('visits', function () {
    it('rejects not authenticated user', function () {
        $response = $this->postJson('/api/v1/visits');
        $response->assertStatus(401);
    });

    /**
     * Test allows to pass such values: 2.0, '2', '3.0'.
     */
    it('rejects invalid campground_id', function ($invalidCampgroundId) {
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

    it('rejects not-existing campground_id', function () {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/visits', [
            'campground_id' => 999999,
            'visit_date' => '2025-01-01',
        ]);

        $response->assertStatus(400)
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
                'message' => 'Campground with provided campground_id does not exist.',
            ]);
    });

    it('rejects invalid visit_date', function ($invalidVisitDate) {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/visits', [
            'campground_id' => $this->campground->campground_id,
            'visit_date' => $invalidVisitDate,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'visit_date' => [
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
        'string except visit_date' => [['twenty-five']],
        'integer except visit_date' => [[2024]],
        'incorrect date format' => [['01.01.2021']],
        'datetime' => [['2025-01-01T00:00:00']],
        'before 1925' => [['1925-01-01']],
        'tomorrow' => [[Carbon::tomorrow()->format('Y-m-d')]],
    ]);
});
