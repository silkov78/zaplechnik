<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Campground extends Model
{
    protected $primaryKey = 'campground_id';

    protected $fillable = [
        'coordinates',
        'osm_id',
        'name',
        'description',
        'website',
        'fee',
        'firewood',
        'fireplace',
        'picnic_table',
        'toilets',
        'access',
    ];

    protected $casts = [
        'coordinates' => Point::class,
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'campground_id', 'campground_id');
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class, Visit::class, 'user_id', 'user_id'
        );
    }
}
