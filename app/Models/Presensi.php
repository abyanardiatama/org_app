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
            // Ambil total poin sebelumnya
            $totalPoinSebelumnya = Presensi::where('user_id', $presensi->user_id)->sum('total_poin');
            
            // Update total poin baru
            $presensi->total_poin = $totalPoinSebelumnya + $presensi->poin_peran + $presensi->poin_kehadiran;
        });
    }

    
}
