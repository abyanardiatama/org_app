<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempatPeminjaman extends Model
{
    /** @use HasFactory<\Database\Factories\TempatPeminjamanFactory> */
    use HasFactory;
    protected $guarded = [];

    public function suratPeminjaman()
    {
        return $this->belongsTo(SuratPeminjaman::class);
    }
}
