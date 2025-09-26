<?php

use App\Models\Campground;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Campground::factory(3)->create();

    $this->firstCampground = Campground::first();

    $this->visit = Visit::create([
        'user_id' => $this->user->user_id,
        'campground_id' => $this->firstCampground->campground_id,
    ]);
});

describe('visits: destroy', function () {
    it('rejects not authenticated user', function () {
        $response = $this->delete('/api/v1/visits');
        $response->assertStatus(401);
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

        $response->assertStatus(400)
            ->assertJsonStructure(['message', 'errors' => ['campground_id']])
            ->assertJsonFragment(['code' => 'exists']);;
    });
});
