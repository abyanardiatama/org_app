<?php

namespace Database\Seeders;

use App\Models\LPJ;
use App\Models\RAB;
use App\Models\spkb;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Donasi;
use App\Models\SKKKMI;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\Sertijab;
use App\Models\Transaksi;
use App\Models\SuratTugas;
use App\Models\SuratProposal;
use App\Models\SuratUndangan;
use App\Models\SuratDivisiKKMI;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\SuratPeminjaman;
use App\Models\SuratPeringatan;
use App\Models\SuratPermohonan;
use Illuminate\Database\Seeder;
use App\Models\BarangPeminjaman;
use App\Models\MahasiswaBerprestasi;
use App\Models\Perlombaan;
use App\Models\RabItem;
use App\Models\TempatPeminjaman;
use App\Models\SuratKeteranganAktif;
use App\Models\SuratBalasanPeminjaman;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Divisi::factory(8)->create();
        // User::factory(11)->create();
        //user seeder
        $this->call([
            DivisiSeeder::class,
            UserSeeder::class,
            KegiatanSeeder::class,
        ]);
        // Kegiatan::factory(11)->create();
        Presensi::factory(11)->create();
        RAB::factory(11)->create();
        Transaksi::factory(11)->create();
        spkb::factory(11)->create();
        Sertijab::factory(11)->create();
        SuratPeringatan::factory(11)->create();
        SuratPermohonan::factory(11)->create();
        SuratTugas::factory(11)->create();
        SuratKeteranganAktif::factory(11)->create();
        SuratPeminjaman::factory(11)->create();
        BarangPeminjaman::factory(11)->create();
        TempatPeminjaman::factory(11)->create();
        SuratDivisiKKMI::factory(11)->create();
        SuratUndangan::factory(11)->create();
        SuratProposal::factory(11)->create();
        LPJ::factory(11)->create();
        SuratBalasanPeminjaman::factory(11)->create();
        SKKKMI::factory(11)->create();
        Perlombaan::factory(11)->create();
        MahasiswaBerprestasi::factory(11)->create();
        RabItem::factory(11)->create();

        // Tambah user eksternal dummy
        User::factory(5)->create([
            'role' => 'external',
        ]);

        Donasi::factory(11)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'external',
        ]);
    }
}
