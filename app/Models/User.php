<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Scope a query to only include users subscribed to a specific advert.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $advertId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubscribers($query, $advertId): Builder
    {
        return $query->whereHas('adverts', function ($query) use ($advertId) {
            $query->where('advert_id', $advertId);
        });
    }

    /**
     * Registers a new user with the given email and password.
     *
     * @param string $email The email address of the user.
     * @param string $password The password for the user.
     * @return self The newly created user instance.
     */
    public static function register(string $email, string $password): self
    {
        return static::create([
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function adverts()
    {
        return $this->belongsToMany(Advert::class);
    }
}
