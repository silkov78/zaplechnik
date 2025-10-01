<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    use HasFactory;

    protected $primaryKey = 'visit_id';

    protected $fillable = [
        'user_id',
        'campground_id',
        'visit_date',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function campground(): BelongsTo
    {
        return $this->belongsTo(
            Campground::class,
            'campground_id',
            'campground_id'
        );
    }

    protected static function booted(): void
    {
        static::created(function ($visit) {
            User::where('user_id', $visit->user_id)
                ->increment('visits_count');
        });
    }
}
