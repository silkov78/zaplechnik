<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
