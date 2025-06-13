<?php

namespace Database\Factories;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Presensi>
 */
class PresensiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $peran = $this->faker->randomElement(['peserta', 'panitia', 'ketua divisi', 'ketua pelaksana']);
        $status = $this->faker->randomElement(['hadir', 'tidak hadir', 'pending']);

        $poinPeran = match ($peran) {
            'peserta' => 1,
            'panitia' => 2,
            'ketua divisi' => 3,
            'ketua pelaksana' => 4,
        };
        // $poinKehadiran = $status === 'hadir' ? 1 : ($status === 'tidak hadir' ? 0 : 0);
        $poinKehadiran = 1;

        $userId = $this->faker->numberBetween(1, 10);
        $totalPoinSebelumnya = DB::table('presensis')
            ->where('user_id', $userId)
            ->sum('total_poin'); // Akumulasi poin sebelumnya

        return [
            'kegiatan_id' => $this->faker->numberBetween(1, 10),
            'user_id' => $this->faker->numberBetween(1, 10),
            'status' => $status,
            'peran' => $peran,
            'poin_peran' => $poinPeran,
            'poin_kehadiran' => $poinKehadiran,
            'total_poin' => $totalPoinSebelumnya + $poinPeran + $poinKehadiran,
            'total_poin' => $poinPeran + $poinKehadiran
        ];
    }
}
