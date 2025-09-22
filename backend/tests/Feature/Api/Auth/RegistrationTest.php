<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::table('users')->delete();
});

describe('registration', function () {
    it('creates user successfully', function () {
        $userData = [
            'name' => 'petya',
            'email' => 'petya@example.com',
            'password' => 'Password4',
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $this->assertDatabaseHas('users', [
            'name' => 'petya',
            'email' => 'petya@example.com'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['user_id', 'name', 'email'],
            ])
            ->assertJsonFragment([
                'message' => 'User successfully registered'
            ]);
    });

    it('rejects empty credentials', function ($invalidData) {
        $response = $this->postJson('/api/v1/register', $invalidData);

        $response->assertStatus(400)
            ->assertJsonStructure([
                'message',
                'errors' => [],
            ])
            ->assertJsonFragment([
                'message' => 'Invalid request',
            ]);
    })->with([
        'missing credentials' => [[]],
        'missing name' => [['email' => 'piotr@example.com', 'password' => 'Silkov78']],
        'missing email' => [['name' => 'silkov78', 'password' => 'Silkov78']],
        'missing password' => [['name' => 'silkov78', 'password' => 'Silkov78']],
        'empty credentials' => [['name' => '', 'email' => '', 'password' => '']],
    ]);

    it('rejects invalid name', function ($invalidParam) {
        User::factory()->create([
            'name' => 'testUser',
        ]);

        $userData = [
            'name' => $invalidParam,
            'email' => 'piotr@example.com',
            'password' => 'Silkov78'
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(400)
            ->assertJsonStructure([
                'message',
                'errors' => ['name'],
            ])
            ->assertJsonFragment([
                'message' => 'Invalid request',
            ]);
    })->with([
        'empty name' => '',
        'not string name' => 1,
        '> 50 symbols name' => str_repeat('A', 51),
        'existing name' => 'testUser',
    ]);

    it('rejects invalid email', function ($invalidParam) {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $userData = [
            'name' => 'testUser',
            'email' => $invalidParam,
            'password' => 'Silkov78'
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(400)
            ->assertJsonStructure([
                'message',
                'errors' => ['email'],
            ])
            ->assertJsonFragment([
                'message' => 'Invalid request',
            ]);
    })->with([
        'empty email' => '',
        'not string email' => 1,
        '> 255 symbols email' => str_repeat('A', 260) . '@example.com',
        'not email string' => 'testUser.com',
        'existing email' => 'test@example.com',
    ]);

    it('rejects invalid password', function ($invalidParam) {
        $userData = [
            'name' => 'testUser',
            'email' => 'test@example.com',
            'password' => $invalidParam,
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        // TODO: remove square brackets and refactor associated endpoint message
        $response->assertStatus(400)->assertJson([
            'message' => 'Invalid request',
            'errors' => [
                'password' => ['Parameter “password” is required. ' .
                    'It must be a string of more than 8 characters, but no more than 255 characters. ' .
                    'It must contain uppercase and lowercase letters of the Latin alphabet and at least one digit',
            ]],
        ]);
    })->with([
        'empty password' => '',
        'not string password' => 1,
        '< 8 symbols password' => 'Pass5',
        '> 255 symbols password' => str_repeat('A', 260) . 'Pass5',
        'doesnt contain numbers' => 'Password',
        'doesnt contain letters' => '123456789',
        'doesnt contain upper case' => 'password123',
        'doesnt contain lower case' => 'PASSWORD123',
    ]);
});
