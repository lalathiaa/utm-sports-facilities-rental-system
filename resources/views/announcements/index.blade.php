<x-app-layout>
    <x-slot name="header">Announcements</x-slot>

    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('announcements.index') }}" class="utm-filter-bar animate-in">

        {{-- Search --}}
        <div class="utm-filter-group">
            <span class="utm-filter-label">Search Announcements</span>
            <div class="utm-search-wrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search by title or keyword…"
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
            @if($search !== '')
                <a href="{{ route('announcements.index') }}" class="btn btn-outline btn-sm">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- Active filter chip --}}
    @if($search !== '')
        <div class="utm-filter-chips">
            <span style="font-size:11.5px;color:var(--slate-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Searching for:</span>
            <a href="{{ route('announcements.index') }}" class="utm-filter-chip">
                "{{ $search }}"
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>
    @endif


    @if($announcements->isEmpty())
        <div class="utm-card" style="text-align:center;padding:64px 32px;">
            <div style="width:56px;height:56px;background:var(--slate-100);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="var(--slate-300)" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            @if($search !== '')
                <p style="font-size:15px;font-weight:600;color:var(--slate-500);margin:0 0 6px;">No announcements match your search</p>
                <p style="font-size:13px;color:var(--slate-300);margin:0 0 20px;">Try different keywords.</p>
                <a href="{{ route('announcements.index') }}" class="btn btn-outline btn-sm">Clear Search</a>
            @else
                <p style="font-size:15px;font-weight:600;color:var(--slate-500);margin:0 0 6px;">No announcements yet</p>
                <p style="font-size:13px;color:var(--slate-300);margin:0;">Check back later for updates from the Rental Officer.</p>
            @endif
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:16px;">
            @foreach($announcements as $ann)
                <div class="utm-card animate-in" style="animation-delay:{{ $loop->index * 0.04 }}s;">
                    <div class="utm-card-body">
                        <div style="display:flex;align-items:flex-start;gap:16px;">
                            <div style="width:44px;height:44px;background:rgba(139,0,0,.07);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="var(--utm-maroon)" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:8px;">
                                    <h3 style="font-family:'Lora',Georgia,serif;font-size:16px;font-weight:600;color:var(--slate-800);margin:0;">
                                        {{ $ann->title }}
                                    </h3>
                                    <span style="font-size:11.5px;color:var(--slate-400);white-space:nowrap;">
                                        {{ $ann->announcement_time->format('d M Y, H:i') }}
                                    </span>
                                </div>
                                <p style="font-size:13.5px;color:var(--slate-600);margin:0 0 10px;line-height:1.7;">
                                    {{ $ann->message }}
                                </p>
                                <div style="font-size:11.5px;color:var(--slate-400);">
                                    Posted by <strong style="color:var(--slate-600);">{{ $ann->user->fullname }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($announcements->hasPages())
                <div style="margin-top:8px;">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    @endif
</x-app-layout>
