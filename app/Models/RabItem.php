<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabItem extends Model
{
    /** @use HasFactory<\Database\Factories\RabItemFactory> */
    use HasFactory;
    protected $guarded = [];

    public function rab()
    {
        return $this->belongsTo(RAB::class);
    }
}
