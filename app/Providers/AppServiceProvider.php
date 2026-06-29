<?php

namespace App\Providers;

use App\Contracts\AiExplainerInterface;
use App\Services\GeminiExplainerService;
use App\Services\TemplateExplainerService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoApiTransport;

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
        // Railway terminates HTTPS at the load balancer and forwards plain
        // HTTP to the container. Force all generated URLs to use HTTPS in
        // production so asset(), url(), and route() never produce http:// links.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register custom Brevo transport for Symfony Mailer
        Mail::extend('brevo', function ($app) {
            return new BrevoApiTransport(
                config('services.brevo.key'),
                new \Symfony\Component\HttpClient\Psr18Client()
            );
        });
    }
}

