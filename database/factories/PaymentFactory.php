<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
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
            'checkout_batch' => fake()->uuid(),
            'order_id' => 'GYMPINANG-'.fake()->unique()->uuid(),
            'gross_amount' => fake()->numberBetween(20000, 500000),
            'status' => 'pending',
        ];
    }
}
