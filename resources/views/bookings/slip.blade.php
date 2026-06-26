<x-app-layout>
    <x-slot name="header">Booking Slip</x-slot>

    <div style="max-width:800px;margin:0 auto;">

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
            @auth
                @if(Auth::user()->isRentalOfficer())
                    <a href="{{ route('bookings.all') }}"
                       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--slate-400);text-decoration:none;font-weight:500;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Bookings
                    </a>
                @else
                    <a href="{{ route('bookings.my') }}"
                       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--slate-400);text-decoration:none;font-weight:500;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to My Bookings
                    </a>
                @endif
            @endauth
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                @if($booking->isConfirmed() && $booking->payment?->isCompleted())
                    <a href="{{ route('payment.receipt', $booking) }}" class="btn btn-outline btn-sm" style="color:#059669;border-color:#059669;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View Receipt
                    </a>
                @endif
                <button onclick="window.print()" class="btn btn-outline btn-sm">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Slip
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="utm-alert utm-alert-success animate-in">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="utm-card animate-in" style="overflow:hidden;">

            {{-- Slip Header --}}
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
                            <div style="font-size:16px;font-weight:700;color:white;">Booking Confirmation</div>
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

            {{-- Status Strip --}}
            <div style="padding:12px 32px;background:{{ $booking->isConfirmed() ? 'rgba(5,150,105,.06)' : ($booking->isCancelRequested() ? 'rgba(217,119,6,.06)' : ($booking->isPendingPayment() ? 'rgba(37,99,235,.06)' : 'rgba(220,38,38,.06)')) }};border-bottom:1px solid var(--slate-100);display:flex;align-items:center;justify-content:center;gap:10px;">
                <span class="badge {{ $booking->isConfirmed() ? 'badge-confirmed' : ($booking->isCancelRequested() ? 'badge-requested' : ($booking->isPendingPayment() ? 'badge-pending' : 'badge-cancelled')) }}"
                      style="font-size:13px;padding:5px 14px;">
                    {{ $booking->statusLabel() }}
                </span>
            </div>

            <div style="padding:28px 32px;display:flex;flex-direction:column;gap:24px;">

                {{-- Facility & Date --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:5px;">Facility</div>
                        <div style="font-size:15px;font-weight:700;color:var(--slate-800);">{{ $booking->facility->name }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:5px;">Booking Date</div>
                        <div style="font-size:15px;font-weight:700;color:var(--slate-800);">{{ $booking->booking_date->format('d M Y') }}</div>
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid var(--slate-100);margin:0;">

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

                {{-- Equipment --}}
                @if($booking->equipment->isNotEmpty())
                    <div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:10px;">Equipment</div>
                        <div style="display:flex;flex-direction:column;gap:6px;">
                            @foreach($booking->equipment as $eq)
                                <div style="display:flex;justify-content:space-between;align-items:center;font-size:13.5px;">
                                    <span style="color:var(--slate-700);">{{ $eq->name }} × {{ $eq->pivot->quantity }}</span>
                                    <span style="color:var(--slate-600);font-weight:600;">
                                        RM {{ number_format($eq->pivot->price_snapshot * $eq->pivot->quantity * $relatedBookings->count(), 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Pricing --}}
                <div style="background:var(--slate-50);border-radius:var(--radius-md);padding:18px 20px;border:1px solid var(--slate-200);">
                    <div style="display:flex;flex-direction:column;gap:7px;font-size:13.5px;color:var(--slate-600);">
                        <div style="display:flex;justify-content:space-between;">
                            <span>{{ $booking->facility->name }} × {{ $relatedBookings->count() }} slot(s)</span>
                            <span>RM {{ number_format($booking->facility->price * $relatedBookings->count(), 2) }}</span>
                        </div>
                        @foreach($booking->equipment as $eq)
                            <div style="display:flex;justify-content:space-between;">
                                <span>{{ $eq->name }} × {{ $eq->pivot->quantity }} × {{ $relatedBookings->count() }} slot(s)</span>
                                <span>RM {{ number_format($eq->pivot->price_snapshot * $eq->pivot->quantity * $relatedBookings->count(), 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-top:12px;padding-top:12px;border-top:1px solid var(--slate-200);font-weight:800;font-size:16px;color:var(--slate-800);">
                        <span>Total Amount</span>
                        <span style="color:var(--utm-maroon);">RM {{ number_format($relatedBookings->sum('total_price'), 2) }}</span>
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid var(--slate-100);margin:0;">

                {{-- Participants --}}
                <div>
                    <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:12px;">Participants</div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach($booking->participants as $participant)
                            <div style="display:flex;align-items:start;gap:12px;padding:14px 16px;background:var(--slate-50);border-radius:var(--radius-md);border:1px solid var(--slate-100);">
                                <div style="width:28px;height:28px;border-radius:50%;background:{{ $participant->is_primary ? 'var(--utm-maroon)' : 'var(--slate-300)' }};color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    {{ $loop->iteration }}
                                </div>
                                <div style="flex:1;display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                                    <div>
                                        <div style="font-size:10px;color:var(--slate-400);text-transform:uppercase;letter-spacing:.06em;">Full Name</div>
                                        <div style="font-size:13px;font-weight:600;color:var(--slate-800);margin-top:3px;">{{ $participant->fullname }}</div>
                                    </div>
                                    <div>
                                        <div style="font-size:10px;color:var(--slate-400);text-transform:uppercase;letter-spacing:.06em;">IC Number</div>
                                        <div style="font-size:13px;font-weight:600;color:var(--slate-800);margin-top:3px;">{{ $participant->ic_number }}</div>
                                    </div>
                                    <div>
                                        <div style="font-size:10px;color:var(--slate-400);text-transform:uppercase;letter-spacing:.06em;">Matric / Staff ID</div>
                                        <div style="font-size:13px;font-weight:600;color:var(--slate-800);margin-top:3px;">{{ $participant->matric_number ?? '—' }}</div>
                                    </div>
                                </div>
                                @if($participant->is_primary)
                                    <span class="badge badge-admin" style="flex-shrink:0;">Renter</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid var(--slate-100);margin:0;">

                {{-- Booked by --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:5px;">Booked By</div>
                        <div style="font-size:14px;font-weight:600;color:var(--slate-700);">{{ $booking->user->fullname }}</div>
                        <div style="font-size:12.5px;color:var(--slate-400);">{{ $booking->user->email }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:5px;">Booked On</div>
                        <div style="font-size:14px;font-weight:600;color:var(--slate-700);">{{ $booking->created_at->format('d M Y') }}</div>
                        <div style="font-size:12.5px;color:var(--slate-400);">{{ $booking->created_at->format('h:i A') }}</div>
                    </div>
                </div>

                @if($booking->cancellation_reason)
                    <div class="utm-alert utm-alert-warning" style="margin:0;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div style="font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Cancellation Reason</div>
                            {{ $booking->cancellation_reason }}
                        </div>
                    </div>
                @endif

            </div>

            {{-- Footer --}}
            <div style="padding:14px 32px;background:var(--slate-50);border-top:1px solid var(--slate-100);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
                <span style="font-size:11.5px;color:var(--slate-400);">Generated on {{ now()->format('d M Y, h:i A') }}</span>
                <span style="font-size:11.5px;color:var(--slate-400);">Universiti Teknologi Malaysia</span>
            </div>

        </div>
    </div>

    <style>
    @media print {
        .utm-sidebar, .utm-topbar, footer, .btn, a[href] { display: none !important; }
        .utm-main { margin-left: 0 !important; }
        .utm-page-content { padding: 0 !important; }
        .utm-card { box-shadow: none !important; border: 1px solid #eee !important; }
    }
    </style>
</x-app-layout>