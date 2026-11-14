<?php

namespace Database\Factories;

use App\Models\PersonalTrainer;
use App\Models\TrainerSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrainerSlot>
 */
class TrainerSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'personal_trainer_id' => PersonalTrainer::factory(),
            'starts_at' => now()->addDays(2)->setTime(9, 0),
            'ends_at' => now()->addDays(2)->setTime(11, 0),
            'status' => 'available',
        ];
    }
}
