<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perlombaan extends Model
{
    /** @use HasFactory<\Database\Factories\PerlombaanFactory> */
    use HasFactory;
    protected $guarded = [];

    public function mahasiswaBerprestasi()
    {
        return $this->hasMany(MahasiswaBerprestasi::class);
    }
}
