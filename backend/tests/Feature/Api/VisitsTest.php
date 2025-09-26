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
});
