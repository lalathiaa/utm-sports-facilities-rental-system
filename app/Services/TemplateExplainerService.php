<?php

namespace App\Services;

use App\Contracts\AiExplainerInterface;

/**
 * TemplateExplainerService — Layer 2 Fallback (PHP String Templates)
 * ====================================================================
 * Generates natural-language recommendation reasons from Layer 1 numbers
 * using pure PHP string formatting. No external dependencies.
 *
 * This is always available — it is the guaranteed fallback when:
 *   - GEMINI_API_KEY is not set in .env
 *   - The Gemini API is unreachable or returns an error
 *   - Any exception occurs in GeminiExplainerService
 *
 * Output quality: deterministic, factually grounded, viva-defensible.
 * Example: "Badminton Court A is recommended with a 4.2★ rating and
 *           high demand at 18:00 system-wide (score: 8.4/10)."
 */
class TemplateExplainerService implements AiExplainerInterface
{
    /**
     * Generate a plain-English explanation from Layer 1 statistics.
     *
     * @param  array<string, mixed> $context
     * @return string
     */
    public function explain(array $context): string
    {
        $name       = $context['facility_name'] ?? 'This facility';
        $rating     = $context['rating']        ?? 0;
        $popularity = (int) ($context['popularity']    ?? 0);
        $slot       = $context['slot']          ?? '';
        $score      = $context['score']         ?? 0;
        $isPersonal = $context['is_personal']   ?? false;
        $isCold     = $context['is_cold_start'] ?? false;
        $slotDemand = (int) ($context['slot_demand']   ?? 0);

        // ── Rating part ────────────────────────────────────────────────────
        if ($rating >= 4.5) {
            $ratingPart = "an excellent {$rating}★ rating";
        } elseif ($rating >= 3.5) {
            $ratingPart = "a solid {$rating}★ rating";
        } elseif ($rating > 0) {
            $ratingPart = "a {$rating}★ rating";
        } else {
            $ratingPart = "no ratings yet (new facility)";
        }

        // ── Popularity part ────────────────────────────────────────────────
        if ($popularity >= 10) {
            $popularityPart = "is highly popular ({$popularity} past bookings)";
        } elseif ($popularity >= 3) {
            $popularityPart = "has {$popularity} past bookings";
        } elseif ($popularity === 1) {
            $popularityPart = "has 1 recorded booking";
        } else {
            $popularityPart = "is a new addition with no prior bookings";
        }

        // ── Personal affinity part ─────────────────────────────────────────
        if ($isPersonal) {
            $personalPart = ", and you have booked it before";
        } elseif ($isCold) {
            $personalPart = "";
        } else {
            $personalPart = "";
        }

        // ── Slot demand part ───────────────────────────────────────────────
        if (!empty($slot)) {
            $slotEnd  = date('H:i', strtotime($slot) + 3600);
            if ($slotDemand >= 5) {
                $slotPart = "The {$slot}–{$slotEnd} slot is in high demand.";
            } elseif ($slotDemand >= 2) {
                $slotPart = "The {$slot}–{$slotEnd} slot sees moderate demand.";
            } else {
                $slotPart = "The {$slot}–{$slotEnd} slot is often available.";
            }
        } else {
            $slotPart = "";
        }

        // ── Compose final sentence ─────────────────────────────────────────
        return "{$name} has {$ratingPart} and {$popularityPart}{$personalPart}. {$slotPart} (Score: {$score}/10)";
    }
}
