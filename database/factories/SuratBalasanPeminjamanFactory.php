<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\SuratPeminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuratBalasanPeminjaman>
 */
class SuratBalasanPeminjamanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Nomor surat contoh: 077/PP/KMI_UPNVYK/V/2023
        $no_surat = str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT)
            . '/PP/KMI_UPNVYK/' . $this->faker->randomElement(['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'])
            . '/' . date('Y');
        $tanggal_surat = $this->faker->dateTimeBetween('2025-01-01', now())->format('Y-m-d');
        $jml_lampiran = $this->faker->numberBetween(0, 5);
        $lampiran = $jml_lampiran > 0 ? $this->faker->filePath() . $this->faker->uuid . '.pdf' : null;

        // Relasi ke surat peminjaman
        $surat_peminjaman = SuratPeminjaman::inRandomOrder()->first();
        $surat_peminjaman_id = $surat_peminjaman ? $surat_peminjaman->id : null;

        // Ketua
        $ketua = User::where('role', 'ketua')->inRandomOrder()->first();
        $nama_ketua = $ketua->name ?? 'Default Ketua';
        $nim_ketua = $ketua->nim ?? '00000000';
        $ttd_ketua = $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null;

        // Sekretaris
        $sekretaris = User::where('role', 'sekretaris')->inRandomOrder()->first();
        $nama_sekretaris = $sekretaris->name ?? 'Default Sekretaris';
        $nim_sekretaris = $sekretaris->nim ?? '00000000';
        $ttd_sekretaris = $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null;

        return [
            'no_surat' => $no_surat,
            'tanggal_surat' => $tanggal_surat,
            'jml_lampiran' => $jml_lampiran,
            'lampiran' => $lampiran,
            'surat_peminjaman_id' => $surat_peminjaman_id,

            'nama_ketua' => $nama_ketua,
            'nim_ketua' => $nim_ketua,
            'ttd_ketua' => $ttd_ketua,

            'nama_sekretaris' => $nama_sekretaris,
            'nim_sekretaris' => $nim_sekretaris,
            'ttd_sekretaris' => $ttd_sekretaris,
        ];
    }
}
