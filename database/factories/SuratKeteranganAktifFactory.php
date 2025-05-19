<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuratKeteranganAktif>
 */
class SuratKeteranganAktifFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //no_surat => 085/SK-KKMI/KMI_UPNVYK/VII/2023
        $no_surat = str_pad($this->faker->unique()->numberBetween(1, 100), 3, '0', STR_PAD_LEFT) . '/SK-KKMI/KMI_UPNVYK/VII/' . date('Y');
        $tanggal_surat = $this->faker->dateTimeBetween('2025-01-01', now())->format('Y-m-d');
        $periode = $this->faker->dateTimeBetween('2023-01-01', now())->format('Y');

        $nama_ketua_kmi = User::where('role', 'ketua')->inRandomOrder()->first();
        $ketua_kmi = $nama_ketua_kmi->name ?? 'Default Ketua KMI';
        $nim_ketua_kmi = $nama_ketua_kmi->nim ?? 'Default NIM Ketua KMI';
        $jurusan_ketua_kmi = $nama_ketua_kmi->prodi ?? 'Default Jurusan Ketua KMI';

        $kepada = User::inRandomOrder()->first();
        $nama_kepada = $kepada->name ?? 'Default Nama';
        $nim_kepada = $kepada->nim ?? 'Default NIM';
        $jurusan_kepada = $kepada->prodi ?? 'Default Jurusan';
        //merge amanah and divisi_id(take name from divisi_id)
        $amanah = $kepada->amanah ?? 'Defautt Amanah';
        //dapatkan nama divisi dari divisi_id
        $divisi = $kepada->divisi->nama_divisi;
        $jabatan_kepada = $amanah . ' ' . $divisi; 

        $pembina_kmi = User::where('role', 'external')->inRandomOrder()->first()?->name ?? 'Default Pembina KMI';
        $nip_pembina_kmi = $pembina_kmi->nip ?? 'Default NIP Pembina KMI';
        return [
            'no_surat' => $no_surat,
            'tanggal_surat' => $tanggal_surat,
            'periode' => $periode,
            'nama_ketua_kmi' => $ketua_kmi,
            'nim_ketua_kmi' => $nim_ketua_kmi,
            'jurusan_ketua_kmi' => $jurusan_ketua_kmi,
            'ttd_ketua_kmi' => $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null,
            'kepada' => $nama_kepada,
            'nim_kepada' => $nim_kepada,
            'jurusan_kepada' => $jurusan_kepada,
            'jabatan_kepada' => $jabatan_kepada,
            'pembina_kmi' => $pembina_kmi,
            'nip_pembina_kmi' => $nip_pembina_kmi,
            
        ];
    }
}
