<?php

namespace App\Providers;

use App\Contracts\AiExplainerInterface;
use App\Services\GeminiExplainerService;
use App\Services\TemplateExplainerService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ── AI Explainer binding ────────────────────────────────────────────
        // GeminiExplainerService is the primary implementation.
        // It internally falls back to TemplateExplainerService when the API
        // key is absent or the call fails — so the feature never breaks.
        //
        // To swap providers (e.g. switch to Groq), change this single binding:
        $this->app->bind(AiExplainerInterface::class, function ($app) {
            return new GeminiExplainerService(
                fallback: $app->make(TemplateExplainerService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

