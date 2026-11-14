<?php

namespace Database\Factories;

use App\Models\PersonalTrainer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PersonalTrainer>
 */
class PersonalTrainerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'specialization' => fake()->randomElement(['Functional Training', 'Strength Training', 'Weight Loss']),
            'description' => fake()->sentence(),
            'experience_years' => fake()->numberBetween(1, 8),
            'certifications' => ['Certified Personal Trainer'],
            'certification_documents' => [],
            'session_price' => 150000,
            'session_minutes' => 120,
            'is_active' => true,
        ];
    }
}
