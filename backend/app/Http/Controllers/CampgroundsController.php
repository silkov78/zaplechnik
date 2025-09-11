<?php

namespace App\Http\Controllers;

use App\Models\Campground;
use Illuminate\Http\JsonResponse;

class CampgroundsController extends Controller
{
    /**
     * Returns campgrounds as GeoJson FeatureCollection
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $this->getFeaturesGeoJson(),
        ]);
    }

    /**
     * Transform campgrounds to standard GeoJson format
     *
     * Each feature includes:
     * - properties: all model attributes except osm_geometry
     * - geometry: osm_geometry as standard geometry description
     *
     * @see https://geojson.org/ GeoJson specification
     */
    public function getFeaturesGeoJson(): array
    {
        // TODO: refactor, when all fields will be included in campgrounds table
        return Campground::all()->map(function ($feature) {
            $featureProperties = $feature->toArray();
            unset($featureProperties['osm_geometry']);

            return [
                'type' => 'Feature',
                'properties' => $featureProperties,
                'geometry' => $feature->osm_geometry,
            ];
        })->toArray();
    }
}
