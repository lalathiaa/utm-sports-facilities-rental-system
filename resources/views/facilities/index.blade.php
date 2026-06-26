<x-app-layout>
    <x-slot name="header">Facilities</x-slot>

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-family:'Lora',Georgia,serif;font-size:22px;font-weight:600;color:var(--slate-800);margin:0 0 4px;">
                Sports Facilities
            </h1>
            <p style="font-size:13.5px;color:var(--slate-400);margin:0;">
                Browse and book available UTM sports facilities
            </p>
        </div>
        @auth
            @if(Auth::user()->isRentalOfficer())
                <a href="{{ route('facilities.create') }}" class="btn btn-primary">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Facility
                </a>
            @endif
        @endauth
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="utm-alert utm-alert-success animate-in">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="utm-alert utm-alert-error animate-in">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif


    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('facilities.index') }}" class="utm-filter-bar animate-in">

        {{-- Search --}}
        <div class="utm-filter-group">
            <span class="utm-filter-label">Search</span>
            <div class="utm-search-wrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search facility name…"
                       class="utm-input" autocomplete="off">
            </div>
        </div>

        {{-- Status --}}
        <div class="utm-filter-group fixed-width" style="min-width:160px;max-width:200px;">
            <span class="utm-filter-label">Status</span>
            <select name="status" class="utm-select">
                <option value="" {{ $status === '' ? 'selected' : '' }}>All Statuses</option>
                <option value="available"     {{ $status === 'available'     ? 'selected' : '' }}>● Available</option>
                <option value="not_available" {{ $status === 'not_available' ? 'selected' : '' }}>● Unavailable</option>
            </select>
        </div>

        {{-- Actions --}}
        <div class="utm-filter-actions">
            <button type="submit" class="btn btn-primary btn-sm">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                Search
            </button>
            @if($search !== '' || $status !== '')
                <a href="{{ route('facilities.index') }}" class="btn btn-outline btn-sm">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- Active filter chips --}}
    @if($search !== '' || $status !== '')
        <div class="utm-filter-chips">
            <span style="font-size:11.5px;color:var(--slate-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Active filters:</span>
            @if($search !== '')
                <a href="{{ route('facilities.index', array_merge(request()->except('search'))) }}" class="utm-filter-chip">
                    "{{ $search }}"
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
            @if($status !== '')
                <a href="{{ route('facilities.index', array_merge(request()->except('status'))) }}" class="utm-filter-chip">
                    {{ $status === 'available' ? 'Available' : 'Unavailable' }}
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </div>
    @endif


    @if($facilities->isEmpty())
        <div class="utm-card" style="text-align:center;padding:64px 32px;">
            <div style="width:56px;height:56px;background:var(--slate-100);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="var(--slate-300)" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/>
                </svg>
            </div>
            @if($search !== '' || $status !== '')
                <p style="font-size:15px;font-weight:600;color:var(--slate-500);margin:0 0 6px;">No facilities match your search</p>
                <p style="font-size:13px;color:var(--slate-300);margin:0 0 20px;">Try adjusting your search or filter criteria.</p>
                <a href="{{ route('facilities.index') }}" class="btn btn-outline btn-sm">Clear Filters</a>
            @else
                <p style="font-size:15px;font-weight:600;color:var(--slate-500);margin:0 0 6px;">No facilities yet</p>
                <p style="font-size:13px;color:var(--slate-300);margin:0 0 20px;">Facilities added by Rental Officers will appear here.</p>
                @auth
                    @if(Auth::user()->isRentalOfficer())
                        <a href="{{ route('facilities.create') }}" class="btn btn-primary btn-sm">Add the first facility</a>
                    @endif
                @endauth
            @endif
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
            @foreach($facilities as $facility)
                <div class="facility-card animate-in">

                    {{-- Image --}}
                    <div class="facility-card-image">
                        @if($facility->image)
                            <img src="{{ Storage::url($facility->image) }}" alt="{{ $facility->name }}">
                        @else
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;background:linear-gradient(135deg,var(--slate-100),var(--slate-50));">
                                <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="var(--slate-300)" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/>
                                </svg>
                            </div>
                        @endif
                        <div style="position:absolute;top:12px;right:12px;">
                            <span class="badge {{ $facility->status === 'available' ? 'badge-available' : 'badge-unavailable' }}">
                                {{ $facility->status === 'available' ? '● Available' : '● Unavailable' }}
                            </span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="facility-card-body" style="display:flex;flex-direction:column;">
                        <div class="facility-card-name">{{ $facility->name }}</div>

                        <div style="display:flex;align-items:baseline;gap:6px;margin-bottom:10px;">
                            <span class="facility-card-price">RM {{ number_format($facility->price, 2) }}</span>
                            <span style="font-size:12px;color:var(--slate-400);">/ slot</span>
                        </div>

                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;font-size:12.5px;color:var(--slate-400);">
                            <span>👥 {{ $facility->required_participants }} participant(s)</span>
                            <span>·</span>
                            <span>{{ $facility->equipment->count() }} equipment</span>
                        </div>

                        @if(!Auth::check() || !Auth::user()->isAdmin())
                            @php $avgRating = $facility->averageRating(); $fbCount = $facility->feedbackCount(); @endphp
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:12px;">
                                <div style="display:flex;gap:1px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span style="font-size:13px;color:{{ $i <= round($avgRating) ? 'var(--utm-gold)' : 'var(--slate-200)' }};">★</span>
                                    @endfor
                                </div>
                                <span style="font-size:12px;color:var(--slate-500);font-weight:600;">{{ $avgRating > 0 ? number_format($avgRating, 1) : '—' }}</span>
                                <span style="font-size:11.5px;color:var(--slate-400);">({{ $fbCount }} review{{ $fbCount !== 1 ? 's' : '' }})</span>
                            </div>
                        @endif

                        {{-- Equipment chips --}}
                        <div style="flex:1;">
                            @if($facility->equipment->isNotEmpty())
                                <div style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:16px;">
                                    @foreach($facility->equipment->take(3) as $eq)
                                        <span style="font-size:11.5px;padding:2px 8px;border-radius:100px;background:var(--slate-100);color:var(--slate-500);font-weight:500;">
                                            {{ $eq->name }}
                                        </span>
                                    @endforeach
                                    @if($facility->equipment->count() > 3)
                                        <span style="font-size:11.5px;color:var(--slate-400);">+{{ $facility->equipment->count() - 3 }} more</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Actions always at bottom --}}
                        <div style="display:flex;gap:8px;align-items:center;margin-top:auto;">
                            <a href="{{ route('facilities.show', $facility) }}" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;">
                                Details
                            </a>
                            @auth
                                @if(Auth::user()->isRentalOfficer())
                                    <a href="{{ route('facilities.edit', $facility) }}" class="btn btn-sm"
                                    style="background:rgba(217,119,6,.08);color:#92600A;">Edit</a>
                                    <form method="POST" action="{{ route('facilities.destroy', $facility) }}"
                                        onsubmit="return confirm('Delete {{ addslashes($facility->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm"
                                                style="background:rgba(220,38,38,.08);color:var(--danger);">Delete</button>
                                    </form>
                                @elseif($facility->isAvailable())
                                    <a href="{{ route('bookings.create', $facility) }}" class="btn btn-primary btn-sm" style="flex:1;justify-content:center;">
                                        Book Now
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($facilities->hasPages())
            <div style="margin-top:28px;">{{ $facilities->links() }}</div>
        @endif
    @endif

</x-app-layout>