<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update booking_equipment pivot to include quantity
        Schema::table('booking_equipment', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1)->after('price_snapshot');
        });

        // Add cancellation_request fields to bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['confirmed', 'cancelled', 'cancel_requested'])
                  ->default('confirmed')
                  ->change();
            $table->text('cancellation_reason')->nullable()->after('total_price');
        });

        // Booking participants table
        Schema::create('booking_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->boolean('is_primary')->default(false); // true = the person who booked
            $table->string('fullname');
            $table->string('ic_number');
            $table->string('matric_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_participants');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('cancellation_reason');
            $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed')->change();
        });

        Schema::table('booking_equipment', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};