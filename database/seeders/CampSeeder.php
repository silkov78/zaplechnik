<?php

namespace Database\Seeders;

use App\Models\Camp;
use Clickbar\Magellan\IO\Parser\Geojson\GeojsonParser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CampSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(GeojsonParser $parser): void
    {
        $data = File::json(database_path('seeders/data/campings_seed.geojson'));

        foreach ($data['features'] as $campData) {
            $campArray = $campData['properties'];
            $campArray['coordinates'] = $parser->parse($campData['geometry']);

            Camp::create($campArray);
        }
    }
}
