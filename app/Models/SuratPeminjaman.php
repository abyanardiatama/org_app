<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPeminjaman extends Model
{
    /** @use HasFactory<\Database\Factories\SuratPeminjamanFactory> */
    use HasFactory;
    protected $guarded = [];

    public function barangPeminjaman()
    {
        return $this->hasMany(BarangPeminjaman::class);
    }

    public function tempatPeminjaman()
    {
        return $this->hasMany(TempatPeminjaman::class);
    }

    public function suratBalasanPeminjaman()
    {
        return $this->hasOne(SuratBalasanPeminjaman::class);
    }
}
