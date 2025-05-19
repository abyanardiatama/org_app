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
        Schema::create('surat_balasan_peminjamen', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->date('tanggal_surat');
            $table->integer('jml_lampiran')->nullable();
            $table->string('lampiran')->nullable();
            $table->foreignId('surat_peminjaman_id')->constrained('surat_peminjamen')->onDelete('cascade');

            $table->string('nama_ketua');
            $table->string('nim_ketua');
            $table->string('ttd_ketua')->nullable();
            
            $table->string('nama_sekretaris');
            $table->string('nim_sekretaris');
            $table->string('ttd_sekretaris')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_balasan_peminjamen');
    }
};
