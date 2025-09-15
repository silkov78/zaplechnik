<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Collection;

trait GeneratesGeoJsonArray
{
    /**
     * Transform geographical model collection in GeoJson featureCollection array
     *
     * Each feature includes:
     * - properties: all model attributes except geometry field
     * - geometry: geometry field in standard geometry description
     *
     * @param Collection $modelCollection Model collection of Geographical entity
     * @param string $geometryField Name of geometry field in model
     *
     * @see https://geojson.org/ GeoJson specification
     */
    public function getFeatureCollectionArray(
        Collection $modelCollection,
        string $geometryField = 'osm_geometry',
    ): array
    {
        return [
            'type' => 'FeatureCollection',
            'features' => $modelCollection->map(function ($feature) use ($geometryField) {
                $featureProperties = $feature->toArray();
                unset($featureProperties[$geometryField]);
                unset($featureProperties['created_at']);
                unset($featureProperties['updated_at']);

                return [
                    'type' => 'Feature',
                    'properties' => $featureProperties,
                    'geometry' => $feature->osm_geometry,
                ];
            })->toArray(),
        ];
    }
}