<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsService $service) {}

    /**
     * Predictive analytics report page for rental officers.
     *
     * Accepts optional ?lookback_days=N query parameter to narrow the window.
     * Default: null = all historical data.
     */
    public function index(Request $request): View
    {
        // null = all historical data; configurable via query param for demo flexibility
        $lookbackDays    = $request->query('lookback_days') ? (int) $request->query('lookback_days') : null;
        $projectionWeeks = (int) $request->query('projection_weeks', 4);

        $report = $this->service->getReport(
            lookbackDays:    $lookbackDays,
            projectionWeeks: $projectionWeeks
        );

        return view('analytics.index', compact('report', 'lookbackDays', 'projectionWeeks'));
    }
}
