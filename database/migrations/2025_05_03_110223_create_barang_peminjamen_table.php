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
        Schema::create('barang_peminjamen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_peminjaman_id')->constrained('surat_peminjamen')->onDelete('cascade');
            $table->string('nama_barang');
            $table->string('jumlah_barang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_peminjamen');
    }
};
