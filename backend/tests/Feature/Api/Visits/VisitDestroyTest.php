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

describe('visits: destroy', function () {
    it('rejects not authenticated user', function () {
        $response = $this->postJson('/api/v1/visits');
        $response->assertStatus(401);
    });
});
