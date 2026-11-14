<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\TrainerBooking;
use App\Models\TrainerSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrainerBooking>
 */
class TrainerBookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'trainer_slot_id' => TrainerSlot::factory(),
            'payment_id' => Payment::factory(),
            'amount' => 150000,
            'status' => 'pending_payment',
        ];
    }
}
