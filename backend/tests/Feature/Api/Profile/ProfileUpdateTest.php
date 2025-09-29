<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'testUser',
    ]);
});

describe('profile: update', function () {
    it('rejects not-authenticated user', function () {
        $this->getJson('/api/v1/me')->assertStatus(401);
    });

    it('rejects empty body', function () {
        $this->actingAs($this->user);

        $this->patch('/api/v1/me', [])
            ->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'At least one field must be provided.',
            ]);
    });

    it('rejects invalid name', function ($invalidParam) {
        $this->actingAs($this->user);

        $response = $this->patch('/api/v1/me', ['name' => $invalidParam]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['name' => [['code', 'message']]]]);
    })->with([
        'empty string' => '',
        'integer' => 24,
        'boolean' => true,
        'length > 50' => str_repeat('Ababab', 10),
    ]);

    it('rejects not-unique name', function () {
        $this->actingAs($this->user);

        $response = $this->patch('/api/v1/me', ['name' => $this->user->name]);

        $response->assertStatus(400)
            ->assertJsonStructure([
                'message',
                'errors' => ['name' => [['code', 'message']]]
            ])
            ->assertJsonFragment(['code' => 'unique']);
    });
});
