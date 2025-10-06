<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
    Storage::put('avatars/old-avatar.png', 'content');

    $this->userWithoutAvatar = User::factory()->create();

    $this->userWithAvatar = User::factory()->create([
        'avatar' => 'old-avatar.png',
    ]);
});

describe('profile: destroy', function () {
    it('deletes user successfully (without avatar)', function () {
        $this->actingAs($this->userWithoutAvatar);

        $this->delete('/api/v1/me')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message', 'info' => ['user_id']
            ])
            ->assertJsonFragment([
                'user_id' => $this->userWithoutAvatar->user_id,
            ]);

        $this->assertDatabaseMissing('users', [
            'user_id' => $this->userWithoutAvatar->user_id,
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $this->userWithoutAvatar->user_id,
        ]);
    });

    it('deletes user successfully (with avatar)', function () {
        $this->actingAs($this->userWithAvatar);

        $this->delete('/api/v1/me')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message', 'info' => ['user_id']
            ])
            ->assertJsonFragment([
                'user_id' => $this->userWithAvatar->user_id,
            ]);

        $this->assertDatabaseMissing('users', [
            'user_id' => $this->userWithAvatar->user_id,
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $this->userWithAvatar->user_id,
        ]);

        Storage::assertMissing('avatars/old-avatar.png');
    });

    it('rejects not-authenticated user', function () {
        $this->delete('/api/v1/me')->assertUnauthorized();
    });

    it('rejects requests by strict rate limit (10)', function () {
        $this->actingAs($this->userWithAvatar);

        for ($i = 0; $i < 10; $i++) {
            $response = $this->delete('/api/v1/me');
            expect($response->status())->not()->toBe(429);
        }

        $response = $this->delete('/api/v1/me');

        $response->assertStatus(429)
            ->assertJsonStructure(['message', 'errors' => ['rate-limit' => ['code', 'message']]])
            ->assertJsonFragment(['code' => 'rate-limit']);
    });
});
