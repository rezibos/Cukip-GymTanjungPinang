<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'duration_days' => fake()->randomElement([1, 7, 30, 365]),
            'price' => fake()->numberBetween(20000, 500000),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}