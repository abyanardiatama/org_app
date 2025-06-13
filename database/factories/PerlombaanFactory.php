<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Perlombaan>
 */
class PerlombaanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kejuaraan' => $this->faker->sentence(3),
            'jenis_prestasi' => $this->faker->randomElement(['Akademik', 'Non-Akademik']),
            'tingkat_prestasi' => $this->faker->randomElement(['Universitas', 'Nasional', 'Internasional']),
            'penyelenggara' => $this->faker->optional()->company(),
            'lokasi_penyelenggara' => $this->faker->optional()->city(),
            'tanggal_mulai' => $this->faker->date(),
            'tanggal_selesai' => $this->faker->optional()->date(),
            'kategori_tanding' => $this->faker->optional()->randomElement(['Debat', 'Cerdas Cermat', 'Olimpiade', 'Lomba Karya Tulis']),
            'url_kegiatan' => $this->faker->optional()->url(),
        ];
    }
}
