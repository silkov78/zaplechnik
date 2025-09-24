<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

describe('logout', function () {
    it('logs out successfully', function () {
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
});
