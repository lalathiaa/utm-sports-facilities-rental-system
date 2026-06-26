<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            $table->date('booking_date');
            $table->time('slot_start');
            $table->time('slot_end');
            $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['facility_id', 'booking_date', 'slot_start'], 'unique_facility_slot');
        });

        Schema::create('booking_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->decimal('price_snapshot', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_equipment');
        Schema::dropIfExists('bookings');
    }
};