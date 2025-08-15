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
            $table->magellanPoint('coordinates', 4326);
            $table->string('osm_id', 20)->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('fee', 100)->nullable();
            $table->string('firewood', 15)->nullable();
            $table->string('fireplace', 15)->nullable();
            $table->string('picnic_table', 15)->nullable();
            $table->string('toilets', 15)->nullable();
            $table->string('availability', 100)->nullable();
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
