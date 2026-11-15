<?php

namespace Database\Factories;

use App\Enums\FacilityCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacilityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'category' => FacilityCategory::Support,
            'description' => null,
            'image_path' => null,
        ];
    }
}
