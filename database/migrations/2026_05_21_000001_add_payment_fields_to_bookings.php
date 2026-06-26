<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires dropping and recreating the enum to add new values
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'confirmed',
            'cancelled',
            'cancel_requested',
            'pending_payment',
            'failed'
        ) NOT NULL DEFAULT 'confirmed'");

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('stripe_session_id')->nullable()->after('cancellation_reason');
            $table->timestamp('payment_expires_at')->nullable()->after('stripe_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['stripe_session_id', 'payment_expires_at']);
        });

        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'confirmed',
            'cancelled',
            'cancel_requested'
        ) NOT NULL DEFAULT 'confirmed'");
    }
};
