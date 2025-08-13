<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'silkov78',
            'email' => 'piotr.silkov78@gmail.com',
            'password' => Hash::make('silkov78'),
            'bio' => 'Вандрую па спадчыне',
            'telegram' => '@' . fake()->userName(),
        ]);

         User::factory(10)->create();
    }
}
