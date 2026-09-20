<?php

namespace Database\Factories;

use App\Enums\Region;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class GymFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->lastName() . ' Fitness',
            'description' => fake()->paragraph(),
            'address' => 'Jl. Contoh No. ' . fake()->numberBetween(1, 99),
            'region' => fake()->randomElement(Region::cases()),
            'phone' => '0771-000000',
            'whatsapp' => '6281200000000',
            'latitude' => fake()->randomFloat(7, 0.90, 1.05),
            'longitude' => fake()->randomFloat(7, 104.30, 104.60),
            'opening_hours' => '06:00 - 22:00',
        ];
    }
}