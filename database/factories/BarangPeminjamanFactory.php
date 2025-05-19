<?php

namespace Database\Factories;

use App\Models\SuratPeminjaman;
use App\Models\SuratTugas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarangPeminjaman>
 */
class BarangPeminjamanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //take id from surat_peminjaman
        $no_surat = SuratPeminjaman::inRandomOrder()->first()?->id;
        $barang = [
            'Laptop',
            'Proyektor',
            'Kamera',
            'Mikrofon',
            'Speaker',
            'Meja',
            'Kursi',
            'Papan Tulis',
            'Kipas Angin',
            'AC',
        ];
        // jumlah + 'buah'
        $jumlah = $this->faker->numberBetween(1, 10) . ' buah';
        return [
            'surat_peminjaman_id' => $no_surat,
            'nama_barang' => $this->faker->randomElement($barang),
            'jumlah_barang' => $jumlah,
        ];
    }
}
