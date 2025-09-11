<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesGeoJsonArray;
use App\Models\Campground;
use Illuminate\Http\JsonResponse;

class CampgroundsController extends Controller
{
    use GeneratesGeoJsonArray;

    /**
     * Returns campgrounds as GeoJson featureCollection
     */
    public function index(): JsonResponse
    {
        $campgroundsArray = $this->getFeatureCollectionArray(
            Campground::all(), 'osm_geometry'
        );

        return response()->json($campgroundsArray);
    }
}
