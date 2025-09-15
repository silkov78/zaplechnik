<?php

namespace Database\Seeders;

use App\Models\Campground;
use Clickbar\Magellan\IO\Parser\Geojson\GeojsonParser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CampgroundSeeder extends Seeder
{
    public function run(GeojsonParser $parser): void
    {
        $seederGeoJsonPath = database_path('seeders/data/campgrounds.geojson');
        $featuresArray = File::json($seederGeoJsonPath)['features'];

        $osmDatabaseMapping = [
            'name' => 'osm_name',
            'geometry' => 'osm_geometry',
            'description' => 'osm_description',
            'website' => 'osm_website',
            'fee' => 'osm_fee',
            'fireplace' => 'osm_fireplace',
            'picnic_table' => 'osm_picnic_table',
            'toilets' => 'osm_toilets',
            'access' => 'osm_access',
            'image' => 'osm_image',
        ];

        foreach ($featuresArray as $feature) {
            $campArray = $feature['properties'];
            $campArray['geometry'] = $parser->parse($feature['geometry']);

            foreach ($osmDatabaseMapping as $osmName => $databaseName) {
                if (array_key_exists($osmName, $campArray)) {
                    $campArray[$databaseName] = $campArray[$osmName];

                    unset($campArray[$osmName]);
                }
            }

            Campground::create($campArray);
        }
    }
}
