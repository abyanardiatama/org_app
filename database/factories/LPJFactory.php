<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LPJ>
 */
class LPJFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $periode = $this->faker->dateTimeBetween('2023-01-01', now())->format('Y');
        $jml_lampiran = $this->faker->numberBetween(0, 5);
        $lampiran = $jml_lampiran > 0 ? $this->faker->filePath() . $this->faker->uuid . '.pdf' : null;

        $nama_proker = $this->faker->randomElement(['Proker 1', 'Proker 2', 'Proker 3', 'Proker 4']);
        $nama_kegiatan = $this->faker->randomElement(['Kegiatan 1', 'Kegiatan 2', 'Kegiatan 3', 'Kegiatan 4']);
        $tanggal_surat = $this->faker->dateTimeBetween('2025-01-01', now())->format('Y-m-d');

        // Kabag Kemahasiswaan
        $kabag = User::where('role', 'kabag')->inRandomOrder()->first();
        $nama_kabag_kemahasiswaan = $kabag->name ?? 'Default Kabag';
        $nip_kabag_kemahasiswaan = $kabag->nip ?? '0000000000';
        $ttd_kabag_kemahasiswaan = $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null;

        // Ketua Panitia
        $ketua_panitia = User::inRandomOrder()->first();
        $nama_ketua_panitia = $ketua_panitia->name ?? 'Default Ketua Panitia';
        $nim_ketua_panitia = $ketua_panitia->nim ?? '00000000';
        $ttd_ketua_panitia = $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null;

        // Ketua Pelaksana
        $ketupel = User::inRandomOrder()->first();
        $nama_ketupel = $ketupel->name ?? 'Default Ketua Pelaksana';
        $nim_ketupel = $ketupel->nim ?? '00000000';
        $ttd_ketupel = $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null;

        // Sekretaris
        $sekretaris = User::where('role', 'sekretaris')->inRandomOrder()->first();
        $nama_sekretaris = $sekretaris->name ?? 'Default Sekretaris';
        $nim_sekretaris = $sekretaris->nim ?? '00000000';
        $ttd_sekretaris = $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null;

        // Ketua
        $ketua = User::where('role', 'ketua')->inRandomOrder()->first();
        $nama_ketua = $ketua->name ?? 'Default Ketua';
        $nim_ketua = $ketua->nim ?? '00000000';
        $ttd_ketua = $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null;

        // Pembina
        $pembina = User::where('role', 'pembina')->inRandomOrder()->first();
        $nama_pembina = $pembina->name ?? 'Default Pembina';
        $nip_pembina = $pembina->nip ?? '0000000000';
        $ttd_pembina = $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null;

        return [
            'nama_proker' => $nama_proker,
            'periode' => $periode,
            'jml_lampiran' => $jml_lampiran,
            'lampiran' => $lampiran,

            'tanggal_surat' => $tanggal_surat,

            'nama_kegiatan' => $nama_kegiatan,

            'nama_kabag_kemahasiswaan' => $nama_kabag_kemahasiswaan,
            'nip_kabag_kemahasiswaan' => $nip_kabag_kemahasiswaan,
            'ttd_kabag_kemahasiswaan' => $ttd_kabag_kemahasiswaan,

            'nama_ketua_panitia' => $nama_ketua_panitia,
            'nim_ketua_panitia' => $nim_ketua_panitia,
            'ttd_ketua_panitia' => $ttd_ketua_panitia,

            'nama_ketupel' => $nama_ketupel,
            'nim_ketupel' => $nim_ketupel,
            'ttd_ketupel' => $ttd_ketupel,

            'nama_sekretaris' => $nama_sekretaris,
            'nim_sekretaris' => $nim_sekretaris,
            'ttd_sekretaris' => $ttd_sekretaris,

            'nama_ketua' => $nama_ketua,
            'nim_ketua' => $nim_ketua,
            'ttd_ketua' => $ttd_ketua,

            'nama_pembina' => $nama_pembina,
            'nip_pembina' => $nip_pembina,
            'ttd_pembina' => $ttd_pembina,
        ];
    }
}
