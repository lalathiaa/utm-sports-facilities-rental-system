<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            $table->date('closure_date');
            $table->time('slot_start')->nullable(); // null = entire day closed
            $table->string('reason')->nullable();
            $table->timestamps();

            // Prevent duplicate closure entries for the same slot
            $table->unique(['facility_id', 'closure_date', 'slot_start'], 'unique_facility_closure_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_closures');
    }
};