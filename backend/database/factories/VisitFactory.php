<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visit>
 */
class VisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 3),
            'campground_id' => $this->faker->numberBetween(1, 10),
            'visit_date' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
