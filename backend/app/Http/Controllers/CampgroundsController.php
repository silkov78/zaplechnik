<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesGeoJsonArray;
use App\Models\Campground;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CampgroundsController extends Controller
{
    use GeneratesGeoJsonArray;

    /**
     * Returns campgrounds as GeoJson featureCollection.
     */
    public function index(): JsonResponse
    {
        $campgroundsArray = Cache::remember(
            'campgrounds_geojson', 3600, function () {
                return $this->getFeatureCollectionArray(
                    Campground::all(), 'osm_geometry'
                );
            }
        );

        return response()->json($campgroundsArray);
    }

    /**
     * Returns campgrounds visited by authenticated user.
     */
    public function indexVisited(Request $request): JsonResponse
    {
        $visitedCampgroundsArray = $this->getFeatureCollectionArray(
            $request->user()->campgrounds,
            'osm_geometry',
        );

        return response()->json($visitedCampgroundsArray);
    }
}
