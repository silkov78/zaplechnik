<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campgrounds', function (Blueprint $table) {
            $table->id('campground_id');
            $table->string('osm_id', 20);
            $table->magellanPoint('osm_geometry', 4326);
            $table->string('osm_name')->nullable();
            $table->text('osm_description')->nullable();
            $table->string('osm_website')->nullable();
            $table->string('osm_fee', 100)->nullable();
            $table->string('osm_fireplace', 15)->nullable();
            $table->string('osm_picnic_table', 15)->nullable();
            $table->string('osm_toilets', 15)->nullable();
            $table->string('osm_access', 100)->nullable();
            $table->string('firewood', 15)->nullable();
            $table->string('script_region', 50)->nullable();
            $table->string('script_district', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campgrounds');
    }
};
