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

        $user = User::where(['email' => $this->credentials['email']])->first();

        $response->assertStatus(200)->assertJsonFragment([
            'message' => 'User successfully login'
        ]);

        $response->assertJsonStructure([
            'message',
            'info' => [
                'token',
                'user_id',
                'expires_in',
            ]
        ]);

        expect($response->json()['info']['token'])->not()->toBeEmpty();
    });
});
