#!/usr/bin/env python3
"""
analytics_engine.py — UTM Sports Facilities Analytics (pandas Layer)
=====================================================================
Called by AnalyticsService.php via Laravel's Process facade.

INPUT  : JSON object on stdin (booking rows + facilities + config).
OUTPUT : Single JSON object on stdout matching AnalyticsService::getReport() shape.

METHODOLOGY (viva-defensible):
  - Demand forecast  : Simple moving average — groupby(facility, dow, hour) ÷ n_weeks.
                       Appropriate for <50 data points; avoids overfitting.
  - Revenue trend    : Weekly totals + linear extrapolation of mean Δ (no scikit-learn).
                       Same method as PHP fallback — only implementation language changes.
  - Utilisation rate : booked_slots ÷ (days_in_window × 14 slots/day) × 100%.
  - Under-booking    : (facility, dow, hour) combinations where avg < threshold.

TIME WINDOW:
  All historical data by default (lookback_days = null). Set to a rolling window
  in production when data volume is large enough for trend charts to be meaningful.
"""

import sys
import json
import itertools
from datetime import datetime, date, timedelta

try:
    import pandas as pd
    PANDAS_AVAILABLE = True
except ImportError:
    PANDAS_AVAILABLE = False

# ── Constants ────────────────────────────────────────────────────────────────

SLOT_HOURS = [
    '08:00','09:00','10:00','11:00','12:00','13:00',
    '14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00',
]

DOW_LABELS = {
    0: 'Monday', 1: 'Tuesday', 2: 'Wednesday', 3: 'Thursday',
    4: 'Friday', 5: 'Saturday', 6: 'Sunday',
}


# ── Entry point ───────────────────────────────────────────────────────────────

def main():
    if not PANDAS_AVAILABLE:
        # Signal to PHP fallback — exit with error so PHP uses its own implementation
        sys.stderr.write("pandas not installed\n")
        sys.exit(1)

    raw = sys.stdin.read()
    payload = json.loads(raw)

    bookings_raw      = payload.get('bookings', [])
    facilities_raw    = payload.get('facilities', [])
    lookback_days     = payload.get('lookback_days')      # None = all data
    projection_weeks  = int(payload.get('projection_weeks', 4))
    underuse_thresh   = float(payload.get('underuse_threshold', 0.3))
    today_str         = payload.get('today', date.today().isoformat())
    today             = datetime.strptime(today_str, '%Y-%m-%d').date()
    slots_per_day     = int(payload.get('slots_per_day', 14))

    # Build facility id→name map (keys are ints from JSON decode)
    facility_names = {int(f['id']): f['name'] for f in facilities_raw}

    if not bookings_raw:
        print(json.dumps(empty_result(facility_names, slots_per_day, today, projection_weeks)))
        return

    # ── Build DataFrame ──────────────────────────────────────────────────────
    df = pd.DataFrame(bookings_raw)
    df['booking_date'] = pd.to_datetime(df['booking_date'])
    df['total_price']  = pd.to_numeric(df['total_price'], errors='coerce').fillna(0.0)
    df['facility_id']  = df['facility_id'].astype(int)

    # slot_start arrives as 'HH:MM' (already trimmed by PHP before passing)
    df['hour'] = df['slot_start'].str[:5]
    df['dow']  = df['booking_date'].dt.dayofweek   # 0=Monday … 6=Sunday
    df['iso_week'] = df['booking_date'].dt.strftime('%G-W%V')

    # Number of distinct ISO weeks in dataset (minimum 1 to avoid division by zero)
    n_weeks = max(1, df['iso_week'].nunique())

    result = {
        'demand_heatmap': compute_demand_heatmap(df, n_weeks),
        'top_slots':      compute_top_slots(df, n_weeks, facility_names),
        'revenue_trend':  compute_revenue_trend(df, projection_weeks),
        'utilisation':    compute_utilisation(df, facility_names, today, lookback_days, slots_per_day),
        'under_booked':   compute_under_booked(df, n_weeks, facility_names, underuse_thresh),
        'summary_stats':  compute_summary_stats(df),
    }

    print(json.dumps(result, default=str))


# ── Demand heatmap ────────────────────────────────────────────────────────────

def compute_demand_heatmap(df, n_weeks):
    """
    Returns nested dict: {facility_id_str: {dow_str: {hour: avg_per_week}}}
    Keys are strings because JSON object keys must be strings.
    PHP's normalizePythonOutput() casts them back to integers.
    """
    grouped = df.groupby(['facility_id', 'dow', 'hour']).size().reset_index(name='cnt')
    grouped['avg'] = (grouped['cnt'] / n_weeks).round(2)

    heatmap = {}
    for _, row in grouped.iterrows():
        fid  = str(int(row['facility_id']))
        dow  = str(int(row['dow']))
        hour = row['hour']
        heatmap.setdefault(fid, {}).setdefault(dow, {})[hour] = float(row['avg'])

    return heatmap


# ── Top slots (demand forecast table) ─────────────────────────────────────────

def compute_top_slots(df, n_weeks, facility_names):
    """
    Returns list of top-10 (facility, dow, hour) by avg_per_week.
    """
    grouped = df.groupby(['facility_id', 'dow', 'hour']).size().reset_index(name='cnt')
    grouped['avg_per_week'] = grouped['cnt'] / n_weeks
    grouped = grouped.sort_values('avg_per_week', ascending=False).head(10)

    result = []
    for _, row in grouped.iterrows():
        fid = int(row['facility_id'])
        dow = int(row['dow'])
        result.append({
            'facility_id':    fid,
            'facility_name':  facility_names.get(fid, 'Unknown'),
            'dow':            dow,
            'dow_label':      DOW_LABELS.get(dow, 'Unknown'),
            'hour':           row['hour'],
            'avg_per_week':   round(float(row['avg_per_week']), 2),
            'total_bookings': int(row['cnt']),
        })
    return result


# ── Revenue trend + projection ─────────────────────────────────────────────────

def compute_revenue_trend(df, projection_weeks):
    """
    Weekly revenue totals + linear extrapolation of mean week-over-week delta.
    Projection method: last_actual + k × mean_delta  (k = 1…projection_weeks).
    Negative projections are clamped to 0.
    """
    weekly = (df.groupby('iso_week')['total_price']
                .sum()
                .reset_index()
                .sort_values('iso_week'))

    labels  = weekly['iso_week'].tolist()
    actuals = [round(float(v), 2) for v in weekly['total_price'].tolist()]

    # Compute mean week-over-week delta
    deltas = [actuals[i] - actuals[i - 1] for i in range(1, len(actuals))]
    mean_delta = round(sum(deltas) / len(deltas), 2) if deltas else 0.0

    last_actual = actuals[-1] if actuals else 0.0

    # Generate projection labels (continuation of ISO week format)
    if labels:
        last_iso = labels[-1]          # e.g. '2026-W25'
        year_part, week_part = last_iso.split('-W')
        last_date = datetime.strptime(f"{last_iso}-1", "%G-W%V-%u").date()
    else:
        last_date = date.today()

    projected_labels = []
    projected = []
    for k in range(1, projection_weeks + 1):
        proj_date  = last_date + timedelta(weeks=k)
        proj_label = proj_date.strftime('%G-W%V') + ' (proj.)'
        projected_labels.append(proj_label)
        projected.append(max(0.0, round(last_actual + k * mean_delta, 2)))

    return {
        'labels':           labels,
        'actuals':          actuals,
        'projected_labels': projected_labels,
        'projected':        projected,
        'mean_delta':       mean_delta,
    }


# ── Utilisation rate ──────────────────────────────────────────────────────────

def compute_utilisation(df, facility_names, today, lookback_days, slots_per_day):
    """
    Utilisation % = booked_slots ÷ (window_days × slots_per_day) × 100.
    Conservative: FacilityClosures are NOT subtracted from denominator.
    """
    if df.empty:
        return [
            {'facility_id': fid, 'facility_name': name,
             'utilisation': 0.0, 'booked_slots': 0, 'total_slots': 0}
            for fid, name in facility_names.items()
        ]

    if lookback_days is not None:
        window_start = today - timedelta(days=lookback_days)
    else:
        # All historical: use earliest booking date
        window_start = df['booking_date'].min().date()

    window_days = max(1, (today - window_start).days + 1)
    total_slots = window_days * slots_per_day

    booked = df.groupby('facility_id').size().to_dict()

    result = []
    for fid, name in facility_names.items():
        b = int(booked.get(fid, 0))
        result.append({
            'facility_id':   int(fid),
            'facility_name': name,
            'utilisation':   round((b / total_slots) * 100, 1) if total_slots > 0 else 0.0,
            'booked_slots':  b,
            'total_slots':   total_slots,
        })
    return result


# ── Under-booked combinations ─────────────────────────────────────────────────

def compute_under_booked(df, n_weeks, facility_names, underuse_threshold):
    """
    Flags (facility, dow, hour) where avg weekly demand < underuse_threshold.
    Checks all combinations (including those never appearing in data → avg = 0).
    Returns top 15 worst-performing (lowest demand first).
    """
    # Build demand lookup
    demand_map = {}
    if not df.empty:
        grouped = df.groupby(['facility_id', 'dow', 'hour']).size()
        for (fid, dow, hour), cnt in grouped.items():
            demand_map[(int(fid), int(dow), hour)] = cnt / n_weeks

    suggestions = []
    for fid, fname in facility_names.items():
        for dow in range(7):
            for hour in SLOT_HOURS:
                avg = demand_map.get((int(fid), dow, hour), 0.0)
                if avg < underuse_threshold:
                    h = int(hour[:2])
                    period = 'mornings' if h < 12 else ('afternoons' if h < 17 else 'evenings')
                    dow_label = DOW_LABELS[dow]
                    suggestions.append({
                        'facility_id':   int(fid),
                        'facility_name': fname,
                        'dow':           dow,
                        'dow_label':     dow_label,
                        'hour':          hour,
                        'avg_per_week':  round(avg, 2),
                        'suggestion':    (
                            f"{fname} is under-booked on {dow_label} {period} ({hour}). "
                            "Consider a promotion or price adjustment."
                        ),
                    })

    suggestions.sort(key=lambda x: x['avg_per_week'])
    return suggestions[:15]


# ── Summary stats ─────────────────────────────────────────────────────────────

def compute_summary_stats(df):
    if df.empty:
        return {'total_bookings': 0, 'total_revenue': 0.0,
                'unique_facilities': 0, 'date_range_start': None, 'date_range_end': None}
    return {
        'total_bookings':    int(len(df)),
        'total_revenue':     round(float(df['total_price'].sum()), 2),
        'unique_facilities': int(df['facility_id'].nunique()),
        'date_range_start':  df['booking_date'].min().strftime('%Y-%m-%d'),
        'date_range_end':    df['booking_date'].max().strftime('%Y-%m-%d'),
    }


# ── Empty result (no bookings) ────────────────────────────────────────────────

def empty_result(facility_names, slots_per_day, today, projection_weeks):
    return {
        'demand_heatmap': {},
        'top_slots':      [],
        'revenue_trend':  {'labels': [], 'actuals': [], 'projected_labels': [],
                           'projected': [], 'mean_delta': 0.0},
        'utilisation':    [
            {'facility_id': int(fid), 'facility_name': name,
             'utilisation': 0.0, 'booked_slots': 0, 'total_slots': 0}
            for fid, name in facility_names.items()
        ],
        'under_booked':   [],
        'summary_stats':  {'total_bookings': 0, 'total_revenue': 0.0,
                           'unique_facilities': 0, 'date_range_start': None,
                           'date_range_end': None},
    }


if __name__ == '__main__':
    main()
