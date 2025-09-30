<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'testUser',
        'email' => 'testUser@test.com',
    ]);

    $this->anotherUser = User::factory()->create();
});

describe('profile: update', function () {
    it('updates one field successfully', function () {
        $this->actingAs($this->user);

        $response = $this->patch('/api/v1/me', [
            'name' => 'hakunaMatata',
        ]);

        $this->assertDatabaseHas('users', [
            'user_id' => $this->user->user_id,
            'name' => 'hakunaMatata',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'hakunaMatata',
                ],
            ]);
    });

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

    it('rejects existing name (not current user)', function () {
        $this->actingAs($this->user);

        $response = $this->patch('/api/v1/me', ['name' => $this->anotherUser->name]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['name' => [['code', 'message']]]
            ])
            ->assertJsonFragment(['code' => 'unique']);
    });

    it('rejects invalid email', function ($invalidParam) {
        $this->actingAs($this->user);

        $response = $this->patch('/api/v1/me', ['email' => $invalidParam]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['email' => [['code', 'message']]]]);
    })->with([
        'empty email' => '',
        'integer' => 24,
        'not-email string' => 'hakuna.matata',
    ]);

    it('rejects existing email (not current user)', function () {
        $this->actingAs($this->user);

        $response = $this->patch('/api/v1/me', ['email' => $this->anotherUser->email]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email' => [['code', 'message']]]
            ])
            ->assertJsonFragment(['code' => 'unique']);
    });

    // TODO: test avatar field

    it('rejects invalid telegram', function ($invalidParam) {
        $this->actingAs($this->user);

        $response = $this->patch('/api/v1/me', ['telegram' => $invalidParam]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['telegram' => [['code', 'message']]]]);
    })->with([
        'empty' => '',
        'not string' => 24,
        'not-telegram string' => 'hakuna.matata',
    ]);

    it('rejects invalid bio', function ($invalidParam) {
        $this->actingAs($this->user);

        $response = $this->patch('/api/v1/me', ['bio' => $invalidParam]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['bio' => [['code', 'message']]]]);
    })->with([
        'empty' => '',
        'not string' => 24,
        'string > 255 symbols' => str_repeat('hakuna.matata', 40),
    ]);

    it('rejects invalid gender', function ($invalidParam) {
        $this->actingAs($this->user);

        $response = $this->patch('/api/v1/me', ['gender' => $invalidParam]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['gender' => [['code', 'message']]]])
            ->assertJsonFragment(['code' => 'enum']);
    })->with([
        'empty' => '',
        'not string' => 24,
        'not gender string' => 'boy',
    ]);

    it('rejects invalid is_private', function ($invalidParam) {
        $this->actingAs($this->user);

        $response = $this->patch('/api/v1/me', ['is_private' => $invalidParam]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['is_private' => [['code', 'message']]]])
            ->assertJsonFragment(['code' => 'boolean']);
    })->with([
        'empty' => '',
        'not boolean' => 24,
        'boolean as string' => 'true',
    ]);
});
