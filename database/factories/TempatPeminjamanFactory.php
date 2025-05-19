<?php

namespace Database\Factories;

use App\Models\SuratPeminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TempatPeminjaman>
 */
class TempatPeminjamanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $no_surat = SuratPeminjaman::inRandomOrder()->first()?->id;
        $tempat_value = [
            'Gedung Serbaguna',
            'Lapangan Basket',
            'Lapangan Futsal',
            'Ruang Rapat',
            'Ruang Kelas',
        ];
        $tempat = $this->faker->randomElement($tempat_value);
        return [
            'surat_peminjaman_id' => $no_surat,
            'nama_tempat' => $tempat,
        ];
    }
}
