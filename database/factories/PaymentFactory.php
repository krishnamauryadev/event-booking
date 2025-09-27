<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
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
            'booking_id' => Booking::inRandomOrder()->first()->id, // pick existing booking
            'amount' => Booking::inRandomOrder()->first()->ticket->price * Booking::inRandomOrder()->first()->quantity,
            'status' => $this->faker->randomElement(['success','failed','refunded']),
        ];
    }
}
