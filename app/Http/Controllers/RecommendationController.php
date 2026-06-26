<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    public function __construct(private readonly RecommendationService $service) {}

    /**
     * Full recommendations page for staff / student / guest.
     */
    public function index(): View
    {
        $recommendations = $this->service->recommend(Auth::id(), limit: 5);

        return view('recommendations.index', compact('recommendations'));
    }

    /**
     * Dashboard widget endpoint — returns top 2 recommendations as a partial.
     * Called inline from dashboard.blade.php via @php / compact pattern
     * to stay consistent with existing dashboard style (no AJAX needed).
     */
    public function widget(): View
    {
        $recommendations = $this->service->recommend(Auth::id(), limit: 2);

        return view('recommendations.widget', compact('recommendations'));
    }
}
