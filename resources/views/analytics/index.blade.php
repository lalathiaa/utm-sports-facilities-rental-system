<x-app-layout>
    <x-slot name="header">Predictive Analytics</x-slot>

    <style>
        .analytics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 24px;
        }
        @media (max-width: 900px) {
            .analytics-grid { grid-template-columns: 1fr; }
        }
        .analytics-full-row {
            grid-column: 1 / -1;
        }
        .chart-container {
            position: relative;
            height: 280px;
        }
        .chart-container-tall {
            position: relative;
            height: 340px;
        }
        .stat-pill {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            padding: 12px 20px;
            font-family: var(--font-body);
        }
        .stat-pill-value {
            font-size: 22px;
            font-weight: 800;
            color: var(--utm-maroon);
        }
        .stat-pill-label {
            font-size: 11px;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: .04em;
            font-weight: 700;
            margin-top: 2px;
        }
        .suggestion-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid var(--slate-100);
        }
        .suggestion-item:last-child { border-bottom: none; }
        .suggestion-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--warning);
            flex-shrink: 0;
            margin-top: 6px;
        }
        .filter-form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-form select, .filter-form input[type=number] {
            border: 1px solid var(--slate-200);
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 13px;
            color: var(--slate-700);
            background: white;
        }
        .methodology-note {
            font-size: 12px;
            color: var(--slate-400);
            font-style: italic;
            margin-top: 8px;
            line-height: 1.6;
        }
    </style>

    {{-- ── Page Header & Filters ──────────────────────────────── --}}
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-top:8px;">
        <div>
            <h1 style="font-family:var(--font-display); font-size:22px; font-weight:700; color:var(--slate-800); margin:0 0 4px;">
                📊 Predictive Analytics Report
            </h1>
            <p style="font-size:13px; color:var(--slate-400); margin:0;">
                Demand forecasts, revenue trends, utilisation rates, and optimisation suggestions.
                @if($lookbackDays)
                    Window: last <strong>{{ $lookbackDays }}</strong> days.
                @else
                    Window: <strong>all historical data</strong>.
                @endif
                &nbsp;·&nbsp;
                @if(($report['engine'] ?? 'php') === 'python')
                    <span style="color:#10B981; font-weight:600;">⚙ pandas engine</span>
                @else
                    <span style="color:var(--slate-400);">⚙ PHP engine (pandas fallback)</span>
                @endif
            </p>
        </div>

        {{-- Window filter --}}
        <form method="GET" action="{{ route('analytics.index') }}" class="filter-form">
            <label style="font-size:12px; font-weight:600; color:var(--slate-500);">Lookback:</label>
            <select name="lookback_days" onchange="this.form.submit()">
                <option value="" {{ $lookbackDays === null ? 'selected' : '' }}>All data</option>
                <option value="30"  {{ $lookbackDays === 30  ? 'selected' : '' }}>Last 30 days</option>
                <option value="60"  {{ $lookbackDays === 60  ? 'selected' : '' }}>Last 60 days</option>
                <option value="90"  {{ $lookbackDays === 90  ? 'selected' : '' }}>Last 90 days</option>
                <option value="180" {{ $lookbackDays === 180 ? 'selected' : '' }}>Last 6 months</option>
            </select>
            <label style="font-size:12px; font-weight:600; color:var(--slate-500);">Projection:</label>
            <select name="projection_weeks" onchange="this.form.submit()">
                <option value="4" {{ $projectionWeeks === 4 ? 'selected' : '' }}>4 weeks</option>
                <option value="8" {{ $projectionWeeks === 8 ? 'selected' : '' }}>8 weeks</option>
            </select>
        </form>
    </div>

    @php $stats = $report['summary_stats']; @endphp

    {{-- ── Summary stat pills ──────────────────────────────────── --}}
    <div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:20px;" class="animate-in animate-in-delay-1">
        <div class="stat-pill">
            <span class="stat-pill-value">{{ number_format($stats['total_bookings']) }}</span>
            <span class="stat-pill-label">Total Bookings</span>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-value">RM {{ number_format($stats['total_revenue'], 2) }}</span>
            <span class="stat-pill-label">Total Revenue</span>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-value">{{ $stats['unique_facilities'] }}</span>
            <span class="stat-pill-label">Active Facilities</span>
        </div>
        @if($stats['date_range_start'])
        <div class="stat-pill">
            <span class="stat-pill-value" style="font-size:13px;">
                {{ \Carbon\Carbon::parse($stats['date_range_start'])->format('d M Y') }}
                –
                {{ \Carbon\Carbon::parse($stats['date_range_end'])->format('d M Y') }}
            </span>
            <span class="stat-pill-label">Data Range</span>
        </div>
        @endif
    </div>

    {{-- Data-window explanation — visible during demo, explains the 'all data' choice --}}
    @if(!$lookbackDays)
    <div style="background:rgba(37,99,235,.05); border:1px solid rgba(37,99,235,.15); border-radius:10px; padding:12px 16px; margin-top:12px; font-size:12.5px; color:#1D4ED8; line-height:1.6;">
        <strong>ℹ Data window: all historical data.</strong>
        This is intentional — the current dataset has fewer than 50 bookings, so a rolling window (e.g. last 90 days) would leave too few data points for a meaningful trend line.
        In production with higher booking volume, switch to a rolling window so forecasts reflect recent trends rather than being diluted by older data.
    </div>
    @endif

    @if($stats['total_bookings'] === 0)
        <div class="utm-alert utm-alert-warning animate-in" style="margin-top:24px;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>No confirmed booking data found for the selected window. Charts will be empty. Try selecting "All data" above.</div>
        </div>
    @endif

    <div class="analytics-grid">

        {{-- ── 1. Revenue Trend Chart ─────────────────────────────── --}}
        <div class="utm-card animate-in animate-in-delay-2" style="padding:24px;">
            <div class="utm-card-header" style="padding:0 0 16px; border:none;">
                <span class="utm-card-title">📈 Revenue Trend & Projection</span>
            </div>
            @php $revData = $report['revenue_trend']; @endphp
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
            <p class="methodology-note">
                Actual weekly revenue (solid line). Projected weeks (dashed) use linear extrapolation:
                mean Δ = RM {{ number_format($revData['mean_delta'], 2) }}/week.
                Clearly labelled — not presented as certain.
            </p>
        </div>

        {{-- ── 2. Utilisation Rate ────────────────────────────────── --}}
        <div class="utm-card animate-in animate-in-delay-3" style="padding:24px;">
            <div class="utm-card-header" style="padding:0 0 16px; border:none;">
                <span class="utm-card-title">⚡ Facility Utilisation Rate</span>
            </div>
            @php $utilData = $report['utilisation']; @endphp
            <div class="chart-container">
                <canvas id="utilisationChart"></canvas>
            </div>
            <p class="methodology-note">
                Booked slots ÷ total possible slots (14 slots/day × days in window) × 100%.
                Conservative denominator — FacilityClosures are not subtracted.
            </p>
        </div>

        {{-- ── 3. Top Demand Slots ────────────────────────────────── --}}
        <div class="utm-card animate-in animate-in-delay-2" style="padding:24px;">
            <div class="utm-card-header" style="padding:0 0 16px; border:none;">
                <span class="utm-card-title">🔥 Demand Forecast — Top Timeslots</span>
            </div>
            @php $topSlots = $report['top_slots']; @endphp
            @if($topSlots->isEmpty())
                <div style="padding:32px; text-align:center; color:var(--slate-400); font-size:13px;">No booking data available.</div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:13px;">
                        <thead>
                            <tr style="border-bottom:2px solid var(--slate-100);">
                                <th style="text-align:left; padding:8px 4px; color:var(--slate-500); font-weight:700; font-size:11px; text-transform:uppercase;">Facility</th>
                                <th style="text-align:left; padding:8px 4px; color:var(--slate-500); font-weight:700; font-size:11px; text-transform:uppercase;">Day</th>
                                <th style="text-align:left; padding:8px 4px; color:var(--slate-500); font-weight:700; font-size:11px; text-transform:uppercase;">Hour</th>
                                <th style="text-align:right; padding:8px 4px; color:var(--slate-500); font-weight:700; font-size:11px; text-transform:uppercase;">Avg/Week</th>
                                <th style="text-align:right; padding:8px 4px; color:var(--slate-500); font-weight:700; font-size:11px; text-transform:uppercase;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topSlots as $slot)
                                <tr style="border-bottom:1px solid var(--slate-100);">
                                    <td style="padding:10px 4px; color:var(--slate-800); font-weight:600;">{{ $slot['facility_name'] }}</td>
                                    <td style="padding:10px 4px; color:var(--slate-600);">{{ $slot['dow_label'] }}</td>
                                    <td style="padding:10px 4px; color:var(--slate-600);">{{ $slot['hour'] }}</td>
                                    <td style="padding:10px 4px; text-align:right;">
                                        <span style="font-weight:700; color:var(--utm-maroon);">{{ number_format($slot['avg_per_week'], 2) }}</span>
                                    </td>
                                    <td style="padding:10px 4px; text-align:right; color:var(--slate-500);">{{ $slot['total_bookings'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="methodology-note">
                    Simple moving average: total bookings per (facility, day, hour) ÷ distinct weeks in dataset.
                    Appropriate for datasets with fewer than 50 data points — avoids overfitting.
                </p>
            @endif
        </div>

        {{-- ── 4. Under-Booked Suggestions ───────────────────────── --}}
        <div class="utm-card animate-in animate-in-delay-3" style="padding:24px;">
            <div class="utm-card-header" style="padding:0 0 16px; border:none;">
                <span class="utm-card-title">💡 Optimisation Suggestions</span>
            </div>
            @php $suggestions = $report['under_booked']; @endphp
            @if($suggestions->isEmpty())
                <div style="padding:32px; text-align:center; color:var(--success); font-size:13px; font-weight:600;">
                    🎉 All facility-timeslot combinations are well-utilised!
                </div>
            @else
                <div style="max-height:320px; overflow-y:auto;">
                    @foreach($suggestions as $s)
                        <div class="suggestion-item">
                            <div class="suggestion-dot"></div>
                            <div>
                                <p style="font-size:13px; color:var(--slate-700); margin:0 0 2px; line-height:1.5;">
                                    {{ $s['suggestion'] }}
                                </p>
                                <span style="font-size:11px; color:var(--slate-400);">
                                    Avg demand: {{ number_format($s['avg_per_week'], 2) }} bookings/week
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="methodology-note">
                    Flagged when average weekly demand &lt; 0.3 bookings/week for a (facility, day, hour) combination.
                    Showing top 15 lowest-demand combinations.
                </p>
            @endif
        </div>

        {{-- ── 5. Demand Heatmap (full width) ─────────────────────── --}}
        @php $heatmap = $report['demand_heatmap']; $facilities = \App\Models\Facility::orderBy('name')->get(); @endphp
        @if(!empty($heatmap))
        <div class="utm-card animate-in analytics-full-row" style="padding:24px;">
            <div class="utm-card-header" style="padding:0 0 16px; border:none;">
                <span class="utm-card-title">🗓 Demand Heatmap — Facility × Hour (avg bookings/week)</span>
            </div>
            <div class="chart-container-tall">
                <canvas id="heatmapChart"></canvas>
            </div>
            <p class="methodology-note">
                Grouped bar chart: each facility's average weekly booking demand by hour of day.
                Taller bars indicate peak demand periods. Based on all historical confirmed bookings.
            </p>
        </div>
        @endif

    </div>

    {{-- ═══ Chart.js ════════════════════════════════════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    // ── Shared theme colours ─────────────────────────────────────
    const UTM_MAROON  = '#8B0000';
    const UTM_GOLD    = '#C9A84C';
    const SLATE_200   = '#E2E8F0';
    const PALETTE = [
        '#8B0000','#C9A84C','#3B82F6','#10B981',
        '#F59E0B','#6366F1','#EF4444','#14B8A6'
    ];

    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#64748B';

    // ── 1. Revenue Trend Chart ───────────────────────────────────
    @php
        $rev           = $report['revenue_trend'];
        $allLabels     = array_merge($rev['labels'], $rev['projected_labels']);
        $actualData    = array_values($rev['actuals']);
        $projectedData = array_fill(0, count($rev['actuals']), null);
        // Bridge: last actual + first projected share the junction point
        if (!empty($rev['actuals']) && !empty($rev['projected'])) {
            $projectedData[] = end($rev['actuals']); // connect the lines
        }
        foreach ($rev['projected'] as $p) {
            $projectedData[] = $p;
        }
        $actualDataPadded = $actualData;
        foreach ($rev['projected'] as $p) {
            $actualDataPadded[] = null;
        }
    @endphp
    (function() {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($allLabels) !!},
                datasets: [
                    {
                        label: 'Actual Revenue (RM)',
                        data: {!! json_encode($actualDataPadded) !!},
                        borderColor: UTM_MAROON,
                        backgroundColor: 'rgba(139,0,0,.08)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: UTM_MAROON,
                    },
                    {
                        label: 'Projected Revenue (RM)',
                        data: {!! json_encode($projectedData) !!},
                        borderColor: UTM_GOLD,
                        backgroundColor: 'rgba(201,168,76,.08)',
                        borderDash: [6, 4],
                        fill: false,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: UTM_GOLD,
                        pointStyle: 'triangle',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.dataset.label + ': RM ' + (ctx.parsed.y ?? 0).toFixed(2)
                        }
                    }
                },
                scales: {
                    y: { ticks: { callback: v => 'RM ' + v.toFixed(0) }, grid: { color: SLATE_200 } },
                    x: { grid: { display: false } }
                }
            }
        });
    })();

    // ── 2. Utilisation Chart ─────────────────────────────────────
    @php
        $util       = $report['utilisation'];
        $utilLabels = $util->pluck('facility_name')->toArray();
        $utilValues = $util->pluck('utilisation')->toArray();
    @endphp
    (function() {
        const ctx = document.getElementById('utilisationChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($utilLabels) !!},
                datasets: [{
                    label: 'Utilisation (%)',
                    data: {!! json_encode($utilValues) !!},
                    backgroundColor: {!! json_encode($utilValues) !!}.map(v =>
                        v >= 50 ? UTM_MAROON : v >= 20 ? UTM_GOLD : '#94A3B8'
                    ),
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.parsed.x.toFixed(1)}% utilised (${@json($util->pluck('booked_slots')->toArray())[ctx.dataIndex ?? 0]} slots booked)`
                        }
                    }
                },
                scales: {
                    x: {
                        max: 100,
                        ticks: { callback: v => v + '%' },
                        grid: { color: SLATE_200 }
                    },
                    y: { grid: { display: false } }
                }
            }
        });
    })();

    // ── 3. Heatmap (grouped bar chart: facility × hour) ──────────
    @php
        $heatmapData  = $report['demand_heatmap'];
        $facilityList = \App\Models\Facility::orderBy('name')->pluck('name', 'id')->toArray();
        $slotHours    = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00'];

        // PHP colour palette — mirrors the JS PALETTE const below, used only for dataset colours
        $phpPalette = ['#8B0000','#C9A84C','#3B82F6','#10B981','#F59E0B','#6366F1','#EF4444','#14B8A6'];

        // Build datasets: one per facility, values = avg demand per hour (summed across all days)
        $heatmapDatasets = [];
        $fi = 0;
        foreach ($facilityList as $facilityId => $facilityName) {
            if (!isset($heatmapData[$facilityId])) { $fi++; continue; }
            $facilityDow = $heatmapData[$facilityId];
            $hourValues  = [];
            foreach ($slotHours as $hour) {
                $total = 0;
                foreach ($facilityDow as $dow => $hours) {
                    $total += $hours[$hour] ?? 0;
                }
                $hourValues[] = round($total, 2);
            }
            $heatmapDatasets[] = [
                'label'           => $facilityName,
                'data'            => $hourValues,
                'backgroundColor' => $phpPalette[$fi % count($phpPalette)],
                'borderRadius'    => 4,
            ];
            $fi++;
        }
    @endphp
    (function() {
        const ctx = document.getElementById('heatmapChart');
        if (!ctx) return;
        const datasets = {!! json_encode($heatmapDatasets) !!};
        if (!datasets.length) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($slotHours) !!},
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y.toFixed(2) + ' avg bookings/wk'
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, stacked: false },
                    y: { grid: { color: '#E2E8F0' }, ticks: { callback: v => v.toFixed(1) } }
                }
            }
        });
    })();
    </script>

</x-app-layout>
