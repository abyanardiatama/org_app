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
        Schema::create('sertijabs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_surat');
            $table->string('periode_lama');
            $table->string('periode_baru');
            $table->string('ketua_lama');
            $table->string('nim_ketua_lama');
            $table->string('ttd_ketua_lama')->nullable();
            $table->string('ketua_baru');
            $table->string('nim_ketua_baru');
            $table->string('ttd_ketua_baru')->nullable();
            $table->string('warek_mhs');
            $table->string('nip_warek_mhs');
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
        Schema::dropIfExists('sertijabs');
    }
};
