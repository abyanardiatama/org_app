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
        Schema::create('s_k_k_k_m_i_s', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->date('tanggal_surat');
            $table->string('periode'); //2023/2024
            $table->integer('jml_lampiran')->nullable();
            $table->string('lampiran')->nullable();

            $table->string('nama_kkmi');
            $table->string('fakultas');

            //nama pembina
            $table->string('nama_pembina');
            $table->string('nip_pembina');
            $table->string('ttd_pembina')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_k_k_k_m_i_s');
    }
};
