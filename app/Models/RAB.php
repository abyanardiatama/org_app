<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RAB extends Model
{
    /** @use HasFactory<\Database\Factories\RABFactory> */
    use HasFactory;

    protected $guarded = [];

    public function rabItems()
    {
        return $this->hasMany(RabItem::class, 'rab_id');
    }
}
