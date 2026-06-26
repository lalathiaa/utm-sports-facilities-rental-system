<x-app-layout>
    <x-slot name="header">Manage Announcements</x-slot>

    @if(session('success'))
        <div class="utm-alert utm-alert-success animate-in">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Page Toolbar ────────────────────────────────────────── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <div>
            <p class="utm-result-count" style="margin:0;">
                <strong>{{ $announcements->total() }}</strong> announcement{{ $announcements->total() !== 1 ? 's' : '' }}
                @if($search !== '') matching <em>"{{ $search }}"</em>@endif
            </p>
        </div>
        <a href="{{ route('officer.announcements.create') }}" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Announcement
        </a>
    </div>

    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('officer.announcements.index') }}" class="utm-filter-bar animate-in">

        {{-- Search --}}
        <div class="utm-filter-group">
            <span class="utm-filter-label">Search by Title</span>
            <div class="utm-search-wrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Type announcement title…"
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
                <a href="{{ route('officer.announcements.index') }}" class="btn btn-outline btn-sm">
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
            <a href="{{ route('officer.announcements.index') }}" class="utm-filter-chip">
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
                <a href="{{ route('officer.announcements.index') }}" class="btn btn-outline btn-sm">Clear Search</a>
            @else
                <p style="font-size:15px;font-weight:600;color:var(--slate-500);margin:0 0 6px;">No announcements yet</p>
                <p style="font-size:13px;color:var(--slate-300);margin:0 0 20px;">Create your first announcement to keep users informed.</p>
                <a href="{{ route('officer.announcements.create') }}" class="btn btn-primary btn-sm">Create Announcement</a>
            @endif
        </div>
    @else
        <div class="utm-card animate-in" style="overflow:hidden;">
            <div class="utm-card-header">
                <span class="utm-card-title">Your Announcements</span>
                <span style="font-size:13px;color:var(--slate-400);">{{ $announcements->total() }} total</span>
            </div>
            <table class="utm-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Posted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($announcements as $ann)
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:13.5px;color:var(--slate-800);max-width:200px;">
                                    {{ $ann->title }}
                                </div>
                            </td>
                            <td style="max-width:380px;">
                                <p style="font-size:13px;color:var(--slate-600);margin:0;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                    {{ $ann->message }}
                                </p>
                            </td>
                            <td style="white-space:nowrap;font-size:12.5px;color:var(--slate-500);">
                                {{ $ann->announcement_time->format('d M Y') }}<br>
                                <span style="color:var(--slate-400);">{{ $ann->announcement_time->format('H:i') }}</span>
                            </td>
                            <td>
                                <div style="display:flex;gap:8px;">
                                    <a href="{{ route('officer.announcements.edit', $ann) }}"
                                       class="btn btn-outline btn-sm">Edit</a>
                                    <form method="POST"
                                          action="{{ route('officer.announcements.destroy', $ann) }}"
                                          onsubmit="return confirm('Delete this announcement?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($announcements->hasPages())
                <div style="padding:16px 24px;border-top:1px solid var(--slate-100);">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    @endif
</x-app-layout>
