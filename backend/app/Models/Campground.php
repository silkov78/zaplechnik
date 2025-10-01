<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Campground extends Model
{
    use HasFactory;

    protected $primaryKey = 'campground_id';

    protected $fillable = [
        'osm_id', 'osm_geometry', 'osm_name', 'osm_description',
        'osm_website', 'osm_fee', 'osm_fireplace', 'osm_picnic_table',
        'osm_toilets', 'osm_access', 'osm_image',
        // TODO: remove from fillable after bulk load
        'script_region',
        'script_district',
    ];

    protected $casts = [
        'osm_geometry' => Point::class,
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'campground_id', 'campground_id');
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            Visit::class,
            'campground_id',
            'user_id',
            'campground_id',
            'user_id',
        );
    }
}
