<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'testUser',
    ]);
});

describe('profile: update', function () {
    it('rejects not-authenticated user', function () {
        $this->getJson('/api/v1/me')->assertStatus(401);
    });

    it('rejects empty body', function () {
        $this->actingAs($this->user);

        $this->patch('/api/v1/me', [])
            ->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'At least one field must be provided.',
            ]);
    });
});
