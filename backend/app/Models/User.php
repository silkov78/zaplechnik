<?php

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
            'user_id',
            'campground_id'
        );
    }

    public function getRank(): string
    {
        if ($this->gender === Gender::FEMALE) {
            return match (true) {
                $this->visits_count < 1 => 'Домоседка',
                $this->visits_count < 6 => 'Новичок',
                $this->visits_count < 16 => 'Идущая к реке',
                $this->visits_count < 31 => 'Туристка',
                $this->visits_count < 51 => 'Следопыт',
                $this->visits_count < 101 => 'Кочевница',
                $this->visits_count >= 101 => 'Агафья Лыкова',
                default => 'Домосед',
            };
        }

        return match (true) {
            $this->visits_count < 1 => 'Домосед',
            $this->visits_count < 6 => 'Новичок',
            $this->visits_count < 16 => 'Идущий к реке',
            $this->visits_count < 31 => 'Турист',
            $this->visits_count < 51 => 'Следопыт',
            $this->visits_count < 101 => 'Кочевник',
            $this->visits_count >= 101 => 'Беар Гриллс',
            default => 'Домосед',
        };
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->avatar
                ? asset('storage/avatars/' . $this->avatar)
                : asset('images/default-avatar.png')
        );
    }
}
