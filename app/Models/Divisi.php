<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    /** @use HasFactory<\Database\Factories\DivisiFactory> */
    use HasFactory;
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class);
    }

    public function surat()
    {
        return $this->hasMany(Surat::class);
    }
}
