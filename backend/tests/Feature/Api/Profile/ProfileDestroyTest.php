<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
    Storage::put('avatars/old-avatar.png', 'content');

    $this->currentUser = User::factory()->create([
        'name' => 'testUser',
        'email' => 'testUser@test.com',
        'avatar' => 'old-avatar.png',
    ]);

    $this->anotherUser = User::factory()->create();
});

describe('profile: destroy', function () {
    it('rejects not-authenticated user', function () {
        $this->delete('/api/v1/me')->assertUnauthorized();
    });
});
