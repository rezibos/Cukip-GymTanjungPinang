<?php

namespace Database\Factories;

use App\Enums\DayOfWeek;
use Illuminate\Database\Eloquent\Factories\Factory;

class GymClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'instructor' => fake()->name(),
            'day_of_week' => fake()->randomElement(DayOfWeek::cases()),
            'start_time' => '07:00',
            'end_time' => '08:00',
            'capacity' => 15,
            'is_active' => true,
        ];
    }
}