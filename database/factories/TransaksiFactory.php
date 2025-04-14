<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaksi>
 */
class TransaksiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 10),
            'nominal' => 5000,
            'status' => $this->faker->randomElement(['pending', 'lunas', 'belum lunas']),
            'bukti_pembayaran' => "https://placehold.co/1080x1920",
        ];
    }
}
