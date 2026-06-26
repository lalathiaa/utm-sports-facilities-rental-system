<?php

namespace App\Services;

use App\Contracts\AiExplainerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GeminiExplainerService — Layer 2 (Optional LLM Enrichment)
 * ============================================================
 * Calls the Google Gemini API (free tier) to generate a natural-language
 * explanation of a recommendation or analytics insight.
 *
 * FREE TIER LIMITS (as of mid-2025, Gemini 1.5 Flash):
 *   - 15 requests per minute
 *   - 1,000,000 tokens per day
 *   - No billing / credit card required
 *   - Obtain key at: https://aistudio.google.com/app/apikey
 *
 * PRIVACY: Only aggregated, anonymised statistical context is sent.
 *          No user names, IDs, emails, or booking details.
 *
 * GRACEFUL DEGRADATION:
 *   - If the API key is missing → falls back to TemplateExplainerService.
 *   - If the HTTP call fails or times out → exception is caught, logs a warning,
 *     falls back to TemplateExplainerService. The UI never breaks.
 */
class GeminiExplainerService implements AiExplainerInterface
{
    private const API_URL  = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
    private const TIMEOUT  = 5; // seconds — must not block the page load

    public function __construct(
        private readonly TemplateExplainerService $fallback
    ) {}

    /**
     * Generate a natural-language recommendation reason via Gemini API.
     * Falls back to TemplateExplainerService on any failure.
     */
    public function explain(array $context): string
    {
        $apiKey = config('services.gemini.key');

        if (empty($apiKey)) {
            // Key not configured — use template fallback silently
            return $this->fallback->explain($context);
        }

        try {
            $prompt = $this->buildPrompt($context);

            $response = Http::timeout(self::TIMEOUT)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(self::API_URL . '?key=' . $apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 80,
                        'temperature'     => 0.4,
                    ],
                ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                if (!empty($text)) {
                    return trim($text);
                }
            }

            Log::warning('GeminiExplainerService: unexpected response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('GeminiExplainerService: API call failed', [
                'error' => $e->getMessage(),
            ]);
        }

        // Fall back to template explanation on any failure
        return $this->fallback->explain($context);
    }

    /**
     * Build a concise prompt from Layer 1 stats.
     * Designed to produce a single, plain-English sentence.
     */
    private function buildPrompt(array $context): string
    {
        $rating     = $context['rating'];
        $popularity = $context['popularity'];
        $slot       = $context['slot'];
        $name       = $context['facility_name'];
        $score      = $context['score'];
        $isPersonal = $context['is_personal'] ?? false;
        $isCold     = $context['is_cold_start'] ?? false;

        $personalNote = $isPersonal
            ? "The user has previously booked this facility."
            : ($isCold ? "The user is new with no booking history." : "The user has not booked this facility before.");

        return <<<PROMPT
You are a sports facility booking assistant for a Malaysian university.
Write exactly ONE concise English sentence (max 25 words) explaining why "{$name}" is recommended.
Use these facts only:
- Star rating: {$rating}/5
- Total past bookings at this facility: {$popularity}
- Best available slot: {$slot}
- Recommendation score: {$score}/10
- {$personalNote}

Do not invent facts. Do not greet the user. Output only the one sentence.
PROMPT;
    }
}
