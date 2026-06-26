<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MySQL error 1553 prevents dropping a unique index when any FK exists on the same
     * table, even if the FK does not use that index. The workaround is to temporarily
     * drop the FK constraint, drop the unique index, then restore the FK.
     *
     * After this, double-booking is enforced entirely at the application layer
     * (BookingController checks confirmed + cancel_requested + pending_payment rows).
     */
    public function up(): void
    {
        // 1. Drop the FK that trips MySQL's false 1553 error
        DB::statement('ALTER TABLE bookings DROP FOREIGN KEY bookings_facility_id_foreign');

        // 2. Now drop the unique index safely
        DB::statement('ALTER TABLE bookings DROP INDEX unique_facility_slot');

        // 3. Recreate the FK (without the unique index it was falsely "protecting")
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT bookings_facility_id_foreign
            FOREIGN KEY (facility_id) REFERENCES facilities (id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE bookings DROP FOREIGN KEY bookings_facility_id_foreign');
        DB::statement('ALTER TABLE bookings ADD UNIQUE KEY unique_facility_slot (facility_id, booking_date, slot_start)');
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT bookings_facility_id_foreign
            FOREIGN KEY (facility_id) REFERENCES facilities (id) ON DELETE CASCADE');
    }
};

