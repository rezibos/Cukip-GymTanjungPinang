<?php

namespace Database\Factories;

use App\Models\GymLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GymLocation>
 */
class GymLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'CukipGym · '.fake()->city(),
            'address' => fake()->streetAddress().', Tanjungpinang',
            'latitude' => fake()->randomFloat(7, 0.85, 1.00),
            'longitude' => fake()->randomFloat(7, 104.40, 104.55),
            'opening_hours' => '06:00 – 22:00 · Setiap hari',
            'whatsapp' => null,
            'image_path' => null,
            'image_position' => 'center',
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active' => true,
        ];
    }
}
