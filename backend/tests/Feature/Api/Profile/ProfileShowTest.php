<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'testUser',
    ]);
});

describe('profile: store', function () {
    it('rejects not-authenticated user', function () {
        $this->getJson('/api/v1/me')->assertStatus(401);
    });

    it('shows profile data successfully', function () {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user_id', 'name', 'rank', 'email',
                    'avatarUrl', 'gender', 'telegram', 'bio',
                    'info' => ['visits_count', 'created_at'],
                ]
            ])
            ->assertJsonFragment(['name' => $this->user->name]);
    });
});
