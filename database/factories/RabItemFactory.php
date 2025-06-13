<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RabItem>
 */
class RabItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qty = $this->faker->numberBetween(1, 10);
        $hargaSatuan = $this->faker->numberBetween(10000, 500000);
        return [
            'rab_id' => \App\Models\RAB::factory(),
            'keterangan' => $this->faker->sentence(3),
            'qty' => $qty,
            'harga_satuan' => $hargaSatuan,
            'jumlah' => $qty * $hargaSatuan,
        ];
    }
}
