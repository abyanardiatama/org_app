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
        Schema::create('surat_keterangan_aktifs', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->string('tanggal_surat');
            $table->string('periode');

            $table->string('nama_ketua_kmi');
            $table->string('nim_ketua_kmi');
            $table->string('jurusan_ketua_kmi');
            $table->string('ttd_ketua_kmi')->nullable();

            $table->string('kepada');
            $table->string('nim_kepada');
            $table->string('jurusan_kepada');
            $table->string('jabatan_kepada');

            $table->string('pembina_kmi');
            $table->string('nip_pembina_kmi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keterangan_aktifs');
    }
};
