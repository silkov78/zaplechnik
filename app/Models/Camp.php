<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Model;

class Camp extends Model
{
    protected $primaryKey = 'camp_id';

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
        'availability',
    ];

    protected $casts = [
        'coordinates' => Point::class,
    ];
}
