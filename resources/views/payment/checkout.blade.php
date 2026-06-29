<x-app-layout>
    <x-slot name="header">Complete Your Payment</x-slot>

    <div style="max-width:800px;margin:0 auto;padding:0 16px;">

        <div style="display:flex;align-items:center;margin-bottom:20px;">
            <a href="{{ route('bookings.my') }}"
               style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--slate-400);text-decoration:none;font-weight:500;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to My Bookings
            </a>
        </div>

        {{-- Info alert --}}
        @if(session('info'))
            <div class="utm-alert animate-in" style="margin-bottom:20px;background:rgba(37,99,235,.07);border-color:rgba(37,99,235,.18);color:#1e40af;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('info') }}
            </div>
        @endif

        {{-- Error alert --}}
        @if(session('error'))
            <div class="utm-alert utm-alert-error animate-in" style="margin-bottom:20px;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="utm-card animate-in" style="overflow:hidden;">

            {{-- Header --}}
            <div class="slip-header">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;position:relative;z-index:1;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:44px;height:44px;background:white;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg viewBox="0 0 40 20" width="32" height="16">
                                <text x="50%" y="75%" dominant-baseline="middle" text-anchor="middle"
                                      font-family="Georgia,serif" font-weight="700" font-size="11" fill="#8B0000">UTM</text>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:16px;font-weight:700;color:white;">Secure Payment</div>
                            <div style="font-size:12px;color:rgba(201,168,76,.85);margin-top:2px;">UTM Sports Facilities Rental System</div>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:11px;color:rgba(255,255,255,.50);text-transform:uppercase;letter-spacing:.08em;">Reference</div>
                        <div style="font-size:20px;font-weight:800;color:white;font-family:'Plus Jakarta Sans',sans-serif;">
                            #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Countdown Timer --}}
            <div style="padding:14px 32px;background:rgba(234,179,8,.06);border-bottom:1px solid rgba(234,179,8,.15);display:flex;align-items:center;justify-content:center;gap:10px;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#b45309;flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span style="font-size:13px;font-weight:600;color:#b45309;">
                    Your slot is reserved for <span id="countdown" style="font-variant-numeric:tabular-nums;">10:00</span>. Complete payment before it expires.
                </span>
            </div>

            <div style="padding:28px 32px;display:flex;flex-direction:column;gap:20px;">

                {{-- Booking Summary --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:5px;">Facility</div>
                        <div style="font-size:15px;font-weight:700;color:var(--slate-800);">{{ $booking->facility->name }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:5px;">Booking Date</div>
                        <div style="font-size:15px;font-weight:700;color:var(--slate-800);">{{ $booking->booking_date->format('d M Y') }}</div>
                    </div>
                </div>

                {{-- Slots --}}
                <div>
                    <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:10px;">Booked Slot(s)</div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach($relatedBookings as $rb)
                            <span style="padding:6px 14px;background:rgba(139,0,0,.06);border:1px solid rgba(139,0,0,.12);border-radius:100px;font-size:13px;font-weight:600;color:var(--utm-maroon);">
                                {{ $rb->slotLabel() }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid var(--slate-100);margin:0;">

                {{-- Total --}}
                <div style="background:var(--slate-50);border-radius:var(--radius-md);padding:18px 20px;border:1px solid var(--slate-200);">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:14px;font-weight:600;color:var(--slate-600);">Total Amount Due</span>
                        <span style="font-size:22px;font-weight:800;color:var(--utm-maroon);">
                            RM {{ number_format($relatedBookings->sum('total_price'), 2) }}
                        </span>
                    </div>
                    <div style="margin-top:8px;font-size:12px;color:var(--slate-400);">
                        Powered by Stripe · Secure encrypted payment
                    </div>
                </div>

                {{-- Stripe CTA --}}
                <a href="{{ route('payment.checkout', $booking) }}"
                   id="pay-btn"
                   style="display:flex;align-items:center;justify-content:center;gap:10px;background:var(--utm-maroon);color:white;padding:14px 24px;border-radius:var(--radius-md);font-size:15px;font-weight:700;text-decoration:none;transition:opacity .2s;border:none;cursor:pointer;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                    Proceed to Stripe Payment
                </a>

                <p style="text-align:center;font-size:12px;color:var(--slate-400);margin:0;">
                    You will be redirected to Stripe's secure payment page.
                    <br>We accept Visa, Mastercard and other major cards.
                </p>

            </div>

            {{-- Footer --}}
            <div style="padding:14px 32px;background:var(--slate-50);border-top:1px solid var(--slate-100);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
                <a href="{{ route('bookings.my') }}" style="font-size:12px;color:var(--slate-400);text-decoration:none;">← Back to My Bookings</a>
                <span style="font-size:11.5px;color:var(--slate-400);">Universiti Teknologi Malaysia</span>
            </div>

        </div>
    </div>

    <script>
    (function () {
        // Calculate remaining time from payment_expires_at
        const expiresAt = new Date("{{ $booking->payment_expires_at?->toIso8601String() }}");
        const countdownEl = document.getElementById('countdown');

        function tick() {
            const remaining = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));
            const m = Math.floor(remaining / 60).toString().padStart(2, '0');
            const s = (remaining % 60).toString().padStart(2, '0');
            if (countdownEl) countdownEl.textContent = `${m}:${s}`;
            if (remaining <= 0) {
                if (countdownEl) countdownEl.textContent = 'Expired';
                const btn = document.getElementById('pay-btn');
                if (btn) {
                    btn.style.opacity = '0.4';
                    btn.style.pointerEvents = 'none';
                    btn.textContent = 'Payment window expired';
                }
                return;
            }
            setTimeout(tick, 1000);
        }
        tick();
    })();
    </script>

</x-app-layout>
