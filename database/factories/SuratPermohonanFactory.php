<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuratPermohonan>
 */
class SuratPermohonanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   //no_surat => 072/PH/KMI_UPNVYK/V/2023
        // Generate a unique no_surat number between 1 and 100
        $no_surat = str_pad($this->faker->unique()->numberBetween(1, 100), 3, '0', STR_PAD_LEFT) . '/PH/KMI_UPNVYK/V/' . date('Y');
        $tanggal_surat = $this->faker->dateTimeBetween('2025-01-01', now())->format('Y-m-d');
        // Generate a random datetime between 2024-01-01 and now
        $tanggal_mulai = $this->faker->dateTimeBetween('2025-01-01', now())->format('Y-m-d');
        $tanggal_selesai = $this->faker->dateTimeBetween($tanggal_mulai, now())->format('Y-m-d');
        $lampiran = $this->faker->numberBetween(1, 10);
        //nama acara 
        $prefix = ['Seminar', 'Workshop', 'Talkshow', 'Pelatihan', 'Diskusi'];
        $topics = ['Teknologi', 'Bisnis', 'Kesehatan', 'Pendidikan', 'Lingkungan'];
        $types = ['Nasional', 'Internasional', 'Lokal', 'Online', 'Hybrid'];

        $namaAcara = Arr::random($prefix) . ' ' . Arr::random($topics) . ' ' . Arr::random($types);

        //perihal surat
        $prefiks = ['Permohonan', 'Undangan', 'Pemberitahuan', 'Pengajuan', 'Laporan'];
        $topik = ['Kegiatan', 'Kerjasama', 'Penggunaan Ruangan', 'Sponsorship', 'Kunjungan'];
        $tipe  = ['Internal', 'Eksternal', 'Resmi', 'Tidak Resmi', 'Urgent'];
        $perihal = $prefiks[array_rand($prefiks)] . ' ' . $topik[array_rand($topik)] . ' ' . $tipe[array_rand($tipe)];

        // nama organisasi
        $awalan = ['Forum', 'Komunitas', 'Asosiasi', 'Perkumpulan', 'Himpunan'];
        $core = ['Mahasiswa', 'Pemuda', 'Teknologi', 'Lingkungan', 'Pengusaha'];
        $suffix = ['Indonesia', 'Nasional', 'Semarang', 'Digital', 'Muda'];

        $namaOrganisasi = $awalan[array_rand($awalan)] . ' ' . $core[array_rand($core)] . ' ' . $suffix[array_rand($suffix)];

        // tempat pengadaan acara
        $jenisTempat = ['Aula', 'Gedung', 'Ruang', 'Hall', 'Auditorium'];
        $namaTempat = ['Serbaguna', 'Utama', 'Multimedia', 'Pertemuan', 'Inovasi'];
        $lokasi = ['Kampus A', 'Kampus B', 'Jakarta', 'Semarang', 'Yogyakarta'];

        $tempatAcara = $jenisTempat[array_rand($jenisTempat)] . ' ' . $namaTempat[array_rand($namaTempat)] . ' - ' . $lokasi[array_rand($lokasi)];

        $ketua = User::where('role', 'ketua')->inRandomOrder()->first()?->name ?? 'Default Ketua KMI';
        $nim_ketua = $ketua->nim ?? 'Default NIM Ketua KMI';
        $pembina = User::where('role', 'external')->inRandomOrder()->first()?->name ?? 'Default Pembina KMI';
        $nip_pembina = $pembina->nim ?? 'Default NIP Pembina KMI';
        return [
            'no_surat' => $no_surat,
            'tanggal_surat' => $tanggal_surat,
            'jml_lampiran' => $lampiran,
            'perihal' => $perihal,
            'tujuan_surat' => User::where('role', 'external')->inRandomOrder()->first()?->name ?? 'Default Tujuan Surat',
            'keperluan' => $namaAcara,
            'penyelenggara' => $namaOrganisasi,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'tempat' => $tempatAcara,
            'ketua_kmi' => $ketua,
            'nim_ketua_kmi' => $nim_ketua,
            'pembina_kmi' => $pembina,
            'nip_pembina_kmi' => $nip_pembina,
            'ttd_ketua_kmi' => $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null,
            'ttd_pembina_kmi' => $this->faker->boolean(50) ? $this->faker->imageUrl(100, 100) : null,
        ];
    }
}
