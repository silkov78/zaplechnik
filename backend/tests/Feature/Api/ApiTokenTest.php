<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->currentUser = User::factory()->create([
        'name' => 'testUser',
        'email' => 'testUser@test.com',
    ]);

    $this->anotherUser = User::factory()->create();

    $this->securedEndpoint = '/api/v1/me';
});

describe('api token', function () {
    it('messages about missing token', function () {
        $response = $this->get($this->securedEndpoint);

        $response
            ->assertStatus(401)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'token' => [[
                        'code',
                        'message',
                    ]],
                ],
            ])
            ->assertJsonFragment(['code' => 'missing']);
    });

    it('messages about invalid token', function () {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalidToken',
        ])->get($this->securedEndpoint);

        $response
            ->assertStatus(401)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'token' => [[
                        'code',
                        'message',
                    ]],
                ],
            ])
            ->assertJsonFragment(['code' => 'invalid']);
    });

    it('messages about expired token', function () {
        $expiredToken = $this->currentUser->createToken(
            'expiredToken', ['*'], now()->subDays(1)
        )->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $expiredToken,
        ])->get($this->securedEndpoint);

        $response
            ->assertStatus(401)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'token' => [[
                        'code',
                        'message',
                    ]],
                ],
            ])
            ->assertJsonFragment(['code' => 'expired']);
    });
});

