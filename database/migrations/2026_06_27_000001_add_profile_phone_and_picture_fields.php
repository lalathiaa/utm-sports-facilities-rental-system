<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
            $table->string('profile_picture')->nullable();
        });

        Schema::table('booking_participants', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'profile_picture']);
        });

        Schema::table('booking_participants', function (Blueprint $table) {
            $table->dropColumn('phone_number');
        });
    }
};
