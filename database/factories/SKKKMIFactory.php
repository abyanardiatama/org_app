<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SKKKMI>
 */
class SKKKMIFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Contoh nomor surat: 085/SK-KKMI/KMI_UPNVYK/VII/2023
        $no_surat = str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT)
            . '/SK-KKMI/KMI_UPNVYK/' . $this->faker->randomElement(['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'])
            . '/' . date('Y');
        $tanggal_surat = $this->faker->dateTimeBetween('2025-01-01', now())->format('Y-m-d');
        $periode = $this->faker->randomElement(['2023/2024', '2024/2025', '2025/2026']);
        $jml_lampiran = $this->faker->numberBetween(0, 5);
        $lampiran = $jml_lampiran > 0 ? $this->faker->filePath() . $this->faker->uuid . '.pdf' : null;

        $nama_kkmi = $this->faker->company();
        $fakultas = $this->faker->randomElement(['Fakultas Teknik', 'Fakultas Ekonomi', 'Fakultas Ilmu Sosial', 'Fakultas Hukum']);

        // Pembina
        $pembina = User::where('role', 'pembina')->inRandomOrder()->first();
        $nama_pembina = $pembina->name ?? 'Default Pembina';
        $nip_pembina = $pembina->nip ?? '0000000000';
        $ttd_pembina = $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null;

        return [
            'no_surat' => $no_surat,
            'tanggal_surat' => $tanggal_surat,
            'periode' => $periode,
            'jml_lampiran' => $jml_lampiran,
            'lampiran' => $lampiran,
            'nama_kkmi' => $nama_kkmi,
            'fakultas' => $fakultas,
            'nama_pembina' => $nama_pembina,
            'nip_pembina' => $nip_pembina,
            'ttd_pembina' => $ttd_pembina,
        ];
    }
}
