<?php

namespace Database\Factories;

use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campground>
 */
class CampgroundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'osm_id' => (string) fake()->numberBetween(10 ** 10, 10 ** 10 * 2),
            'osm_geometry' => Point::makeGeodetic(
                fake()->randomFloat(4, 52.0, 56.0),
                fake()->randomFloat(4, 24.0, 32.0),
            ),
            'osm_name' => fake()->city(),
            'osm_description' => fake()->sentence(),
        ];
    }
}
