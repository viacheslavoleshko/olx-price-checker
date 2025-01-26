<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'advert_id',
        'value',
        'currency',
        'negotiable',
        'trade',
        'budget',
    ];

    public function advert()
    {
        return $this->belongsTo(Advert::class);
    }
}
