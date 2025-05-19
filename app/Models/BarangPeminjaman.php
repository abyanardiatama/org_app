<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangPeminjaman extends Model
{
    /** @use HasFactory<\Database\Factories\BarangPeminjamanFactory> */
    use HasFactory;
    protected $guarded = [];

    public function suratPeminjaman()
    {
        return $this->belongsTo(SuratPeminjaman::class);
    }
}
