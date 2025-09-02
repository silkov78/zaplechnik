<?php

use Clickbar\Magellan\Schema\MagellanSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up(): void
    {
        MagellanSchema::enablePostgisIfNotExists($this->connection);
    }

    public function down(): void
    {
//        MagellanSchema::disablePostgisIfExists($this->connection);
        DB::statement('DROP EXTENSION IF EXISTS postgis_tiger_geocoder;');
        DB::statement('DROP EXTENSION IF EXISTS postgis_topology;');
        DB::statement('DROP EXTENSION IF EXISTS postgis;');
    }
};
