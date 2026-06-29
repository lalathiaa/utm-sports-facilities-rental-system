<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook;

class PaymentController extends Controller
{
    // ─── Step 1: Show payment summary page ───────────────────────────────────

    public function prepare(Booking $booking): View|RedirectResponse
    {
        Booking::expireStaleBookings();
        $booking->refresh();

        abort_if($booking->user_id !== Auth::id(), 403);

        if ($booking->isPaymentExpired()) {
            return redirect()->route('bookings.my')
                ->with('error', 'Your payment window has expired. The slot has been released.');
        }

        if (! $booking->isPendingPayment()) {
            return redirect()->route('bookings.my')
                ->with('error', 'This booking is not awaiting payment.');
        }

        $booking->load('facility');

        $relatedBookings = Booking::where('booking_group_id', $booking->booking_group_id)
            ->where('status', 'pending_payment')
            ->orderBy('slot_start')
            ->get();

        return view('payment.checkout', compact('booking', 'relatedBookings'));
    }

    // ─── Step 2: Create Stripe session and redirect ───────────────────────────

    public function checkout(Booking $booking): RedirectResponse
    {
        Booking::expireStaleBookings();
        $booking->refresh();

        abort_if($booking->user_id !== Auth::id(), 403);

        if (! $booking->isPendingPayment()) {
            return redirect()->route('bookings.my')
                ->with('error', 'This booking is not awaiting payment.');
        }

        if ($booking->isPaymentExpired()) {
            return redirect()->route('bookings.my')
                ->with('error', 'Your payment window has expired. The slot has been released.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $booking->load('facility');

        // Load all slots in this booking group
        $relatedBookings = Booking::where('booking_group_id', $booking->booking_group_id)
            ->where('status', 'pending_payment')
            ->get();

        $totalAmount  = $relatedBookings->sum('total_price');
        $slotCount    = $relatedBookings->count();
        $facilityName = $booking->facility->name;
        $bookingDate  = $booking->booking_date->format('d M Y');

        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'mode'                 => 'payment',
                'line_items'           => [[
                    'price_data' => [
                        'currency'     => 'myr',
                        'unit_amount'  => (int) round($totalAmount * 100),
                        'product_data' => [
                            'name'        => "{$facilityName} — {$slotCount} slot(s)",
                            'description' => "Booking for {$bookingDate} · UTM Sports Facilities",
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'success_url'    => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'     => route('payment.cancel', $booking),
                // Stripe requires expires_at to be at least 30 minutes from now.
                // We use 30 min here; our own 10-min expiry is enforced separately
                // via payment_expires_at on the booking and the payments:expire command.
                'expires_at'     => now()->addMinutes(30)->timestamp,
                'metadata'       => [
                    'booking_group_id' => $booking->booking_group_id,
                    'user_id'          => Auth::id(),
                ],
                'customer_email' => Auth::user()->email,
            ]);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error('Stripe authentication failed: ' . $e->getMessage());
            return redirect()->route('payment.prepare', $booking)
                ->with('error', 'Payment service configuration error. Please contact support.');
        } catch (\Exception $e) {
            Log::error('Stripe session creation failed: ' . $e->getMessage());
            return redirect()->route('payment.prepare', $booking)
                ->with('error', 'Could not connect to payment service. Please try again in a moment.');
        }

        // Upsert the payment record (handles retry if user comes back to this page)
        Payment::updateOrCreate(
            ['booking_group_id' => $booking->booking_group_id],
            [
                'stripe_session_id' => $session->id,
                'payment_status'    => 'pending',
                'amount'            => $totalAmount,
            ]
        );

        return redirect()->away($session->url);

    }

    // ─── Step 3a: Stripe redirects back on success ────────────────────────────

    public function success(Request $request): RedirectResponse
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('bookings.my')->with('error', 'Invalid payment session.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeSession::retrieve([
                'id'     => $sessionId,
                'expand' => ['payment_intent'],
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe session retrieval failed: ' . $e->getMessage());
            return redirect()->route('bookings.my')
                ->with('error', 'Could not verify your payment. Please contact support.');
        }

        if ($session->payment_status !== 'paid') {
            return redirect()->route('bookings.my')
                ->with('error', 'Payment was not completed. Please try again.');
        }

        $groupId = $session->metadata->booking_group_id ?? null;
        $booking = Booking::where('booking_group_id', $groupId)->first();

        if (! $booking) {
            return redirect()->route('bookings.my')->with('error', 'Booking not found.');
        }

        abort_if($booking->user_id !== Auth::id(), 403);

        // Already confirmed (webhook beat the return URL) — go straight to receipt
        if ($booking->isConfirmed()) {
            return redirect()->route('payment.receipt', $booking)
                ->with('success', 'Payment successful! Your booking is confirmed.');
        }

        $this->confirmPayment($groupId, $session);

        return redirect()->route('payment.receipt', $booking)
            ->with('success', 'Payment successful! Your booking is confirmed.');
    }

    // ─── Step 3b: Stripe redirects back on cancel ─────────────────────────────

    public function cancel(Booking $booking): View
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        $booking->load('facility');

        return view('payment.failed', compact('booking'));
    }

    // ─── Stripe Webhook (server-to-server async) ──────────────────────────────

    public function webhook(Request $request): \Illuminate\Http\Response
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature mismatch: ' . $e->getMessage());
            return response('Invalid signature', 400);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook invalid payload: ' . $e->getMessage());
            return response('Invalid payload', 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleSessionCompleted($event->data->object),
            'checkout.session.expired'   => $this->handleSessionExpired($event->data->object),
            default                      => null,
        };

        return response('OK', 200);
    }

    // ─── Payment Receipt ──────────────────────────────────────────────────────

    public function receipt(Booking $booking): View
    {
        $user = Auth::user();
        if (! $user->isRentalOfficer() && ! $user->isAdmin() && $booking->user_id !== $user->id) {
            abort(403);
        }

        $booking->load(['facility', 'equipment', 'participants', 'user', 'payment']);

        $relatedBookings = Booking::with(['equipment', 'participants'])
            ->where('booking_group_id', $booking->booking_group_id)
            ->orderBy('slot_start')
            ->get();

        return view('payment.receipt', compact('booking', 'relatedBookings'));
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Confirm payment and all related slot bookings atomically.
     */
    private function confirmPayment(int $groupId, object $session): void
    {
        $paymentIntent = $session->payment_intent;

        DB::transaction(function () use ($groupId, $session, $paymentIntent) {
            // Confirm every slot in this booking group
            Booking::where('booking_group_id', $groupId)
                ->where('status', 'pending_payment')
                ->update(['status' => 'confirmed']);

            // Update the payment record
            Payment::where('booking_group_id', $groupId)
                ->update([
                    'payment_status' => 'completed',
                    'transaction_id' => is_string($paymentIntent)
                        ? $paymentIntent
                        : ($paymentIntent->id ?? null),
                    'payment_method' => 'card',
                    'paid_at'        => now(),
                ]);
        });
    }

    private function handleSessionCompleted(object $session): void
    {
        $groupId = $session->metadata->booking_group_id ?? null;
        if (! $groupId) return;

        $booking = Booking::where('booking_group_id', $groupId)->first();

        if ($booking && $booking->isPendingPayment()) {
            $this->confirmPayment((int) $groupId, $session);
        }
    }

    private function handleSessionExpired(object $session): void
    {
        $groupId = $session->metadata->booking_group_id ?? null;
        if (! $groupId) return;

        DB::transaction(function () use ($groupId) {
            // Expire every slot in the group
            Booking::where('booking_group_id', $groupId)
                ->where('status', 'pending_payment')
                ->update(['status' => 'failed']);

            Payment::where('booking_group_id', $groupId)
                ->where('payment_status', 'pending')
                ->update(['payment_status' => 'failed']);
        });
    }
}
