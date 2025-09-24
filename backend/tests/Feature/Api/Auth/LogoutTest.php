<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

describe('logout', function () {
    it('returns correct message after logout', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'info' => ['user_id'],
            ])
            ->assertJsonFragment([
                'message' => 'User logged out successfully.',
            ]);
    });

    it('revokes current token from db', function () {
        $user = User::factory()->create();

        $token1 = $user->createToken('device1')->plainTextToken;
        $token2 = $user->createToken('device2')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
            ->postJson('/api/v1/logout');

        $response->assertStatus(200);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->user_id,
            'name' => 'device1',
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->user_id,
            'name' => 'device2',
        ]);
    });

    it('rejects not authenticated user', function () {
        $response = $this->postJson('/api/v1/logout');

        $response->assertStatus(401);
    });

    it('rejects user with invalid token', function () {
        $response = $this->withHeader('Authorization', 'Bearer: HakunaMatata')
            ->postJson('/api/v1/logout');

        $response->assertStatus(401);
    });
});
