<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Kegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donasi>
 */
class DonasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        //user from role external
        $donatur = User::where('role', 'external')->inRandomOrder()->first();
        $kegiatan = Kegiatan::inRandomOrder()->first();
        return [
            'user_id' => $donatur->id, // Ambil ID user yang valid dari tabel users
            'kegiatan_id' => $kegiatan->id, // Ambil ID kegiatan yang valid dari tabel kegiatans
            'jumlah_donasi' => $this->faker->numberBetween(10000, 1000000),
            'metode_pembayaran' => $this->faker->randomElement(['transfer', 'cash', 'e-wallet', 'credit_card', 'debit_card', 'qr_code']),
            'bukti_pembayaran' => $this->faker->imageUrl(640, 480, 'business', true, 'Payment Proof'), // URL gambar bukti pembayaran
        ];
    }
}
