<x-app-layout>
    <x-slot name="header">My Bookings</x-slot>

    @if(session('success'))
        <div class="utm-alert utm-alert-success animate-in">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="utm-alert animate-in" style="background:rgba(37,99,235,.07);border-color:rgba(37,99,235,.15);color:#1e40af;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div class="utm-alert utm-alert-error animate-in">{{ session('error') }}</div>
    @endif

    {{-- ── Status Tabs ─────────────────────────────────────────── --}}
    @php
        $statusTabs = [
            'all'              => ['label' => 'All',                   'cls' => 'active-slate'],
            'pending_payment'  => ['label' => 'Pending Payment',       'cls' => 'active-blue'],
            'confirmed'        => ['label' => 'Confirmed',             'cls' => 'active-green'],
            'cancel_requested' => ['label' => 'Cancellation Requested','cls' => 'active-amber'],
            'cancelled'        => ['label' => 'Cancelled',             'cls' => 'active-red'],
            'completed'        => ['label' => 'Completed',             'cls' => 'active'],
            'failed'           => ['label' => 'Failed',                'cls' => 'active-red'],
        ];
    @endphp
    <div class="utm-status-tabs">
        @foreach($statusTabs as $key => $tab)
            <a href="{{ route('bookings.my', array_merge(request()->query(), ['status' => $key])) }}"
               class="utm-status-tab {{ $status === $key ? $tab['cls'] : '' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('bookings.my') }}" class="utm-filter-bar">
        <input type="hidden" name="status" value="{{ $status }}">

        {{-- Search --}}
        <div class="utm-filter-group">
            <span class="utm-filter-label">Search Facility</span>
            <div class="utm-search-wrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Type to search by facility name…"
                       class="utm-input" autocomplete="off">
            </div>
        </div>

        {{-- Actions --}}
        <div class="utm-filter-actions">
            <button type="submit" class="btn btn-primary btn-sm">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                Search
            </button>
            @if($search !== '' || $status !== 'all')
                <a href="{{ route('bookings.my') }}" class="btn btn-outline btn-sm">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear all
                </a>
            @endif
        </div>
    </form>

    {{-- Active filter chips --}}
    @if($search !== '')
        <div class="utm-filter-chips">
            <span style="font-size:11.5px;color:var(--slate-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Filtering by:</span>
            <a href="{{ route('bookings.my', array_merge(request()->except('search'))) }}" class="utm-filter-chip">
                "{{ $search }}"
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>
    @endif

    @if($bookings->isEmpty())
        <div class="utm-card" style="text-align:center;padding:64px 32px;">
            <div style="width:56px;height:56px;background:var(--slate-100);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="var(--slate-300)" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            @if($search !== '' || $status !== 'all')
                <p style="font-size:15px;font-weight:600;color:var(--slate-500);margin:0 0 6px;">No bookings match your filters</p>
                <p style="font-size:13px;color:var(--slate-300);margin:0 0 20px;">Try adjusting your search or status filter.</p>
                <a href="{{ route('bookings.my') }}" class="btn btn-outline btn-sm">Clear Filters</a>
            @else
                <p style="font-size:15px;font-weight:600;color:var(--slate-500);margin:0 0 6px;">No bookings yet</p>
                <p style="font-size:13px;color:var(--slate-300);margin:0 0 20px;">Start by browsing available facilities.</p>
                <a href="{{ route('facilities.index') }}" class="btn btn-primary btn-sm">Browse Facilities</a>
            @endif
        </div>
    @else
        <div class="utm-card animate-in" style="overflow:hidden;">
            <div style="overflow-x: auto;">
                <table class="utm-table">
                <thead>
                    <tr>
                        <th>Ref #</th>
                        <th>Facility</th>
                        <th>Date</th>
                        <th>Slot</th>
                        <th>Equipment</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        @php
                            $slotDT = $booking->booking_date->toDateString() . ' ' . $booking->slot_start;
                            $isPast = strtotime($slotDT) <= time();
                        @endphp
                        <tr style="{{ $booking->isCancelled() || $booking->isFailed() ? 'opacity:.55;' : '' }}">
                            <td style="font-size:12.5px;color:var(--slate-400);font-family:monospace;font-weight:600;">
                                #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                            </td>
                            <td>
                                <div style="font-weight:600;font-size:13.5px;color:var(--slate-800);">{{ $booking->facility->name }}</div>
                            </td>
                            <td style="white-space:nowrap;font-size:13px;">
                                {{ $booking->booking_date->format('d M Y') }}
                            </td>
                            <td style="white-space:nowrap;font-size:13px;">
                                {{ $booking->slotLabel() }}
                            </td>
                            <td>
                                @if($booking->equipment->isNotEmpty())
                                    <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                        @foreach($booking->equipment as $eq)
                                            <span style="font-size:11px;padding:2px 7px;border-radius:100px;background:rgba(37,99,235,.08);color:#1D4ED8;font-weight:600;">
                                                {{ $eq->name }} ×{{ $eq->pivot->quantity }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color:var(--slate-300);font-size:12px;">—</span>
                                @endif
                            </td>
                            <td style="font-weight:700;font-size:13.5px;color:var(--slate-800);white-space:nowrap;">
                                RM {{ number_format($booking->total_price, 2) }}
                            </td>
                            <td>
                                <span class="badge {{ $booking->statusBadgeClass() }}">{{ $booking->statusLabel() }}</span>
                                @if($booking->isPendingPayment() && $booking->payment_expires_at)
                                    <div style="font-size:10.5px;color:#92400e;margin-top:3px;">Expires {{ $booking->payment_expires_at->diffForHumans() }}</div>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;flex-direction:column;gap:4px;">
                                    {{-- Pending payment: show Complete Payment button --}}
                                    @if($booking->isPendingPayment() && !$booking->isPaymentExpired())
                                        <a href="{{ route('payment.prepare', $booking) }}"
                                           style="font-size:12.5px;color:white;font-weight:600;background:var(--utm-maroon);padding:4px 10px;border-radius:6px;text-decoration:none;white-space:nowrap;display:inline-block;">
                                            Complete Payment
                                        </a>
                                    @endif
                                    {{-- Confirmed with payment: show receipt link --}}
                                    @if($booking->isConfirmed() && $booking->payment?->isCompleted())
                                        <a href="{{ route('payment.receipt', $booking) }}"
                                           style="font-size:12.5px;color:#059669;font-weight:600;text-decoration:none;">
                                            View Receipt
                                        </a>
                                    @endif
                                    {{-- View booking slip --}}
                                    <a href="{{ route('bookings.slip', $booking) }}"
                                       style="font-size:12.5px;color:var(--utm-maroon);font-weight:600;text-decoration:none;">
                                        View Slip
                                    </a>
                                    @if($booking->isConfirmed() && !$isPast)
                                        <button type="button"
                                                style="font-size:12.5px;color:var(--danger);font-weight:600;background:none;border:none;padding:0;cursor:pointer;text-align:left;"
                                                onclick="openCancelModal({{ $booking->id }})">
                                            Request Cancel
                                        </button>
                                    @endif
                                    @if($booking->isCancelRequested())
                                        <span style="font-size:11.5px;color:var(--warning);font-weight:500;">Awaiting approval</span>
                                    @endif
                                    {{-- Leave Feedback: only for completed bookings with no feedback yet --}}
                                    @if($booking->isCompleted() && !$booking->feedback)
                                         <a href="{{ route('feedback.create', $booking->booking_group_id ?? $booking->id) }}"
                                            style="font-size:12.5px;color:var(--utm-gold);font-weight:600;text-decoration:none;white-space:nowrap;">
                                             ★ Leave Feedback
                                         </a>
                                    @elseif($booking->isCompleted() && $booking->feedback)
                                        <span style="font-size:11.5px;color:var(--slate-400);font-weight:500;">✓ Feedback submitted</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @if($bookings->hasPages())
                <div style="padding:16px 24px;border-top:1px solid var(--slate-100);">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- Cancel Modal --}}
    <div id="cancel-modal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;background:rgba(0,0,0,.45);">
        <div style="background:white;border-radius:var(--radius-xl);padding:32px;max-width:460px;width:90%;box-shadow:var(--shadow-elevated);">
            <h3 style="font-family:'Lora',Georgia,serif;font-size:20px;font-weight:600;color:var(--slate-800);margin:0 0 8px;">
                Request Cancellation
            </h3>
            <p style="font-size:13.5px;color:var(--slate-500);margin:0 0 24px;line-height:1.6;">
                Your request will be sent to the Rental Officer for approval. Please provide a reason below.
            </p>
            <form id="cancel-form" method="POST" action="">
                @csrf
                <div class="utm-form-group">
                    <label class="utm-label">Reason <span style="color:var(--danger);">*</span></label>
                    <textarea name="cancellation_reason" rows="3" required maxlength="500"
                              placeholder="Please explain why you'd like to cancel..."
                              class="utm-input" style="resize:vertical;"></textarea>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
                    <button type="button" onclick="closeCancelModal()" class="btn btn-outline">Never Mind</button>
                    <button type="submit" class="btn btn-danger">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openCancelModal(id) {
        document.getElementById('cancel-form').action = `/bookings/${id}/request-cancel`;
        document.getElementById('cancel-modal').style.display = 'flex';
    }
    function closeCancelModal() {
        document.getElementById('cancel-modal').style.display = 'none';
    }
    </script>

</x-app-layout>