<?php

namespace Database\Factories;

use App\Models\GymProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GymProfile>
 */
class GymProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Gym',
            'description' => 'Gym lokal di Tanjungpinang.',
            'address' => fake()->streetAddress().', Tanjungpinang',
            'latitude' => 0.9187,
            'longitude' => 104.4665,
            'opening_hours' => '06:00 - 22:00',
            'whatsapp' => null,
            'image' => null,
        ];
    }
}
