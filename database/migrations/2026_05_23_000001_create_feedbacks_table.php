<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_group_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating')->unsigned();          // 0–5
            $table->string('title', 255);
            $table->text('message');
            $table->timestamp('feedback_time');
            $table->timestamps();

            // One feedback per booking group
            $table->unique('booking_group_id');

            // FK to the first booking in the group (booking_group_id = booking.id)
            $table->foreign('booking_group_id')
                  ->references('id')
                  ->on('bookings')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
