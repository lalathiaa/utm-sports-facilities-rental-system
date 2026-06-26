<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove the redundant stripe_session_id column from bookings.
     * The session ID is already stored in the payments table and is the
     * single source of truth. The booking_group_id links all slots from
     * the same booking session together reliably.
     *
     * Also adds booking_group_id to group multiple slot bookings:
     *   - Set to the first booking's ID within a session
     *   - Allows reliable lookup of all related slots without fragile
     *     user/facility/date/status queries
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Group multiple slot bookings together (self-referential: first booking's ID)
            $table->unsignedBigInteger('booking_group_id')->nullable()->after('id');

            // Remove the redundant column — stripe_session_id lives in payments only
            $table->dropColumn('stripe_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('booking_group_id');
            $table->string('stripe_session_id')->nullable()->after('cancellation_reason');
        });
    }
};
