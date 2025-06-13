<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MahasiswaBerprestasiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nim' => $this->faker->numerify('13########'),
            'nama' => $this->faker->name(),
            'prodi' => $this->faker->randomElement(['Informatika', 'Teknik Kimia', 'Manajemen', 'Agroteknologi']),
            'fakultas' => $this->faker->randomElement(['FTI', 'FEB', 'FP', 'FISIP', 'FTM']),
            'jenis_prestasi' => $this->faker->randomElement(['Akademik', 'Non-Akademik']),
            'tingkat_prestasi' => $this->faker->randomElement(['Universitas', 'Nasional', 'Internasional']),
            'nama_kejuaraan' => $this->faker->sentence(3),
            'penyelenggara' => $this->faker->company(),
            'lokasi_penyelenggara' => $this->faker->city(),
            'jumlah_pt_peserta' => $this->faker->numberBetween(1, 50),
            'jumlah_peserta_lomba' => $this->faker->numberBetween(5, 500),
            'tanggal_mulai' => $this->faker->date(),
            'tanggal_selesai' => $this->faker->optional()->date(),
            'peringkat' => $this->faker->optional()->randomElement(['Juara 1', 'Juara 2', 'Juara 3', 'Harapan 1', 'Finalis']),
            'tunggal/beregu' => $this->faker->optional()->randomElement(['Tunggal', 'Beregu']),
            'kategori_tanding' => $this->faker->optional()->randomElement(['Debat', 'Cerdas Cermat', 'Olimpiade', 'Lomba Karya Tulis']),
            'dosen_pembimbing' => $this->faker->optional()->name(),
            'nidn' => $this->faker->optional()->numerify('10######'),
            'nip' => $this->faker->optional()->numerify('19##########'),
            'foto_penerima_prestasi' => $this->faker->optional()->imageUrl(300, 400, 'people'),
            'sertifikat_prestasi' => $this->faker->optional()->imageUrl(400, 300, 'abstract'),
            'surat_tugas' => $this->faker->optional()->imageUrl(400, 300, 'business'),
            'url_kegiatan' => $this->faker->optional()->url(),
            'nomor_telepon' => $this->faker->optional()->phoneNumber(),
            'nomor_wa' => $this->faker->optional()->phoneNumber(),
            'perlombaan_id' => \App\Models\Perlombaan::factory(),
        ];
    }
}
