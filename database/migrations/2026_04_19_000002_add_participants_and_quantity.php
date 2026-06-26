<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add required_participants to facilities
        Schema::table('facilities', function (Blueprint $table) {
            $table->unsignedTinyInteger('required_participants')->default(1)->after('image');
        });

        // Add quantity to equipment
        Schema::table('equipment', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1)->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn('required_participants');
        });
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};