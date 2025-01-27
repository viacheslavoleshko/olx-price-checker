<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Advert extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'is_active',
    ];

    /**
     * Scope a query to only include adverts subscribed by a given user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubscribedAdverts($query, $userId)
    {
        return $query->whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }
}
