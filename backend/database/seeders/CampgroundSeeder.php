<?php

namespace Database\Seeders;

use App\Models\Campground;
use Clickbar\Magellan\IO\Parser\Geojson\GeojsonParser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CampgroundSeeder extends Seeder
{
    private const string CAMPGROUNDS_FILE = 'seeders/data/camp_site_geocoded.geojson';

    public function __construct(
        public readonly GeojsonParser $parser
    ) {}

    public function run(): void
    {
        $this->loadFromGeoJson();
    }

    public function loadFromGeoJson(): void
    {
        $seederGeoJsonPath = database_path(self::CAMPGROUNDS_FILE);
        $featuresArray = File::json($seederGeoJsonPath)['features'];

        $osmDatabaseMapping = [
            '@id' => 'osm_id',
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
            'district:be' => 'script_district',
            'region:be' => 'script_region',
        ];

        foreach ($featuresArray as $feature) {
            $campArray = [];

            $campArray['osm_geometry'] = $this->parser->parse($feature['geometry']);

            foreach ($feature['properties'] as $featureProperty => $propertyValue) {
                if (!array_key_exists($featureProperty, $osmDatabaseMapping)) {
                    continue;
                }

                $databaseName = $osmDatabaseMapping[$featureProperty];

                $campArray[$databaseName] = $propertyValue;
            }

            Campground::create($campArray);
        }
    }
}
