<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'telegram',
        'bio',
    ];

    protected $guarded = [
        'visits_count',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'user_id');
    }

    public function campgrounds(): HasManyThrough
    {
        return $this->hasManyThrough(
            Campground::class,
            Visit::class,
            'user_id',
            'campground_id',
        );
    }
}
