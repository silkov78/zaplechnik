<?php

namespace Database\Seeders;

use App\Models\Visit;
use Illuminate\Database\Seeder;

class VisitSeeder extends Seeder
{
    public function run(): void
    {
        Visit::factory()->create([
            'user_id' => 1,
            'campground_id' => 1,
            'visit_date' => '2025-05-05',
        ]);

        Visit::factory(10)->create();
    }
}
