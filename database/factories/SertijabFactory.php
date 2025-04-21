<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sertijab>
 */
class SertijabFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ketua_lama = User::where('role', 'ketua')->inRandomOrder()->first();
        $ketua_baru = User::where('role', 'ketua')->inRandomOrder()->first();

        $nim_ketua_lama = $ketua_lama ? $ketua_lama->nim : 'Default NIM Ketua';
        $nim_ketua_baru = $ketua_baru ? $ketua_baru->nim : 'Default NIM Ketua';
        return [
            'tanggal_surat' => $this->faker->dateTimeBetween('2023-01-01', now())->format('Y-m-d'),
            'periode_lama' => $this->faker->year(),
            'periode_baru' => $this->faker->year(),
            // use name from user with role 'ketua'
            'ketua_lama' => $ketua_lama ? $ketua_lama->name : 'Default Ketua',
            'nim_ketua_lama' => $nim_ketua_lama,
            'ttd_ketua_lama' => $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null,
            'ketua_baru' => $ketua_baru ? $ketua_baru->name : 'Default Ketua',
            'nim_ketua_baru' => $nim_ketua_baru,
            'ttd_ketua_baru' => $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null,
            'warek_mhs' => $this->faker->name(),
            'nip_warek_mhs' => $this->faker->numberBetween(1000000000, 9999999999),
            'pembina_kmi' => $this->faker->name(),
            'nip_pembina_kmi' => $this->faker->numberBetween(1000000000, 9999999999),
            //directory for ttd_ketua_lama
        ];
    }
}
