<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('registration process', function () {
    it('creates user successfully', function () {
        $userData = [
            'name' => 'petya',
            'email' => 'petya@example.com',
            'password' => 'Password4',
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User created successfully',
                'user' => [
                    'user_id' => 1,
                    'name' => 'petya',
                    'email' => 'petya@example.com'
                ]
            ]);
    });
});
