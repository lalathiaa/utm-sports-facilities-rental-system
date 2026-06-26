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

        DB::transaction(function () use ($expiredGroups) {
            // Expire every slot in each expired group
            Booking::whereIn('booking_group_id', $expiredGroups)
                ->where('status', 'pending_payment')
                ->update(['status' => 'failed']);

            // Expire the corresponding payment records
            Payment::whereIn('booking_group_id', $expiredGroups)
                ->where('payment_status', 'pending')
                ->update(['payment_status' => 'failed']);
        });

        $this->info("Expired {$expiredGroups->count()} booking group(s).");

        return self::SUCCESS;
    }
}
