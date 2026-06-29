<x-app-layout>
    <x-slot name="header">Manage Bookings</x-slot>

    {{-- Flash --}}
    @if(session('success'))
        <div class="utm-alert utm-alert-success animate-in">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="utm-alert utm-alert-error animate-in">{{ session('error') }}</div>
    @endif

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-family:'Lora',Georgia,serif;font-size:22px;font-weight:600;color:var(--slate-800);margin:0 0 4px;">
                All Bookings
            </h1>
            <p style="font-size:13.5px;color:var(--slate-400);margin:0;">
                Review, manage, and action booking requests
            </p>
        </div>
    </div>

    {{-- ── Status Tabs ─────────────────────────────────────────── --}}
    @php
        $tabDefs = [
            'all'              => ['label' => 'All Bookings',           'cls' => 'active-slate'],
            'pending_payment'  => ['label' => 'Pending Payment',       'cls' => 'active-blue'],
            'confirmed'        => ['label' => 'Confirmed',              'cls' => 'active-green'],
            'cancel_requested' => ['label' => 'Cancellation Requested', 'cls' => 'active-amber'],
            'cancelled'        => ['label' => 'Cancelled',              'cls' => 'active-red'],
            'failed'           => ['label' => 'Failed',                 'cls' => 'active-red'],
        ];
    @endphp
    <div class="utm-status-tabs">
        @foreach($tabDefs as $key => $tab)
            <a href="{{ route('bookings.all', array_merge(request()->query(), ['status' => $key])) }}"
               class="utm-status-tab {{ $status === $key ? $tab['cls'] : '' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('bookings.all') }}" class="utm-filter-bar animate-in">
        <input type="hidden" name="status" value="{{ $status }}">

        {{-- Search --}}
        <div class="utm-filter-group">
            <span class="utm-filter-label">Search</span>
            <div class="utm-search-wrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search renter name, email or facility…"
                       class="utm-input" autocomplete="off">
            </div>
        </div>

        {{-- Date --}}
        <div class="utm-filter-group fixed-width" style="min-width:170px;max-width:200px;">
            <span class="utm-filter-label">Booking Date</span>
            <input type="date" name="date" value="{{ $date }}" class="utm-input">
        </div>

        {{-- Actions --}}
        <div class="utm-filter-actions">
            <button type="submit" class="btn btn-primary btn-sm">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                Filter
            </button>
            @if($search !== '' || $date !== '')
                <a href="{{ route('bookings.all', ['status' => $status]) }}" class="btn btn-outline btn-sm">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- Active filter chips --}}
    @if($search !== '' || $date !== '')
        <div class="utm-filter-chips">
            <span style="font-size:11.5px;color:var(--slate-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Active filters:</span>
            @if($search !== '')
                <a href="{{ route('bookings.all', array_merge(request()->except('search'))) }}" class="utm-filter-chip">
                    "{{ $search }}"
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
            @if($date !== '')
                <a href="{{ route('bookings.all', array_merge(request()->except('date'))) }}" class="utm-filter-chip">
                    📅 {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </div>
    @endif



    @if($bookings->isEmpty())
        <div class="utm-card" style="text-align:center;padding:64px 32px;">
            <div style="width:56px;height:56px;background:var(--slate-100);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="var(--slate-300)" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p style="font-size:15px;font-weight:600;color:var(--slate-500);margin:0 0 6px;">No bookings found</p>
            <p style="font-size:13px;color:var(--slate-300);margin:0;">
                {{ $status === 'all' ? 'No bookings have been made yet.' : 'No bookings match this filter.' }}
            </p>
        </div>
    @else
        <div class="utm-card animate-in" style="overflow:hidden;">
            <div style="overflow-x: auto;">
                <table class="utm-table">
                    <thead>
                        <tr>
                            <th>Ref #</th>
                            <th>Renter</th>
                            <th>Facility</th>
                            <th>Date & Slot</th>
                            <th>Participants</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr style="{{ $booking->isCancelled() || $booking->isFailed() ? 'opacity:.55;' : '' }}">
                                <td style="font-size:12.5px;color:var(--slate-400);font-family:monospace;font-weight:600;">
                                    #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>
                                    <div style="font-weight:600;font-size:13.5px;color:var(--slate-800);">{{ $booking->user->fullname }}</div>
                                    <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">{{ $booking->user->email }}</div>
                                </td>
                                <td style="font-size:13.5px;color:var(--slate-700);font-weight:500;">
                                    {{ $booking->facility->name }}
                                </td>
                                <td>
                                    <div style="font-size:13px;font-weight:600;color:var(--slate-700);">
                                        {{ $booking->booking_date->format('d M Y') }}
                                    </div>
                                    <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">
                                        {{ $booking->slotLabel() }}
                                    </div>
                                </td>
                                <td>
                                    @if($booking->participants->isNotEmpty())
                                        <div style="display:flex;flex-direction:column;gap:3px;">
                                            @foreach($booking->participants as $p)
                                                <div style="font-size:12.5px;color:{{ $p->is_primary ? 'var(--slate-700)' : 'var(--slate-400)' }};font-weight:{{ $p->is_primary ? '600' : '400' }};">
                                                    {{ $p->fullname }}
                                                    @if($p->is_primary)
                                                        <span style="font-size:10px;color:var(--utm-maroon);font-weight:600;">(Renter)</span>
                                                    @endif
                                                </div>
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
                                    @if($booking->isCancelRequested() && $booking->cancellation_reason)
                                        <div style="font-size:11.5px;color:var(--warning);margin-top:4px;max-width:160px;line-height:1.4;">
                                            "{{ Str::limit($booking->cancellation_reason, 55) }}"
                                        </div>
                                    @endif
                                </td>
                            <td>
                                <div style="display:flex;flex-direction:column;gap:5px;">
                                    {{-- View Slip --}}
                                    <a href="{{ route('bookings.slip', $booking) }}"
                                       style="font-size:12.5px;color:var(--utm-maroon);font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        View Slip
                                    </a>

                                    {{-- Cancel Requested: Approve / Reject --}}
                                    @if($booking->isCancelRequested())
                                        <form method="POST" action="{{ route('bookings.approve-cancel', $booking) }}"
                                              onsubmit="return confirm('Approve cancellation for booking #{{ str_pad($booking->id,6,'0',STR_PAD_LEFT) }}?')">
                                            @csrf
                                            <button type="submit"
                                                    style="font-size:12.5px;color:var(--danger);font-weight:600;background:none;border:none;padding:0;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Approve Cancel
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('bookings.reject-cancel', $booking) }}">
                                            @csrf
                                            <button type="submit"
                                                    style="font-size:12.5px;color:#059669;font-weight:600;background:none;border:none;padding:0;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Reject Request
                                            </button>
                                        </form>

                                    {{-- Confirmed: Officer can directly cancel --}}
                                    @elseif($booking->isConfirmed())
                                        <form method="POST" action="{{ route('bookings.cancel', $booking) }}"
                                              onsubmit="return confirm('Cancel booking #{{ str_pad($booking->id,6,'0',STR_PAD_LEFT) }}? This cannot be undone.')">
                                            @csrf
                                            <button type="submit"
                                                    style="font-size:12.5px;color:var(--danger);font-weight:600;background:none;border:none;padding:0;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                Cancel Booking
                                            </button>
                                        </form>
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

</x-app-layout>