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
        Schema::create('perlombaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kejuaraan');
            $table->string('jenis_prestasi');
            $table->string('tingkat_prestasi');
            $table->string('penyelenggara')->nullable();
            $table->string('lokasi_penyelenggara')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->string('kategori_tanding')->nullable();
            $table->string('url_kegiatan')->nullable();
            $table->timestamps();
        });

        // Update mahasiswa_berprestasis table to add perlombaan_id foreign key
        Schema::table('mahasiswa_berprestasis', function (Blueprint $table) {
            $table->foreignId('perlombaan_id')->nullable()->constrained('perlombaans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa_berprestasis', function (Blueprint $table) {
            $table->dropForeign(['perlombaan_id']);
            $table->dropColumn('perlombaan_id');
        });
        Schema::dropIfExists('perlombaans');
    }
};
