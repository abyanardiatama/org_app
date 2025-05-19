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
        Schema::create('surat_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_proker');
            $table->string('periode');
            $table->integer('jml_lampiran')->nullable();
            $table->string('lampiran')->nullable();

            $table->string('no_surat');
            $table->date('tanggal_surat');

            $table->string('nama_kegiatan');
            $table->dateTime('hari_tanggal_waktu');
            $table->string('tempat');

            $table->string('nama_kabag_kemahasiswaan');
            $table->string('nip_kabag_kemahasiswaan');
            $table->string('ttd_kabag_kemahasiswaan')->nullable();

            $table->string('nama_ketua_panitia');
            $table->string('nim_ketua_panitia');
            $table->string('ttd_ketua_panitia')->nullable();

            $table->string('nama_ketupel');
            $table->string('nim_ketupel');
            $table->string('ttd_ketupel')->nullable();
            
            $table->string('nama_sekretaris');
            $table->string('nim_sekretaris');
            $table->string('ttd_sekretaris')->nullable();
            
            $table->string('nama_ketua');
            $table->string('nim_ketua');
            $table->string('ttd_ketua')->nullable();
            
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
        Schema::dropIfExists('surat_proposals');
    }
};
