<x-app-layout>
    <x-slot name="header">AI Recommendations</x-slot>

    <style>
        .rec-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 24px;
        }
        .rec-card {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .rec-card:hover {
            box-shadow: 0 8px 32px rgba(139,0,0,.10);
            transform: translateY(-2px);
        }
        .rec-card-inner {
            display: flex;
            align-items: stretch;
            gap: 0;
        }
        .rec-rank-badge {
            width: 56px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-body);
            font-size: 22px;
            font-weight: 800;
            color: white;
            border-radius: 0;
        }
        .rec-body {
            flex: 1;
            padding: 20px 24px;
            min-width: 0;
        }
        .rec-facility-name {
            font-size: 17px;
            font-weight: 700;
            color: var(--slate-800);
            margin: 0 0 4px;
        }
        .rec-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 8px 0 12px;
            font-size: 12.5px;
            color: var(--slate-500);
        }
        .rec-meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 999px;
            padding: 3px 10px;
            font-weight: 600;
        }
        .rec-reason {
            font-size: 13px;
            color: var(--slate-600);
            line-height: 1.6;
            font-style: italic;
            border-left: 3px solid var(--utm-maroon);
            padding-left: 10px;
            margin: 10px 0 0;
        }
        .rec-score-bar-wrap {
            margin: 12px 0 0;
        }
        .rec-score-bar-bg {
            height: 6px;
            background: var(--slate-100);
            border-radius: 999px;
            overflow: hidden;
        }
        .rec-score-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--utm-maroon-dark), var(--utm-maroon));
            transition: width 0.6s ease;
        }
        .rec-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 24px;
            border-top: 1px solid var(--slate-100);
            background: var(--slate-50);
        }
        .rec-empty {
            text-align: center;
            padding: 60px 24px;
            color: var(--slate-400);
        }
        .ai-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(135deg, #8B0000, #a00);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .legend-box {
            background: rgba(139,0,0,.04);
            border: 1px solid rgba(139,0,0,.12);
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            margin-bottom: 24px;
            font-size: 13px;
            color: var(--slate-600);
            line-height: 1.7;
        }
        .legend-box strong { color: var(--utm-maroon); }
    </style>

    {{-- ── Page header ──────────────────────────────────────── --}}
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:8px;">
        <div>
            <h1 style="font-family:var(--font-display); font-size:22px; font-weight:700; color:var(--slate-800); margin:0 0 4px;">
                ✨ Recommended For You
            </h1>
            <p style="font-size:13.5px; color:var(--slate-400); margin:0;">
                Top facility and timeslot picks, ranked by rating, popularity, and your booking history.
            </p>
        </div>
        <span class="ai-badge">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            AI-Powered
        </span>
    </div>

    {{-- ── Scoring explanation (transparent AI) ───────────────── --}}
    <div class="legend-box animate-in animate-in-delay-1">
        <strong>How the score is computed:</strong>
        Each facility-timeslot pair is scored 0–10 using a weighted formula:
        <strong>35%</strong> average star rating ·
        <strong>30%</strong> system-wide booking popularity ·
        <strong>25%</strong> your personal booking history at that facility ·
        <strong>10%</strong> time-of-day demand.
        Only slots that are actually available to book right now are shown.
    </div>

    {{-- ── Recommendations list ────────────────────────────────── --}}
    @if($recommendations->isEmpty())
        <div class="utm-card rec-empty animate-in">
            <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="var(--slate-300)" stroke-width="1.2" style="margin:0 auto 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <p style="font-size:15px; font-weight:600; color:var(--slate-500); margin:0 0 8px;">No available facilities right now</p>
            <p style="font-size:13px; margin:0;">All facilities are fully booked within the next 14 days. Please check back later.</p>
        </div>
    @else
        <div class="rec-grid">
            @foreach($recommendations as $i => $rec)
                @php
                    $facility  = $rec['facility'];
                    $score     = $rec['score'];
                    $slotDate  = $rec['slot_date'];
                    $slotStart = $rec['slot_start'];
                    $slotEnd   = $rec['slot_end'];
                    $reason    = $rec['reason'];
                    $rank      = $i + 1;
                    $pct       = $score * 10; // 0–100 for bar width

                    // Rank colour
                    $rankColor = match($rank) {
                        1 => '#8B0000',
                        2 => '#C9A84C',
                        3 => '#6B7280',
                        default => '#94A3B8',
                    };

                    // Booking URL pre-filled with date
                    $bookUrl = route('bookings.create', $facility) . '?date=' . $slotDate;
                @endphp

                <div class="utm-card rec-card animate-in animate-in-delay-{{ $rank }}">
                    <div class="rec-card-inner">

                        {{-- Rank column --}}
                        <div class="rec-rank-badge" style="background: {{ $rankColor }};">
                            #{{ $rank }}
                        </div>

                        {{-- Main body --}}
                        <div class="rec-body">
                            <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                                <p class="rec-facility-name">{{ $facility->name }}</p>
                                <span style="font-size:18px; font-weight:800; color:var(--utm-maroon); font-family:var(--font-body);">
                                    {{ number_format($score, 1) }}<span style="font-size:12px; font-weight:500; color:var(--slate-400);">/10</span>
                                </span>
                            </div>

                            <div class="rec-meta">
                                {{-- Date & slot --}}
                                <span class="rec-meta-pill">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($slotDate)->format('d M Y') }}
                                </span>
                                <span class="rec-meta-pill">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $slotStart }} – {{ $slotEnd }}
                                </span>
                                {{-- Rating stars --}}
                                @if($facility->averageRating() > 0)
                                    <span class="rec-meta-pill" style="color: var(--warning);">
                                        ★ {{ number_format($facility->averageRating(), 1) }}
                                        <span style="color:var(--slate-400); font-weight:400;">({{ $facility->feedbackCount() }} reviews)</span>
                                    </span>
                                @else
                                    <span class="rec-meta-pill" style="color:var(--slate-400);">★ No reviews yet</span>
                                @endif
                                {{-- Price --}}
                                <span class="rec-meta-pill">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    RM {{ number_format($facility->price, 2) }}/hr
                                </span>
                            </div>

                            {{-- Score bar --}}
                            <div class="rec-score-bar-wrap">
                                <div class="rec-score-bar-bg">
                                    <div class="rec-score-bar-fill" style="width: {{ $pct }}%;"></div>
                                </div>
                            </div>

                            {{-- AI reason --}}
                            <p class="rec-reason">{{ $reason }}</p>
                        </div>
                    </div>

                    {{-- CTA footer --}}
                    <div class="rec-actions">
                        <a href="{{ $bookUrl }}" class="btn btn-sm"
                           style="background:var(--utm-maroon); color:white; display:inline-flex; align-items:center; gap:6px;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Book Now — {{ \Carbon\Carbon::parse($slotDate)->format('d M') }}, {{ $slotStart }}
                        </a>
                        <a href="{{ route('facilities.show', $facility) }}" class="btn btn-outline btn-sm">View Facility</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Help note ──────────────────────────────────────────── --}}
    <div style="margin-top:24px; font-size:12px; color:var(--slate-400); text-align:center; line-height:1.7;">
        Recommendations update in real time based on current availability, system-wide booking patterns, and your booking history.<br>
        Slots shown are the earliest available within the next 14 days.
    </div>

</x-app-layout>
