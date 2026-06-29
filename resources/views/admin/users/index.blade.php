<x-app-layout>
    <x-slot name="header">User Management</x-slot>

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
        <div class="utm-alert utm-alert-error animate-in">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-family:'Lora',Georgia,serif;font-size:22px;font-weight:600;color:var(--slate-800);margin:0 0 4px;">
                Rental Officer Assignment
            </h1>
            <p style="font-size:13.5px;color:var(--slate-400);margin:0;">
                View all registered users and manage Rental Officer promotions
            </p>
        </div>

        {{-- Summary counts --}}
        @php
            $counts = [
                'all'            => \App\Models\User::whereIn('role',['staff','student','guest','rental_officer'])->count(),
                'staff'          => \App\Models\User::where('role','staff')->count(),
                'student'        => \App\Models\User::where('role','student')->count(),
                'guest'          => \App\Models\User::where('role','guest')->count(),
                'rental_officer' => \App\Models\User::where('role','rental_officer')->count(),
            ];
        @endphp
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <div style="padding:8px 16px;background:white;border:1px solid var(--slate-200);border-radius:var(--radius-md);text-align:center;">
                <div style="font-size:18px;font-weight:800;color:var(--utm-maroon);">{{ $counts['rental_officer'] }}</div>
                <div style="font-size:11px;color:var(--slate-400);text-transform:uppercase;letter-spacing:.06em;">Officers</div>
            </div>
            <div style="padding:8px 16px;background:white;border:1px solid var(--slate-200);border-radius:var(--radius-md);text-align:center;">
                <div style="font-size:18px;font-weight:800;color:var(--slate-700);">{{ $counts['all'] }}</div>
                <div style="font-size:11px;color:var(--slate-400);text-transform:uppercase;letter-spacing:.06em;">Total Users</div>
            </div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
        @php
            $tabs = [
                'all'            => ['label' => 'All Users',       'count' => $counts['all'],            'active' => 'background:var(--slate-700);color:white;'],
                'staff'          => ['label' => 'UTM Staff',       'count' => $counts['staff'],          'active' => 'background:#2563EB;color:white;'],
                'student'        => ['label' => 'UTM Students',    'count' => $counts['student'],        'active' => 'background:#7C3AED;color:white;'],
                'guest'          => ['label' => 'Guests',          'count' => $counts['guest'],          'active' => 'background:#475569;color:white;'],
                'rental_officer' => ['label' => 'Rental Officers', 'count' => $counts['rental_officer'], 'active' => 'background:var(--utm-maroon);color:white;'],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
            <a href="{{ route('admin.users.index', array_merge(request()->query(), ['filter' => $key])) }}"
               style="padding:7px 14px;border-radius:100px;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;display:flex;align-items:center;gap:6px;
                      {{ $filter === $key ? $tab['active'] : 'background:white;color:var(--slate-500);border:1.5px solid var(--slate-200);' }}">
                {{ $tab['label'] }}
                <span style="font-size:11px;padding:1px 6px;border-radius:100px;
                             {{ $filter === $key ? 'background:rgba(255,255,255,.25);color:white;' : 'background:var(--slate-100);color:var(--slate-500);' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="utm-filter-bar" style="margin-bottom:20px;">
        <input type="hidden" name="filter" value="{{ $filter }}">

        {{-- Search --}}
        <div class="utm-filter-group" style="flex:1;">
            <span class="utm-filter-label">Search Users</span>
            <div class="utm-search-wrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Search by name, username, email, IC, matric or staff ID…"
                       class="utm-input" autocomplete="off" style="width:100%;">
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
            @if(($search ?? '') !== '' || $filter !== 'all')
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear all
                </a>
            @endif
        </div>
    </form>

    @if($users->isEmpty())
        <div class="utm-card" style="text-align:center;padding:64px 32px;">
            <div style="width:56px;height:56px;background:var(--slate-100);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="var(--slate-300)" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p style="font-size:15px;font-weight:600;color:var(--slate-500);margin:0 0 6px;">No users found</p>
            <p style="font-size:13px;color:var(--slate-300);margin:0;">No users match this filter.</p>
        </div>
    @else
        <div class="utm-card animate-in" style="overflow:hidden;">
            <div style="overflow-x: auto;">
                <table class="utm-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>ID / Matric</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    @php
                                        $avatarBg = match($user->role) {
                                            'rental_officer' => 'rgba(139,0,0,.10)',
                                            'staff'          => 'rgba(37,99,235,.10)',
                                            'student'        => 'rgba(124,58,237,.10)',
                                            default          => 'rgba(71,85,105,.10)',
                                        };
                                        $avatarColor = match($user->role) {
                                            'rental_officer' => 'var(--utm-maroon)',
                                            'staff'          => '#1D4ED8',
                                            'student'        => '#6D28D9',
                                            default          => '#475569',
                                        };
                                    @endphp
                                    <div style="width:36px;height:36px;border-radius:50%;background:{{ $avatarBg }};color:{{ $avatarColor }};
                                                display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;">
                                        {{ strtoupper(substr($user->fullname, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:13.5px;color:var(--slate-800);">{{ $user->fullname }}</div>
                                        <div style="font-size:11.5px;color:var(--slate-400);margin-top:1px;">IC: {{ $user->ic_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:13px;color:var(--slate-600);font-family:monospace;">
                                {{ $user->username }}
                            </td>
                            <td style="font-size:13px;color:var(--slate-600);">
                                {{ $user->email }}
                            </td>
                            <td style="font-size:13px;color:var(--slate-600);font-family:monospace;">
                                @if($user->matric_number)
                                    {{ $user->matric_number }}
                                @elseif($user->staff_id)
                                    {{ $user->staff_id }}
                                @else
                                    <span style="color:var(--slate-300);">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($user->role) {
                                        'rental_officer' => 'badge-admin',
                                        'staff'          => 'badge-staff',
                                        'student'        => 'badge-student',
                                        default          => 'badge-guest',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $user->role_label }}</span>
                            </td>
                            <td>
                                @if($user->role !== 'rental_officer')
                                    <form method="POST" action="{{ route('admin.users.promote', $user) }}"
                                          onsubmit="return confirm('Promote {{ addslashes($user->fullname) }} to Rental Officer?')">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                            </svg>
                                            Promote to Officer
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.demote', $user) }}"
                                          onsubmit="return confirm('Demote {{ addslashes($user->fullname) }} from Rental Officer?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm"
                                                style="background:rgba(220,38,38,.08);color:var(--danger);border:1px solid rgba(220,38,38,.15);">
                                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                            </svg>
                                            Demote
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
                <div style="padding:16px 24px;border-top:1px solid var(--slate-100);">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    @endif

</x-app-layout>