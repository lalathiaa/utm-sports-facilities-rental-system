<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirePayments extends Command
{
    protected $signature   = 'payments:expire';
    protected $description = 'Release slots for booking groups whose payment window has expired.';

    public function handle(): int
    {
        // Find distinct expired groups (only need one booking per group to get the group ID)
        $expiredGroups = Booking::select('booking_group_id')
            ->where('status', 'pending_payment')
            ->where('payment_expires_at', '<', now())
            ->whereNotNull('booking_group_id')
            ->distinct()
            ->pluck('booking_group_id');

        if ($expiredGroups->isEmpty()) {
            $this->info('No expired pending payments found.');
            return self::SUCCESS;
        }

        $totalSlotsReleased = 0;

        DB::transaction(function () use ($expiredGroups, &$totalSlotsReleased) {
            // 1. Expire all bookings that are expired (including null booking_group_id)
            $totalSlotsReleased = Booking::where('status', 'pending_payment')
                ->where('payment_expires_at', '<', now())
                ->update(['status' => 'failed']);

            // 2. Expire the corresponding payment records
            if ($expiredGroups->isNotEmpty()) {
                Payment::whereIn('booking_group_id', $expiredGroups)
                    ->where('payment_status', 'pending')
                    ->update(['payment_status' => 'failed']);
            }
        });

        $this->info("Expired {$expiredGroups->count()} booking group(s), releasing {$totalSlotsReleased} slot(s) back to available.");

        return self::SUCCESS;
    }
}
