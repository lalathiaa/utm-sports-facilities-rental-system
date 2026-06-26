<?php

namespace App\Contracts;

interface AiExplainerInterface
{
    /**
     * Generate a natural-language explanation from pre-computed statistical context.
     *
     * The $context array contains only aggregated, non-PII statistics from Layer 1.
     * No user names, IDs, or personal data are sent to any external service.
     *
     * @param  array<string, mixed> $context
     * @return string  Plain-text sentence(s) explaining the recommendation or insight.
     */
    public function explain(array $context): string;
}
