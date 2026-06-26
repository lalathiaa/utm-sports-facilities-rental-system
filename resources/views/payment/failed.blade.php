<x-app-layout>
    <x-slot name="header">Payment Failed</x-slot>

    <div style="max-width:560px;">

        <div class="utm-card animate-in" style="overflow:hidden;text-align:center;">

            {{-- Header --}}
            <div style="padding:40px 32px 32px;display:flex;flex-direction:column;align-items:center;gap:16px;">

                {{-- Error icon --}}
                <div style="width:72px;height:72px;border-radius:50%;background:rgba(220,38,38,.08);border:2px solid rgba(220,38,38,.15);display:flex;align-items:center;justify-content:center;">
                    <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="color:#dc2626;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>

                <div>
                    <div style="font-size:20px;font-weight:800;color:var(--slate-800);margin-bottom:6px;">Payment Not Completed</div>
                    <div style="font-size:14px;color:var(--slate-500);line-height:1.6;">
                        You cancelled the payment or it was not processed.<br>
                        Your slot reservation is still active — you can try again.
                    </div>
                </div>

                {{-- Expiry notice --}}
                @if($booking->payment_expires_at && $booking->payment_expires_at->isFuture())
                    <div style="background:rgba(234,179,8,.07);border:1px solid rgba(234,179,8,.2);border-radius:var(--radius-md);padding:12px 20px;font-size:13px;color:#92400e;display:flex;align-items:center;gap:8px;">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Slot reserved until <strong>{{ $booking->payment_expires_at->format('h:i A') }}</strong>
                    </div>
                @else
                    <div style="background:rgba(220,38,38,.06);border:1px solid rgba(220,38,38,.15);border-radius:var(--radius-md);padding:12px 20px;font-size:13px;color:#dc2626;display:flex;align-items:center;gap:8px;">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Your payment window has expired. Please make a new booking.
                    </div>
                @endif

                {{-- Booking summary --}}
                <div style="width:100%;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:var(--radius-md);padding:16px 20px;text-align:left;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:13px;">
                        <div>
                            <div style="color:var(--slate-400);font-size:10px;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Facility</div>
                            <div style="font-weight:600;color:var(--slate-700);">{{ $booking->facility->name }}</div>
                        </div>
                        <div>
                            <div style="color:var(--slate-400);font-size:10px;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Date</div>
                            <div style="font-weight:600;color:var(--slate-700);">{{ $booking->booking_date->format('d M Y') }}</div>
                        </div>
                        <div>
                            <div style="color:var(--slate-400);font-size:10px;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Slot</div>
                            <div style="font-weight:600;color:var(--slate-700);">{{ $booking->slotLabel() }}</div>
                        </div>
                        <div>
                            <div style="color:var(--slate-400);font-size:10px;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Amount</div>
                            <div style="font-weight:700;color:var(--utm-maroon);">RM {{ number_format($booking->total_price, 2) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex;flex-direction:column;gap:10px;width:100%;">
                    @if($booking->isPendingPayment() && !$booking->isPaymentExpired())
                        <a href="{{ route('payment.prepare', $booking) }}"
                           style="display:flex;align-items:center;justify-content:center;gap:8px;background:var(--utm-maroon);color:white;padding:13px 24px;border-radius:var(--radius-md);font-size:14px;font-weight:700;text-decoration:none;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                            Try Payment Again
                        </a>
                    @endif
                    <a href="{{ route('bookings.my') }}"
                       style="display:flex;align-items:center;justify-content:center;gap:8px;background:white;color:var(--slate-600);padding:12px 24px;border-radius:var(--radius-md);font-size:14px;font-weight:600;text-decoration:none;border:1px solid var(--slate-200);">
                        Back to My Bookings
                    </a>
                </div>

            </div>

            {{-- Footer --}}
            <div style="padding:14px 32px;background:var(--slate-50);border-top:1px solid var(--slate-100);font-size:11.5px;color:var(--slate-400);text-align:center;">
                Universiti Teknologi Malaysia · UTM Sports Facilities Rental System
            </div>

        </div>
    </div>

</x-app-layout>
