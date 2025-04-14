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
        Schema::create('r_a_b_s', function (Blueprint $table) {
            $table->id();
            $table->string('divisi');
            $table->string('nama_anggota');
            $table->string('tanggal_kegiatan');
            $table->string('jumlah');
            $table->string('status')->default('belum diproses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('r_a_b_s');
    }
};
