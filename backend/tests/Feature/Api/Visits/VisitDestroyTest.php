<?php

use App\Models\Campground;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->userCamp = Campground::factory()->create();
    $this->notUserCamp = Campground::factory()->create();

    $this->visit = Visit::create([
        'user_id' => $this->user->user_id,
        'campground_id' => $this->userCamp->campground_id,
    ]);
});

describe('visits: destroy', function () {
    it('destroys a visit successfully', function () {
        Sanctum::actingAs($this->user);

        $response = $this->delete('/api/v1/visits', [
            'campground_id' => $this->userCamp->campground_id,
        ]);

        $this->assertDatabaseMissing('visits', [
            'user_id' => $this->user->user_id,
            'campground_id' => $this->userCamp->campground_id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User successfully deleted a visit.',
                'info' => [
                    'visit_id' => $this->visit->visit_id,
                    'user_id' => $this->visit->user_id,
                ],
            ]);
    });

    it('rejects not authenticated user', function () {
        $response = $this->delete('/api/v1/visits');
        $response->assertStatus(401);
    });

    it('rejects requests by medium rate limit (40)', function () {
        $this->actingAs($this->user);

        for ($i = 0; $i < 40; $i++) {
            $response = $this->delete('/api/v1/visits');
            expect($response->status())->not()->toBe(429);
        }

        $response = $this->delete('/api/v1/visits');

        $response->assertStatus(429)
            ->assertJsonStructure(['message', 'errors' => ['rate-limit' => ['code', 'message']]])
            ->assertJsonFragment(['code' => 'rate-limit']);
    });

    it('rejects empty query', function () {
        Sanctum::actingAs($this->user);

        $response = $this->delete('/api/v1/visits');
        $response->assertStatus(422);
    });

    it('rejects invalid campground_id (empty)', function () {
        Sanctum::actingAs($this->user);

        $response = $this->delete('/api/v1/visits', [
            'campground_id' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['campground_id']])
            ->assertJsonFragment(['code' => 'required']);
    });

    /**
     * Test allows to pass such values: 2.0, '2', '3.0'.
     */
    it('rejects invalid campground_id (not integer)', function ($invalidCampgroundId) {
        Sanctum::actingAs($this->user);

        $response = $this->delete('/api/v1/visits', [
            'campground_id' => $invalidCampgroundId,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['campground_id']])
            ->assertJsonFragment(['code' => 'decimal']);
    })->with(['twenty-four', 3.2]);

    it('rejects invalid campground_id (less 1)', function ($invalidCampgroundId) {
        Sanctum::actingAs($this->user);

        $response = $this->delete('/api/v1/visits', [
            'campground_id' => $invalidCampgroundId,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['campground_id']])
            ->assertJsonFragment(['code' => 'gt']);
    })->with([0, -2]);

    it('rejects not-existing campground_id', function () {
        Sanctum::actingAs($this->user);

        $response = $this->delete('/api/v1/visits', [
            'campground_id' => 999999,
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
                'errors' => ['campground_id' => [['code', 'message']]]
            ])
            ->assertJsonFragment(['code' => 'exists']);
    });

    it('rejects campground_id not associated with current user', function () {
        Sanctum::actingAs($this->user);

        $response = $this->delete('/api/v1/visits', [
            'campground_id' => $this->notUserCamp->campground_id,
            'user_id' => $this->user->user_id,
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
                'errors' => ['campground_id' => [['code', 'message']]]
            ])
            ->assertJsonFragment(['code' => 'exists']);
    });
});
