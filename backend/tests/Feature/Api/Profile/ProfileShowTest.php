<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'testUser',
    ]);
});

describe('profile: show', function () {
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

    it('returns default avatarUrl', function () {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(200);

        $this->assertStringContainsString(
            'images/default-avatar.png', $response['data']['avatarUrl']
        );
    });

    it('rejects not-authenticated user', function () {
        $this->getJson('/api/v1/me')->assertStatus(401);
    });

    it('rejects requests by medium rate limit (40)', function () {
        $this->actingAs($this->user);

        for ($i = 0; $i < 40; $i++) {
            $response = $this->getJson('/api/v1/me');
            expect($response->status())->not()->toBe(429);
        }

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(429)
            ->assertJsonStructure(['message', 'errors' => ['rate-limit' => ['code', 'message']]])
            ->assertJsonFragment(['code' => 'rate-limit']);
    });
});
