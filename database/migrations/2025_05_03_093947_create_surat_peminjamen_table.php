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
        Schema::create('surat_peminjamen', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->date('tanggal_surat');
            $table->string('periode');

            $table->string('kepada');
            $table->string('kegiatan');
            $table->string('tempat');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->string('nama_ketua_kmi');
            $table->string('nim_ketua_kmi');
            $table->string('ttd_ketua_kmi')->nullable();
            
            $table->string('nama_sekretaris_kmi');
            $table->string('nim_sekretaris_kmi');
            $table->string('ttd_sekretaris_kmi')->nullable();
            
            $table->string('nama_ketupel_kmi');
            $table->string('nim_ketupel_kmi');
            $table->string('ttd_ketupel_kmi')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_peminjamen');
    }
};
