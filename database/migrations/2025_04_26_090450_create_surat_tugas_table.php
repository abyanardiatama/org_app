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
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->string('tempat');
            $table->string('tanggal_surat');

            $table->string('kepada');
            $table->string('nim_kepada');
            $table->string('jurusan_kepada');
            $table->string('jabatan_kepada');

            $table->string('ketua_kmi');
            $table->string('nim_ketua_kmi');
            $table->string('jurusan_ketua_kmi');
            $table->string('jabatan_ketua_kmi');

            $table->string('ttd_ketua_kmi')->nullable();
            $table->string('ttd_kepada')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_tugas');
    }
};
