<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuratPeminjaman>
 */
class SuratPeminjamanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        //028/PP/KMI_UPNVYK/IV/2023
        $no_surat = str_pad($this->faker->unique()->numberBetween(1, 100), 3, '0', STR_PAD_LEFT) . '/PP/KMI_UPNVYK/IV/' . date('Y');
        $tanggal_surat = $this->faker->dateTimeBetween('2025-01-01', now())->format('Y-m-d');
        $periode = $this->faker->dateTimeBetween('2023-01-01', now())->format('Y');

        $nama_ketua_kmi = User::where('role', 'ketua')->inRandomOrder()->first();
        $ketua_kmi = $nama_ketua_kmi->name ?? 'Default Ketua KMI';
        $nim_ketua_kmi = $nama_ketua_kmi->nim ?? 'Default NIM Ketua KMI';

        $sekretaris_kmi = User::where('role', 'sekretaris')->inRandomOrder()->first();
        $nama_sekretaris_kmi = $sekretaris_kmi->name ?? 'Default Sekretaris KMI';
        $nim_sekretaris_kmi = $sekretaris_kmi->nim ?? 'Default NIM Sekretaris KMI';

        //user inrandomorder any role
        $ketupel_kmi = User::inRandomOrder()->first();
        $nama_ketupel_kmi = $ketupel_kmi->name ?? 'Default Ketua Pelaksana KMI';
        $nim_ketupel_kmi = $ketupel_kmi->nim ?? 'Default NIM Ketua Pelaksana KMI';

        //ttd ketua kmi
        $ttd_ketua_kmi = $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null;
        //ttd sekretaris kmi
        $ttd_sekretaris_kmi = $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null;
        //ttd ketupel kmi
        $ttd_ketupel_kmi = $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null;

        //faker company
        $kepada = $this->faker->company();
        $tempat_value = [
            'Gedung Serbaguna',
            'Lapangan Basket',
            'Lapangan Futsal',
            'Ruang Rapat',
            'Ruang Kelas',
        ];
        $tempat = $this->faker->randomElement($tempat_value);
        $kegiatan_value = [
            'Rapat Anggota',
            'Pelatihan',
            'Seminar',
            'Workshop',
            'Diskusi Publik',
        ];
        $kegiatan = $this->faker->randomElement($kegiatan_value);
        //date time
        $tanggal_mulai = $this->faker->dateTimeBetween('2025-01-01', now())->format('Y-m-d H:i:s');
        $tanggal_selesai = $this->faker->dateTimeBetween($tanggal_mulai, now())->format('Y-m-d H:i:s');


        return [
            'no_surat' => $no_surat,
            'tanggal_surat' => $tanggal_surat,
            'periode' => $periode,

            'kepada' => $kepada,
            'kegiatan' => $kegiatan,
            'tempat' => $tempat,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,

            'nama_ketua_kmi' => $ketua_kmi,
            'nim_ketua_kmi' => $nim_ketua_kmi,
            'ttd_ketua_kmi' => $ttd_ketua_kmi,

            'nama_sekretaris_kmi' => $nama_sekretaris_kmi,
            'nim_sekretaris_kmi' => $nim_sekretaris_kmi,
            'ttd_sekretaris_kmi' => $ttd_sekretaris_kmi,

            'nama_ketupel_kmi' => $nama_ketupel_kmi,
            'nim_ketupel_kmi' => $nim_ketupel_kmi,
            'ttd_ketupel_kmi' => $ttd_ketupel_kmi,
        ];
    }
}
