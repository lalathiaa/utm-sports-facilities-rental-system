<x-app-layout>
    <x-slot name="header">All Feedbacks</x-slot>

    {{-- ── Filter Bar ──────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('feedback.all') }}" class="utm-filter-bar animate-in">

        {{-- Search --}}
        <div class="utm-filter-group">
            <span class="utm-filter-label">Search</span>
            <div class="utm-search-wrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search by user name or feedback title…"
                       class="utm-input" autocomplete="off">
            </div>
        </div>

        {{-- Facility --}}
        <div class="utm-filter-group fixed-width" style="min-width:180px;max-width:220px;">
            <span class="utm-filter-label">Facility</span>
            <select name="facility" class="utm-select">
                <option value="">All Facilities</option>
                @foreach($facilities as $facility)
                    <option value="{{ $facility->id }}" {{ $facilityId == $facility->id ? 'selected' : '' }}>
                        {{ $facility->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Rating --}}
        <div class="utm-filter-group fixed-width" style="min-width:140px;max-width:170px;">
            <span class="utm-filter-label">Rating</span>
            <select name="rating" class="utm-select">
                <option value="">All Ratings</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ $rating == $i && $rating !== null && $rating !== '' ? 'selected' : '' }}>
                        {{ str_repeat('★', $i) }}{{ str_repeat('☆', 5 - $i) }} &nbsp;{{ $i }}/5
                    </option>
                @endfor
            </select>
        </div>

        {{-- Actions --}}
        <div class="utm-filter-actions">
            <button type="submit" class="btn btn-primary btn-sm">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                Filter
            </button>
            @if($search !== '' || $facilityId || ($rating !== null && $rating !== ''))
                <a href="{{ route('feedback.all') }}" class="btn btn-outline btn-sm">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Active filter chips --}}
    @if($search !== '' || $facilityId || ($rating !== null && $rating !== ''))
        <div class="utm-filter-chips">
            <span style="font-size:11.5px;color:var(--slate-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Active filters:</span>
            @if($search !== '')
                <a href="{{ route('feedback.all', array_merge(request()->except('search'))) }}" class="utm-filter-chip">
                    "{{ $search }}"
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
            @if($facilityId)
                <a href="{{ route('feedback.all', array_merge(request()->except('facility'))) }}" class="utm-filter-chip">
                    {{ $facilities->firstWhere('id', $facilityId)?->name ?? 'Facility' }}
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
            @if($rating !== null && $rating !== '')
                <a href="{{ route('feedback.all', array_merge(request()->except('rating'))) }}" class="utm-filter-chip">
                    {{ $rating }}★ Rating
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </div>
    @endif



    @if($feedbacks->isEmpty())
        <div class="utm-card" style="text-align:center;padding:64px 32px;">
            <div style="width:56px;height:56px;background:var(--slate-100);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="var(--slate-300)" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            <p style="font-size:15px;font-weight:600;color:var(--slate-500);margin:0 0 6px;">No feedbacks found</p>
            <p style="font-size:13px;color:var(--slate-300);margin:0;">Try adjusting the filters above.</p>
        </div>
    @else
        <div class="utm-card animate-in" style="overflow:hidden;">
            <div class="utm-card-header">
                <span class="utm-card-title">Feedbacks</span>
                <span style="font-size:13px;color:var(--slate-400);">{{ $feedbacks->total() }} total</span>
            </div>
            <table class="utm-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Facility</th>
                        <th>Rating</th>
                        <th>Title & Message</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($feedbacks as $fb)
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:13.5px;color:var(--slate-800);">{{ $fb->user->fullname }}</div>
                                <div style="font-size:11.5px;color:var(--slate-400);">{{ $fb->user->role_label }}</div>
                            </td>
                            <td>
                                <div style="font-weight:600;font-size:13px;color:var(--slate-800);">{{ $fb->facility->name }}</div>
                                <div style="font-size:11.5px;color:var(--slate-400);">
                                    Booking #{{ str_pad($fb->booking_group_id, 6, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;gap:2px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span style="font-size:15px;color:{{ $i <= $fb->rating ? 'var(--utm-gold)' : 'var(--slate-200)' }};">★</span>
                                    @endfor
                                </div>
                                <div style="font-size:11px;color:var(--slate-400);margin-top:1px;">{{ $fb->rating }}/5</div>
                            </td>
                            <td style="max-width:340px;">
                                <div style="font-weight:600;font-size:13px;color:var(--slate-800);margin-bottom:3px;">{{ $fb->title }}</div>
                                <p style="font-size:12.5px;color:var(--slate-600);margin:0;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                    {{ $fb->message }}
                                </p>
                            </td>
                            <td style="white-space:nowrap;font-size:12.5px;color:var(--slate-500);">
                                {{ $fb->feedback_time->format('d M Y') }}<br>
                                <span style="color:var(--slate-400);">{{ $fb->feedback_time->format('H:i') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($feedbacks->hasPages())
                <div style="padding:16px 24px;border-top:1px solid var(--slate-100);">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    @endif
</x-app-layout>
