<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

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

    it('rejects empty or partly empty credentials', function ($invalidData) {
        $response = $this->postJson('/api/v1/register', $invalidData);

        $response->assertStatus(400)->assertJsonFragment([
            'message' => 'Invalid request',
        ]);
    })->with([
        'missing credentials' => [[]],
        'missing name' => [['email' => 'piotr@example.com', 'password' => 'Silkov78']],
        'missing email' => [['name' => 'silkov78', 'password' => 'Silkov78']],
        'missing password' => [['name' => 'silkov78', 'password' => 'Silkov78']],
        'empty credentials' => [['name' => '', 'email' => '', 'password' => '']],
    ]);

    it('rejects invalid name', function ($invalidName) {
        User::factory()->create([
            'name' => 'testUser',
        ]);

        $userData = [
            'name' => $invalidName,
            'email' => 'piotr@example.com',
            'password' => 'Silkov78'
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        // TODO: remove square brackets and refactor associated endpoint message
        $response->assertStatus(400)->assertJson([
            'message' => 'Invalid request',
            'errors' => [
                'name' => ['Parameter “name” is required and unique. It must be a string and less than 50 characters'],
            ],
        ]);
    })->with([
        'empty name' => '',
        'not string name' => 1,
        '> 50 symbols name' => str_repeat('A', 51),
        'existing name' => 'testUser',
    ]);
    
});
