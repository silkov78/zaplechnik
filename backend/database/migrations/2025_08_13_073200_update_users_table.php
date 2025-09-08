<?php

use App\Enums\Gender;
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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id', 'user_id');
            $table->string('name', length: 50)->unique()->change();
            $table->enum('gender', Gender::values())->nullable();
            $table->string('avatar')->nullable();
            $table->string('bio')->nullable();
            $table->string('telegram', length: 100)->nullable();
            $table->boolean('is_private')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
           $table->dropColumn(['avatar', 'telegram', 'bio', 'gender', 'is_private']);
           $table->dropUnique('users_name_unique');
           $table->renameColumn('user_id', 'id');
        });
    }
};
