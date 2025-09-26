<?php

use App\Models\Campground;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

// TODO: Read about intersection tests

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->campground = Campground::factory()->create();
});

describe('visits: store', function () {
    it('creates visit successfully', function () {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/visits', [
            'campground_id' => $this->campground->campground_id,
            'user_id' => $this->user->user_id,
            'visit_date' => '2025-01-01',
        ]);

        $this->assertDatabaseHas('visits', [
            'campground_id' => $this->campground->campground_id,
            'user_id' => $this->user->user_id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                    'data' => [
                        'info' => [
                            'user_id', 'campground_id', 'visit_date',
                        ]
                    ]]
            );
    });

    it('creates visit successfully without date', function () {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/visits', [
            'campground_id' => $this->campground->campground_id,
            'user_id' => $this->user->user_id,
        ]);

        $this->assertDatabaseHas('visits', [
            'campground_id' => $this->campground->campground_id,
            'user_id' => $this->user->user_id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'info' => [
                        'user_id', 'campground_id', 'visit_date',
                    ]
                ]]
            );
    });

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
            ->assertJsonStructure(['message', 'errors' => ['campground_id']]);
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
            ->assertJsonStructure(['message', 'errors' => ['campground_id']]);;
    });

    /*
     * visit_date
     */
    it('rejects invalid visit_date (incorrect date_format)', function ($invalidVisitDate) {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/visits', [
            'campground_id' => $this->campground->campground_id,
            'visit_date' => $invalidVisitDate,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['visit_date']])
            ->assertJsonFragment(['code' => 'date_format']);
    })->with([
        'incorrect date_format' => [[
            'twenty-five', 2021, '2021', '01.01.2021', '2025-01-01T00:00:00'
        ]]
    ]);

    it('rejects invalid visit_date (before 1924-12-31)', function () {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/visits', [
            'campground_id' => $this->campground->campground_id,
            'visit_date' => '1900-01-01',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['visit_date']])
            ->assertJsonFragment(['code' => 'after']);
    });

    it('rejects invalid visit_date (after today)', function () {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/visits', [
            'campground_id' => $this->campground->campground_id,
            'visit_date' => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['visit_date']])
            ->assertJsonFragment(['code' => 'before_or_equal']);
    });
});
