<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahasiswaBerprestasi extends Model
{
    /** @use HasFactory<\Database\Factories\MahasiswaBerprestasiFactory> */
    use HasFactory;
    protected $guarded = [];

    public function perlombaan()
    {
        return $this->belongsTo(Perlombaan::class);
    }
}
