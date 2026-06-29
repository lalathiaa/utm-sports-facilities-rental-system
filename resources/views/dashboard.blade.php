<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    @php
        $user = Auth::user();
    @endphp

    <style>
        /* ── Layout wrappers ─────────────────────── */
        .dashboard-root {
            width: 100%;
            max-width: none;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
            margin-top: 24px;
            width: 100%;
        }
        @media (max-width: 1100px) {
            .dashboard-grid {
                grid-template-columns: 2fr 1fr;
            }
        }
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ── Stat grids ──────────────────────────── */
        /* Admin: 2 cards */
        .dashboard-stat-grid-admin {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 28px;
            width: 100%;
        }
        /* Officer: 4 cards */
        .dashboard-stat-grid-officer {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 28px;
            width: 100%;
        }
        /* Student/Staff/Guest: 3 cards */
        .dashboard-stat-grid-user {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 28px;
            width: 100%;
        }
        @media (max-width: 1100px) {
            .dashboard-stat-grid-officer {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .dashboard-stat-grid-admin,
            .dashboard-stat-grid-officer,
            .dashboard-stat-grid-user {
                grid-template-columns: 1fr;
            }
        }

        /* ── Action buttons ──────────────────────── */
        .action-card-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 12px;
            transition: all 0.2s ease;
            text-decoration: none;
            cursor: pointer;
            margin-bottom: 12px;
        }
        .action-card-btn:hover {
            background: #fff;
            border-color: var(--utm-maroon);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .action-icon-wrapper {
            width: 40px;
            height: 40px;
            background: var(--utm-maroon);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 12px;
            flex-shrink: 0;
        }
    </style>

    <div class="dashboard-root">

        {{-- ── Welcome Banner ─────────────────────────────────── --}}
        <div style="background: linear-gradient(135deg, var(--utm-maroon-dark) 0%, var(--utm-maroon) 60%, #A50000 100%);
                    border-radius: var(--radius-xl); padding: 32px 36px; margin-bottom: 28px; position: relative; overflow: hidden; box-shadow: var(--shadow-card);">
            {{-- Decorative elements --}}
            <div style="position: absolute; right: -20px; top: -20px; width: 160px; height: 160px; border-radius: 50%; background: rgba(255,255,255,.04);"></div>
            <div style="position: absolute; right: 80px; bottom: -40px; width: 100px; height: 100px; border-radius: 50%; background: rgba(201,168,76,.06);"></div>

            <div style="position: relative; z-index: 1;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <p style="font-size: 12px; color: var(--utm-gold-light); font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin: 0 0 8px;">
                            {{ $user->role_label }}
                        </p>
                        <h1 style="font-family: var(--font-display); font-size: 28px; font-weight: 600; color: white; margin: 0 0 10px; line-height: 1.2;">
                            Welcome back, {{ explode(' ', $user->fullname)[0] }}
                        </h1>
                        <p style="font-size: 14px; color: rgba(255,255,255,.65); margin: 0;">
                            {{ now()->format('l, d F Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Stat Cards ───────────────────────────────────────── --}}
        @if($user->isAdmin())
            @php
                $totalUsers = \App\Models\User::whereIn('role', ['staff', 'student', 'guest', 'rental_officer'])->count();
                $officers   = \App\Models\User::where('role', 'rental_officer')->count();
            @endphp
            <div class="dashboard-stat-grid-admin">
                {{-- Total Users --}}
                <div class="utm-card animate-in animate-in-delay-1" style="border-left: 4px solid var(--info); padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 13px; color: var(--slate-400); margin: 0 0 4px; font-weight: 600;">Total Users</p>
                            <h3 style="font-size: 26px; font-weight: 800; color: var(--slate-800); margin: 0; font-family: var(--font-body);">{{ $totalUsers }}</h3>
                            <p style="font-size: 12px; color: var(--slate-400); margin: 6px 0 0;">Registered members</p>
                        </div>
                        <div style="background: rgba(37, 99, 235, .08); padding: 12px; border-radius: 10px; color: var(--info);">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Rental Officers --}}
                <div class="utm-card animate-in animate-in-delay-2" style="border-left: 4px solid var(--success); padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 13px; color: var(--slate-400); margin: 0 0 4px; font-weight: 600;">Rental Officers</p>
                            <h3 style="font-size: 26px; font-weight: 800; color: var(--slate-800); margin: 0; font-family: var(--font-body);">{{ $officers }}</h3>
                            <p style="font-size: 12px; color: var(--slate-400); margin: 6px 0 0;">Active system staff</p>
                        </div>
                        <div style="background: rgba(5, 150, 105, .08); padding: 12px; border-radius: 10px; color: var(--success);">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($user->isRentalOfficer())
            @php
                $totalFacilities  = \App\Models\Facility::count();
                $availFacilities  = \App\Models\Facility::where('status','available')->count();
                $totalBookings    = \App\Models\Booking::count();
                $pendingBookings  = \App\Models\Booking::where('status','pending')->count();
                $pendingCancel    = \App\Models\Booking::where('status','cancel_requested')->count();
                $revenue          = \App\Models\Booking::where('status','confirmed')->sum('total_price');
                $averageRating    = \App\Models\Feedback::avg('rating') ?? 0;
                $satisfaction     = $averageRating > 0 ? ($averageRating / 5) * 100 : 0;
            @endphp
            <div class="dashboard-stat-grid-officer">
                {{-- Total Bookings --}}
                <div class="utm-card animate-in animate-in-delay-1" style="border-left: 4px solid var(--info); padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 13px; color: var(--slate-400); margin: 0 0 4px; font-weight: 600;">Total Bookings</p>
                            <h3 style="font-size: 26px; font-weight: 800; color: var(--slate-800); margin: 0; font-family: var(--font-body);">{{ number_format($totalBookings) }}</h3>
                            <p style="font-size: 12px; color: var(--success); margin: 6px 0 0; display: flex; align-items: center; gap: 4px;">
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                {{ $pendingBookings }} pending approval
                            </p>
                        </div>
                        <div style="background: rgba(37, 99, 235, .08); padding: 12px; border-radius: 10px; color: var(--info);">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total Facilities --}}
                <div class="utm-card animate-in animate-in-delay-2" style="border-left: 4px solid var(--success); padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 13px; color: var(--slate-400); margin: 0 0 4px; font-weight: 600;">Total Facilities</p>
                            <h3 style="font-size: 26px; font-weight: 800; color: var(--slate-800); margin: 0; font-family: var(--font-body);">{{ $totalFacilities }}</h3>
                            <p style="font-size: 12px; color: var(--info); margin: 6px 0 0; display: flex; align-items: center; gap: 4px;">
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $availFacilities }} available
                            </p>
                        </div>
                        <div style="background: rgba(5, 150, 105, .08); padding: 12px; border-radius: 10px; color: var(--success);">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Revenue --}}
                <div class="utm-card animate-in animate-in-delay-3" style="border-left: 4px solid var(--danger); padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 13px; color: var(--slate-400); margin: 0 0 4px; font-weight: 600;">Revenue</p>
                            <h3 style="font-size: 26px; font-weight: 800; color: var(--slate-800); margin: 0; font-family: var(--font-body);">RM {{ number_format($revenue, 2) }}</h3>
                            <p style="font-size: 12px; color: var(--slate-400); margin: 6px 0 0;">From completed bookings</p>
                        </div>
                        <div style="background: rgba(220, 38, 38, .08); padding: 12px; border-radius: 10px; color: var(--danger);">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Customer Satisfaction --}}
                <div class="utm-card animate-in animate-in-delay-4" style="border-left: 4px solid var(--warning); padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 13px; color: var(--slate-400); margin: 0 0 4px; font-weight: 600;">Customer Satisfaction</p>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <h3 style="font-size: 26px; font-weight: 800; color: var(--slate-800); margin: 0; font-family: var(--font-body);">{{ number_format($satisfaction, 1) }}%</h3>
                                @if($averageRating > 0)
                                    <div style="display: flex; gap: 1px;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="{{ $i <= round($averageRating) ? 'var(--utm-gold)' : 'var(--slate-200)' }}" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                            <p style="font-size: 12px; color: var(--slate-400); margin: 6px 0 0; display: flex; align-items: center; gap: 4px;">
                                @if($averageRating > 0)
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="var(--success)" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    <span style="color: var(--success); font-weight: 600;">{{ number_format($averageRating, 1) }}/5</span> stars from feedback
                                @else
                                    <span style="font-style: italic;">No feedback data yet</span>
                                @endif
                            </p>
                        </div>
                        <div style="background: rgba(217, 119, 6, .08); padding: 12px; border-radius: 10px; color: var(--warning);">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

        @else
            {{-- Staff / Student / Guest --}}
            @php
                $myBookings       = \App\Models\Booking::where('user_id', $user->id)->count();
                $myConfirmed      = \App\Models\Booking::where('user_id', $user->id)->where('status','confirmed')->count();
                $pendingFeedback = \App\Models\Booking::with('feedback')
                                    ->where('user_id', $user->id)
                                    ->where('status', 'confirmed')
                                    ->whereDoesntHave('feedback')
                                    ->get()
                                    ->filter(fn($b) => $b->isCompleted())
                                    ->unique('booking_group_id');
            @endphp
            <div class="dashboard-stat-grid-user">
                {{-- Total Bookings --}}
                <div class="utm-card animate-in animate-in-delay-1" style="border-left: 4px solid var(--info); padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 13px; color: var(--slate-400); margin: 0 0 4px; font-weight: 600;">Total Bookings</p>
                            <h3 style="font-size: 26px; font-weight: 800; color: var(--slate-800); margin: 0; font-family: var(--font-body);">{{ $myBookings }}</h3>
                            <p style="font-size: 12px; color: var(--slate-400); margin: 6px 0 0;">All-time bookings</p>
                        </div>
                        <div style="background: rgba(37, 99, 235, .08); padding: 12px; border-radius: 10px; color: var(--info);">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Confirmed Bookings --}}
                <div class="utm-card animate-in animate-in-delay-2" style="border-left: 4px solid var(--success); padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 13px; color: var(--slate-400); margin: 0 0 4px; font-weight: 600;">Active Bookings</p>
                            <h3 style="font-size: 26px; font-weight: 800; color: var(--slate-800); margin: 0; font-family: var(--font-body);">{{ $myConfirmed }}</h3>
                            <p style="font-size: 12px; color: var(--slate-400); margin: 6px 0 0;">Confirmed slot(s)</p>
                        </div>
                        <div style="background: rgba(5, 150, 105, .08); padding: 12px; border-radius: 10px; color: var(--success);">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Pending Feedback --}}
                <div class="utm-card animate-in animate-in-delay-3" style="border-left: 4px solid var(--warning); padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 13px; color: var(--slate-400); margin: 0 0 4px; font-weight: 600;">Pending Feedbacks</p>
                            <h3 style="font-size: 26px; font-weight: 800; color: var(--slate-800); margin: 0; font-family: var(--font-body);">{{ $pendingFeedback->count() }}</h3>
                            <p style="font-size: 12px; color: var(--slate-400); margin: 6px 0 0;">Awaiting your review</p>
                        </div>
                        <div style="background: rgba(217, 119, 6, .08); padding: 12px; border-radius: 10px; color: var(--warning);">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Alerts (Full Width) ─────────────────────────────── --}}
        @if($user->isRentalOfficer() && $pendingCancel > 0)
            <div class="utm-alert utm-alert-warning animate-in" style="margin-bottom:16px;">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <strong style="font-weight:700;">Cancellation Review Required:</strong> You have {{ $pendingCancel }} cancellation request(s) awaiting action.
                    <a href="{{ route('bookings.all', ['status' => 'cancel_requested']) }}" style="color:inherit;font-weight:700;margin-left:6px;text-decoration:underline;">Review Bookings →</a>
                </div>
            </div>
        @endif

        @if(!$user->isAdmin() && !$user->isRentalOfficer() && $pendingFeedback->isNotEmpty())
            <div class="utm-alert animate-in" style="background:rgba(201,168,76,.08);border:1px solid rgba(201,168,76,.25);color:#92600A;margin-bottom:16px;">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <div>
                    You have <strong style="font-weight:700;">{{ $pendingFeedback->count() }} completed booking(s)</strong> awaiting your feedback.
                    <a href="{{ route('bookings.my') }}" style="color:inherit;font-weight:700;margin-left:6px;text-decoration:underline;">Submit Feedback →</a>
                </div>
            </div>
        @endif

        {{-- ── Two-Column Layout Grid ─────────────────────────── --}}
        <div class="dashboard-grid">

            {{-- Left Column: Main content panels --}}
            <div style="display:flex;flex-direction:column;gap:24px;">

                {{-- 1. Admin: Recent User Registrations --}}
                @if($user->isAdmin())
                    @php
                        $recentUsers = \App\Models\User::whereIn('role', ['staff', 'student', 'guest', 'rental_officer'])
                            ->orderByDesc('created_at')
                            ->limit(5)
                            ->get();
                    @endphp
                    <div class="utm-card animate-in animate-in-delay-3">
                        <div class="utm-card-header">
                            <span class="utm-card-title">👥 Recent User Registrations</span>
                            <a href="{{ route('admin.users.index') }}" style="font-size:13px;color:var(--utm-maroon);font-weight:600;text-decoration:none;">Manage Users →</a>
                        </div>
                        @if($recentUsers->isEmpty())
                            <div style="padding:40px;text-align:center;color:var(--slate-400);font-size:14px;">
                                No recent user registrations.
                            </div>
                        @else
                            <div style="overflow-x: auto;">
                                <table class="utm-table" style="border: none; margin: 0; width: 100%;">
                                    <thead>
                                        <tr style="background: var(--slate-50);">
                                            <th style="padding: 12px 24px; font-size: 11px;">User</th>
                                            <th style="padding: 12px 24px; font-size: 11px;">Role</th>
                                            <th style="padding: 12px 24px; font-size: 11px;">Registered At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentUsers as $ru)
                                            <tr style="border-bottom: 1px solid var(--slate-100);">
                                                <td style="padding: 12px 24px;">
                                                    <div style="font-weight:600;font-size:13.5px;color:var(--slate-800);">{{ $ru->fullname }}</div>
                                                    <div style="font-size:11.5px;color:var(--slate-400);">{{ $ru->email }}</div>
                                                </td>
                                                <td style="padding: 12px 24px;">
                                                    @php
                                                        $badgeClass = match($ru->role) {
                                                            'rental_officer' => 'badge-admin',
                                                            'staff'          => 'badge-staff',
                                                            'student'        => 'badge-student',
                                                            default          => 'badge-guest',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $ru->role_label }}</span>
                                                </td>
                                                <td style="padding: 12px 24px; font-size:12.5px;color:var(--slate-500);">
                                                    {{ $ru->created_at ? $ru->created_at->format('d M Y, H:i') : 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- 2. Rental Officer: Recent Bookings Activity (above announcements) --}}
                @if($user->isRentalOfficer())
                    @php
                        $recentBookings = \App\Models\Booking::with(['facility', 'user'])
                            ->orderByDesc('created_at')
                            ->limit(3)
                            ->get();
                    @endphp
                    <div class="utm-card animate-in animate-in-delay-3">
                        <div class="utm-card-header">
                            <span class="utm-card-title">📅 Recent Bookings Activity</span>
                            <a href="{{ route('bookings.all') }}" style="font-size:13px;color:var(--utm-maroon);font-weight:600;text-decoration:none;">Manage Bookings →</a>
                        </div>
                        @if($recentBookings->isEmpty())
                            <div style="padding:40px;text-align:center;color:var(--slate-400);font-size:14px;">
                                No bookings registered yet.
                            </div>
                        @else
                            <div>
                                @foreach($recentBookings as $rb)
                                    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 24px;border-bottom:1px solid var(--slate-100);">
                                        <div style="flex:1;min-width:0;">
                                            <div style="font-weight:600;font-size:13.5px;color:var(--slate-800);">{{ $rb->facility->name }}</div>
                                            <div style="font-size:12px;color:var(--slate-500);margin-top:2px;">
                                                Renter: <strong style="color: var(--slate-700);">{{ $rb->user->fullname }}</strong> · {{ $rb->booking_date->format('d M Y') }} ({{ $rb->slotLabel() }})
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
                                            @php
                                                $badgeClass = match($rb->status) {
                                                    'confirmed' => 'badge-confirmed',
                                                    'cancelled' => 'badge-cancelled',
                                                    'cancel_requested' => 'badge-requested',
                                                    'pending_payment' => 'badge-pending',
                                                    default => '',
                                                };
                                            @endphp
                                            @if($rb->status === 'failed')
                                                <span style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:100px;font-size:11px;font-weight:600;background:rgba(220,38,38,.08);color:var(--danger);">Failed</span>
                                            @else
                                                <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $rb->status)) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- 3. Student/Staff/Guest: Upcoming Bookings --}}
                @if(!$user->isAdmin() && !$user->isRentalOfficer())
                    @php
                        $upcomingBookings = \App\Models\Booking::where('user_id', $user->id)
                                            ->where('status', 'confirmed')
                                            ->where('booking_date', '>=', now()->toDateString())
                                            ->orderBy('booking_date')->orderBy('slot_start')
                                            ->with('facility')->limit(3)->get();
                    @endphp
                    <div class="utm-card animate-in animate-in-delay-3">
                        <div class="utm-card-header">
                            <span class="utm-card-title">📅 Upcoming Bookings</span>
                            <a href="{{ route('bookings.my') }}" style="font-size:13px;color:var(--utm-maroon);font-weight:600;text-decoration:none;">View all →</a>
                        </div>
                        @if($upcomingBookings->isEmpty())
                            <div style="padding:40px;text-align:center;color:var(--slate-400);font-size:14px;">
                                No upcoming bookings scheduled.
                                <a href="{{ route('facilities.index') }}" style="color:var(--utm-maroon);font-weight:600;margin-left:4px;">Book a facility now →</a>
                            </div>
                        @else
                            <div>
                                @foreach($upcomingBookings as $ub)
                                    <div style="display:flex;align-items:center;gap:16px;padding:16px 24px;border-bottom:1px solid var(--slate-100);">
                                        <div style="width:44px;height:44px;background:rgba(139,0,0,.06);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--utm-maroon);">
                                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div style="flex:1;min-width:0;">
                                            <div style="font-weight:600;font-size:14px;color:var(--slate-800);">{{ $ub->facility->name }}</div>
                                            <div style="font-size:12.5px;color:var(--slate-400);margin-top:2px;">
                                                {{ $ub->booking_date->format('d M Y') }} · {{ $ub->slotLabel() }}
                                            </div>
                                        </div>
                                        <a href="{{ route('bookings.slip', $ub) }}" class="btn btn-outline btn-sm">View Slip</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- 4. Announcements --}}
                @if($user->isRentalOfficer())
                    @php
                        $latestAnnouncements = \App\Models\Announcement::where('user_id', $user->id)
                            ->orderByDesc('created_at')->limit(3)->get();
                    @endphp
                    <div class="utm-card animate-in animate-in-delay-4">
                        <div class="utm-card-header">
                            <span class="utm-card-title">📢 My Announcements</span>
                            <a href="{{ route('officer.announcements.index') }}" style="font-size:13px;color:var(--utm-maroon);font-weight:600;text-decoration:none;">Manage →</a>
                        </div>
                        @if($latestAnnouncements->isEmpty())
                            <div style="padding:32px;text-align:center;color:var(--slate-400);font-size:13.5px;">
                                No announcements published yet.
                                <a href="{{ route('officer.announcements.create') }}" style="color:var(--utm-maroon);font-weight:600;margin-left:4px;">Create one →</a>
                            </div>
                        @else
                            <div>
                                @foreach($latestAnnouncements as $ann)
                                    <div style="display:flex;align-items:flex-start;gap:14px;padding:16px 24px;border-bottom:1px solid var(--slate-100);">
                                        <div style="width:36px;height:36px;background:rgba(139,0,0,.06);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--utm-maroon);">
                                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                            </svg>
                                        </div>
                                        <div style="flex:1;min-width:0;">
                                            <div style="font-weight:600;font-size:13.5px;color:var(--slate-800);">{{ $ann->title }}</div>
                                            <p style="font-size:12.5px;color:var(--slate-600);margin:4px 0 0;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $ann->message }}</p>
                                            <div style="font-size:11px;color:var(--slate-400);margin-top:4px;">{{ $ann->announcement_time->format('d M Y, H:i') }}</div>
                                        </div>
                                        <a href="{{ route('officer.announcements.edit', $ann) }}" class="btn btn-outline btn-sm">Edit</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @elseif(!$user->isAdmin())
                    @php
                        $latestAnnouncements = \App\Models\Announcement::with('user')
                            ->orderByDesc('created_at')->limit(3)->get();
                    @endphp
                    @if($latestAnnouncements->isNotEmpty())
                        <div class="utm-card animate-in animate-in-delay-4">
                            <div class="utm-card-header">
                                <span class="utm-card-title">📢 Latest Announcements</span>
                                <a href="{{ route('announcements.index') }}" style="font-size:13px;color:var(--utm-maroon);font-weight:600;text-decoration:none;">View all →</a>
                            </div>
                            <div>
                                @foreach($latestAnnouncements as $ann)
                                    <div style="display:flex;align-items:flex-start;gap:14px;padding:16px 24px;border-bottom:1px solid var(--slate-100);">
                                        <div style="width:36px;height:36px;background:rgba(139,0,0,.06);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--utm-maroon);">
                                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                            </svg>
                                        </div>
                                        <div style="flex:1;min-width:0;">
                                            <div style="font-weight:600;font-size:13.5px;color:var(--slate-800);">{{ $ann->title }}</div>
                                            <p style="font-size:12.5px;color:var(--slate-600);margin:4px 0 0;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $ann->message }}</p>
                                            <div style="font-size:11px;color:var(--slate-400);margin-top:4px;">{{ $ann->announcement_time->format('d M Y, H:i') }} · by {{ $ann->user->fullname }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                {{-- 5. Rental Officer: Latest Feedback --}}
                @if($user->isRentalOfficer())
                    @php
                        $latestFeedbacks = \App\Models\Feedback::with(['user', 'facility'])
                            ->orderByDesc('created_at')
                            ->limit(3)
                            ->get();
                    @endphp
                    <div class="utm-card animate-in animate-in-delay-5">
                        <div class="utm-card-header">
                            <span class="utm-card-title">💬 Latest Customer Feedbacks</span>
                            <a href="{{ route('feedback.all') }}" style="font-size:13px;color:var(--utm-maroon);font-weight:600;text-decoration:none;">View all →</a>
                        </div>
                        @if($latestFeedbacks->isEmpty())
                            <div style="padding:40px;text-align:center;color:var(--slate-400);font-size:14px;">
                                No customer feedbacks received yet.
                            </div>
                        @else
                            <div>
                                @foreach($latestFeedbacks as $fb)
                                    <div style="padding:16px 24px;border-bottom:1px solid var(--slate-100);">
                                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                                            <div style="font-weight:600;font-size:13.5px;color:var(--slate-800);">{{ $fb->user->fullname }}</div>
                                            <div style="display:flex;gap:2px;">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= $fb->rating ? 'var(--utm-gold)' : 'var(--slate-200)' }}" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <div style="font-size:12px;color:var(--slate-400);margin-bottom:8px;">
                                            Facility: <strong style="color:var(--slate-600);">{{ $fb->facility->name }}</strong> · {{ $fb->created_at ? $fb->created_at->diffForHumans() : 'Recently' }}
                                        </div>
                                        <div style="font-weight:650;font-size:13px;color:var(--slate-700);margin-bottom:4px;">"{{ $fb->title }}"</div>
                                        <p style="font-size:12.5px;color:var(--slate-600);margin:0;line-height:1.5;">{{ $fb->message }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- 6. Student/Staff/Guest: Recent Feedbacks --}}
                @if(!$user->isAdmin() && !$user->isRentalOfficer())
                    @php
                        $myRecentFeedbacks = \App\Models\Feedback::with('facility')
                            ->where('user_id', $user->id)
                            ->orderByDesc('created_at')
                            ->limit(3)
                            ->get();
                    @endphp
                    <div class="utm-card animate-in animate-in-delay-5">
                        <div class="utm-card-header">
                            <span class="utm-card-title">💬 My Recent Feedbacks</span>
                            <a href="{{ route('feedback.my') }}" style="font-size:13px;color:var(--utm-maroon);font-weight:600;text-decoration:none;">View history →</a>
                        </div>
                        @if($myRecentFeedbacks->isEmpty())
                            <div style="padding:32px;text-align:center;color:var(--slate-400);font-size:13.5px;">
                                You have not submitted any feedbacks yet.
                            </div>
                        @else
                            <div>
                                @foreach($myRecentFeedbacks as $mf)
                                    <div style="padding:16px 24px;border-bottom:1px solid var(--slate-100);">
                                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                                            <span style="font-weight:600;font-size:13.5px;color:var(--slate-800);">{{ $mf->facility->name }}</span>
                                            <div style="display:flex;gap:2px;">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= $mf->rating ? 'var(--utm-gold)' : 'var(--slate-200)' }}" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <div style="font-size:11.5px;color:var(--slate-400);margin-bottom:6px;">
                                            Submitted {{ $mf->created_at ? $mf->created_at->format('d M Y, H:i') : 'Recently' }}
                                        </div>
                                        <div style="font-weight:650;font-size:13px;color:var(--slate-700);margin-bottom:4px;">"{{ $mf->title }}"</div>
                                        <p style="font-size:12.5px;color:var(--slate-600);margin:0;line-height:1.5;">{{ $mf->message }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Right Column: Sidebar Actions & Account Info --}}
            <div style="display:flex;flex-direction:column;gap:24px;">

                {{-- Quick Actions Panel --}}
                <div class="utm-card animate-in animate-in-delay-2">
                    <div class="utm-card-header">
                        <span class="utm-card-title">⚡ Quick Actions</span>
                    </div>
                    <div class="utm-card-body" style="padding:20px;">
                        @if($user->isAdmin())
                            <a href="{{ route('admin.users.index') }}" class="action-card-btn">
                                <div style="display:flex;align-items:center;">
                                    <div class="action-icon-wrapper" style="background:var(--info);">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <span style="font-weight:600;color:var(--slate-700);font-size:13.5px;">Manage Users</span>
                                </div>
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--slate-400)" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @elseif($user->isRentalOfficer())
                            <a href="{{ route('facilities.create') }}" class="action-card-btn">
                                <div style="display:flex;align-items:center;">
                                    <div class="action-icon-wrapper" style="background:rgba(37,99,235,0.9);">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </div>
                                    <span style="font-weight:600;color:var(--slate-700);font-size:13.5px;">Add New Facility</span>
                                </div>
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--slate-400)" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <a href="{{ route('bookings.all') }}" class="action-card-btn">
                                <div style="display:flex;align-items:center;">
                                    <div class="action-icon-wrapper" style="background:rgba(5,150,105,0.9);">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <span style="font-weight:600;color:var(--slate-700);font-size:13.5px;">Manage Bookings</span>
                                </div>
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--slate-400)" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <a href="{{ route('feedback.all') }}" class="action-card-btn">
                                <div style="display:flex;align-items:center;">
                                    <div class="action-icon-wrapper" style="background:rgba(124,58,237,0.9);">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                        </svg>
                                    </div>
                                    <span style="font-weight:600;color:var(--slate-700);font-size:13.5px;">View Feedbacks</span>
                                </div>
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--slate-400)" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('facilities.index') }}" class="action-card-btn">
                                <div style="display:flex;align-items:center;">
                                    <div class="action-icon-wrapper" style="background:rgba(37,99,235,0.9);">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <span style="font-weight:600;color:var(--slate-700);font-size:13.5px;">Book a Facility</span>
                                </div>
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--slate-400)" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <a href="{{ route('bookings.my') }}" class="action-card-btn">
                                <div style="display:flex;align-items:center;">
                                    <div class="action-icon-wrapper" style="background:rgba(5,150,105,0.9);">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <span style="font-weight:600;color:var(--slate-700);font-size:13.5px;">My Bookings</span>
                                </div>
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--slate-400)" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <a href="{{ route('feedback.my') }}" class="action-card-btn">
                                <div style="display:flex;align-items:center;">
                                    <div class="action-icon-wrapper" style="background:rgba(124,58,237,0.9);">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                        </svg>
                                    </div>
                                    <span style="font-weight:600;color:var(--slate-700);font-size:13.5px;">My Feedbacks</span>
                                </div>
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="var(--slate-400)" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Account Information --}}
                <div class="utm-card animate-in animate-in-delay-3">
                    <div class="utm-card-header">
                        <span class="utm-card-title">👤 Profile Details</span>
                        <a href="{{ route('profile.edit') }}" style="font-size:12px;color:var(--utm-maroon);font-weight:600;text-decoration:none;">Edit Info</a>
                    </div>
                    <div class="utm-card-body" style="padding:20px;font-size:13.5px;line-height:1.6;color:var(--slate-700);">
                        <div style="margin-bottom:12px;">
                            <span style="display:block;font-size:11px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;">Full Name</span>
                            <strong style="color:var(--slate-800);">{{ $user->fullname }}</strong>
                        </div>
                        <div style="margin-bottom:12px;">
                            <span style="display:block;font-size:11px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;">Username</span>
                            <span>{{ $user->username }}</span>
                        </div>
                        <div style="margin-bottom:12px;">
                            <span style="display:block;font-size:11px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;">Email Address</span>
                            <span>{{ $user->email }}</span>
                        </div>
                        @if($user->matric_number)
                            <div style="margin-bottom:12px;">
                                <span style="display:block;font-size:11px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;">Matric Number</span>
                                <span>{{ $user->matric_number }}</span>
                            </div>
                        @endif
                        @if($user->staff_id)
                            <div style="margin-bottom:12px;">
                                <span style="display:block;font-size:11px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;">Staff ID</span>
                                <span>{{ $user->staff_id }}</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>{{-- end .dashboard-root --}}

</x-app-layout>