<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Divisi>
 */
class DivisiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        return [
            //create divisi name divisi + nama
            'nama_divisi' => $this->faker->randomElement(['Divisi Media', 'Divisi Kaderisasi', 'Divisi BKKMI', 'Divisi Keilmuan', 'Divisi Kemuslimahan', 'Divisi Syiar', 'Divisi Jaringan', 'Divisi Dana Usaha']),
            //use picsum
            'image' => 'https://picsum.photos/300/200',
        ];
    }
}
