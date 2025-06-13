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
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->text('deskripsi_kegiatan')->nullable();
            // $table->text('gambaran_singkat');
            // $table->text('tujuan_kegiatan');
            // $table->text('manfaat_kegiatan');
            // $table->text('sasaran_kegiatan');
            $table->foreignId('divisi_id')->constrained('divisis')->onDelete('cascade');
            $table->string('status')->default('aktif');
            $table->integer('total_biaya')->default(0);
            $table->integer('target_biaya')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};
