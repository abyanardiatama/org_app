<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mahasiswa_berprestasis', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->string('nama');
            $table->string('prodi');
            $table->string('fakultas');

            $table->string('jenis_prestasi'); 
            $table->string('tingkat_prestasi');
            $table->string('nama_kejuaraan');
            $table->string('penyelenggara')->nullable();
            $table->string('lokasi_penyelenggara')->nullable();
            $table->string('jumlah_pt_peserta')->nullable();
            $table->string('jumlah_peserta_lomba')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->string('peringkat')->nullable();
            $table->string('tunggal/beregu')->nullable();
            $table->string('kategori_tanding')->nullable();

            $table->string('dosen_pembimbing')->nullable();
            $table->string('nidn')->nullable();
            $table->string('nip')->nullable();

            $table->string('foto_penerima_prestasi')->nullable();
            $table->string('sertifikat_prestasi')->nullable();
            $table->string('surat_tugas')->nullable();
            $table->string('url_kegiatan')->nullable();

            $table->string('nomor_telepon')->nullable();
            $table->string('nomor_wa')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa_berprestasis');
    }
};
