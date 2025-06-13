<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kegiatan extends Model
{
    /** @use HasFactory<\Database\Factories\KegiatanFactory> */
    use HasFactory;
    protected $guarded = [];

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
    public function donasi()
    {
        return $this->hasMany(Donasi::class);
    }
}
