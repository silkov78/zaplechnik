<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::table('users')->delete();

    $this->credentials = [
        'email' => 'testUser@test.com',
        'password' => 'Password78',
    ];

    User::factory()->create([
        'name' => 'testUser',
        'email' => $this->credentials['email'],
        'password' => Hash::make($this->credentials['password']),
    ]);
});

describe('login', function () {
    it('logs in user successfully', function () {
        $response = $this->postJson('/api/v1/login', $this->credentials);

        $this->assertDatabaseHas('users', [
            'email' => $this->credentials['email'],
        ]);

        $userId = User::where('email', $this->credentials['email'])->first()->user_id;

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'info' => [
                    'token',
                    'user_id',
                    'expires_in',
                ],
            ])
            ->assertJsonFragment([
                'message' => 'User successfully logged in.',
            ]);

        expect($response->json()['info']['token'])->not()->toBeEmpty()
        ->and($response->json()['info']['user_id'])->toBe($userId);
    });

    it('rejects empty credentials', function ($emptyCredentials) {
        $response = $this->postJson('/api/v1/login', $emptyCredentials);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [],
            ])
            ->assertJsonFragment([
                'message' => 'The given data was invalid.',
            ]);
    })->with([
        'missing credentials' => [[]],
        'missing email' => [['password' => 'Silkov78']],
        'missing password' => [['email' => 'testUser@test.com']],
        'empty credentials' => [['email' => '', 'password' => '']],
    ]);

    it('rejects incorrect credentials', function ($invalidCredentials) {
        $response = $this->postJson('/api/v1/login', $invalidCredentials);

        $response->assertStatus(400)
            ->assertJsonStructure([
                'message',
                'errors' => [],
            ])
            ->assertJsonFragment([
                'message' => 'The given data was invalid.',
            ]);
    })->with([
        'non-existing password credentials' => [[
            'email' => 'hakuna@matata.com', 'password' => 'Hakunamatata78',
        ]],
        'existing email and incorrect password' => [[
            'email' => 'testUser@test.com',
            'password' => 'Hakunamatata78',
        ]],
    ]);
});
