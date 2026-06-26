<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replace payments.booking_id (which only pointed to the first slot's booking)
     * with booking_group_id so the payment record is clearly associated with
     * the entire booking group, not just one slot.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop old FK and column
            $table->dropForeign(['booking_id']);
            $table->dropColumn('booking_id');

            // Add booking_group_id — references the first booking ID in the group
            $table->unsignedBigInteger('booking_group_id')->after('id');
            $table->index('booking_group_id', 'payments_booking_group_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_booking_group_id_index');
            $table->dropColumn('booking_group_id');
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
        });
    }
};
