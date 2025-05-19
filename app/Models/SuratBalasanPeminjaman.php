<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratBalasanPeminjaman extends Model
{
    /** @use HasFactory<\Database\Factories\SuratBalasanPeminjamanFactory> */
    use HasFactory;
    protected $guarded = [];

    public function suratPeminjaman()
    {
        return $this->belongsTo(SuratPeminjaman::class, 'surat_peminjaman_id');
    }
}
