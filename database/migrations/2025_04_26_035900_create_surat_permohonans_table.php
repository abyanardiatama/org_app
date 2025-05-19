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
        Schema::create('surat_permohonans', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->date('tanggal_surat');
            $table->string('jml_lampiran')->nullable();
            $table->string('perihal');
            $table->string('tujuan_surat');
            $table->string('keperluan');
            $table->string('penyelenggara');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->string('tempat');
            $table->string('ketua_kmi');
            $table->string('nim_ketua_kmi');
            $table->string('ttd_ketua_kmi')->nullable();
            $table->string('pembina_kmi');
            $table->string('nip_pembina_kmi');
            $table->string('ttd_pembina_kmi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_permohonans');
    }
};
