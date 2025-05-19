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
        Schema::create('spkbs', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->date('tanggal_surat');
            $table->string('jml_lampiran')->nullable();
            $table->string('ketua_kmi');
            $table->string('nim_ketua_kmi');
            $table->string('ttd_ketua_kmi')->nullable();
            $table->string('sekretaris_kmi');
            $table->string('nim_sekretaris_kmi');
            $table->string('ttd_sekretaris_kmi')->nullable();
            $table->string('kabag_binwa');
            $table->string('nip_kabag_binwa');
            $table->string('pembina_kmi');
            $table->string('nip_pembina_kmi');
            $table->string('periode');

            $table->string('susunan_pengurus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spkbs');
    }
};
