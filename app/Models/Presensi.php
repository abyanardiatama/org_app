<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Presensi extends Model
{
    /** @use HasFactory<\Database\Factories\PresensiFactory> */
    use HasFactory;
    protected $guarded = [];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($presensi) {
            $presensi->poin_peran = $presensi->poin_peran ?? 0;
            $presensi->poin_kehadiran = $presensi->poin_kehadiran ?? 0;

            $presensi->total_poin = $presensi->poin_peran + $presensi->poin_kehadiran;

        });
    }

    
}
