<?php

use App\Models\Campground;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('user', function () {
    it('increments visits_count with new visits', function () {
        $user = User::factory()->create();

        Campground::factory(10)->create();

        foreach (Campground::all() as $campground) {
            Visit::factory()->create([
                'user_id' => $user->user_id,
                'campground_id' => $campground->campground_id,
            ]);
        }

        $user->refresh();

        expect($user->visits()->count())->toBe(10)
            ->and($user->visits_count)->toBe(10);
    });

    it('returns user rank correctly (male)', function ($visitsCount, $rank) {
        $user = User::factory()->create(['gender' => 'male']);

        Campground::factory($visitsCount)->create();

        foreach (Campground::all() as $campground) {
            Visit::factory()->create([
                'user_id' => $user->user_id,
                'campground_id' => $campground->campground_id,
            ]);
        }

        $user->refresh();

        expect($user->getRank())->toBe($rank);
    })->with([
        [0, 'Домосед'],
        [1, 'Новичок'],
        [6, 'Идущий к реке'],
        [16, 'Турист'],
        [31, 'Следопыт'],
        [51, 'Кочевник'],
        [101, 'Беар Гриллс'],
    ]);
});