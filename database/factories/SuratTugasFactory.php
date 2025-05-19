<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuratTugas>
 */
class SuratTugasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //030/ST-KKMI/KMI_UPNVYK/V/2023
        $no_surat = str_pad($this->faker->unique()->numberBetween(1, 100), 3, '0', STR_PAD_LEFT) . '/ST-KKMI/KMI_UPNVYK/V/' . date('Y');
        $tanggal_surat = $this->faker->dateTimeBetween('2025-01-01', now())->format('Y-m-d');
        $tempat = 'Yogyakarta';

        $kepada = User::inRandomOrder()->first()?->name ?? 'Default Pembina KMI';
        $nim_kepada = User::inRandomOrder()->first()?->nim ?? 'Default NIM Pembina KMI';
        $jabatan = $kepada->role ?? 'Default Pembina KMI';
        $jurusan = $kepada->jurusan ?? 'Default Jurusan Pembina KMI';

        $ketua = User::where('role', 'ketua')->inRandomOrder()->first()?->name ?? 'Default Ketua KMI';
        $nim_ketua = $ketua->nim ?? 'Default NIM Ketua KMI';
        $jurusan_ketua = $ketua->jurusan ?? 'Default Jurusan Ketua KMI';
        $jabatan_ketua = $ketua->role ?? 'Default Jabatan Ketua KMI';
        return [
            'no_surat' => $no_surat,
            'tempat' => $tempat,
            'tanggal_surat' => $tanggal_surat,
            'kepada' => $kepada,
            'nim_kepada' => $nim_kepada,
            'jurusan_kepada' => $jurusan,
            'jabatan_kepada' => $jabatan,
            'ketua_kmi' => $ketua,
            'nim_ketua_kmi' => $nim_ketua,
            'jurusan_ketua_kmi' => $jurusan_ketua,
            'jabatan_ketua_kmi' => $jabatan_ketua,
            'ttd_ketua_kmi' => $this->faker->boolean(50) ? UploadedFile::fake()->image($this->faker->word . '.png', 100, 100) : null,
            'ttd_kepada' => $this->faker->boolean(50) ? UploadedFile::fake()->image($this->faker->word . '.png', 100, 100) : null,
        ];
    }
}
