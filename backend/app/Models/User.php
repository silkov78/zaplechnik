<?php

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name', 'email', 'gender', 'password',
        'avatar', 'telegram', 'bio', 'is_private',
    ];

    protected $hidden = [
        'password', 'remember_token', 'email_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gender' => Gender::class,
            'is_private' => 'boolean',
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
