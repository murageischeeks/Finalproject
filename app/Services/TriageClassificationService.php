<?php

namespace App\Services;

use App\Models\FollowUpSubmission;
use App\Models\TriageRule;

class TriageClassificationService
{
    private array $rules;

    public function __construct()
    {
        // Group by rule_type, keyed by 'key' field for fast lookup
        $this->rules = [];

        TriageRule::all()->each(function ($rule) {
            $this->rules[$rule->rule_type][$rule->key] = (float) $rule->weight;
        });
    }

    public function classify(FollowUpSubmission $submission): string
    {
        $score = 0;

        // Step 1 — Sum symptom weights
        foreach ($submission->symptom_categories as $symptom) {
            $score += $this->rules['symptom_weight'][$symptom] ?? 0;
        }

        // Step 2 — Multiply by severity multiplier
        $multiplier = $this->rules['severity_multiplier'][(string) $submission->severity] ?? 1.0;
        $score *= $multiplier;

        // Step 3 — Add recovery status modifier
        $score += $this->rules['recovery_modifier'][$submission->recovery_status] ?? 0;

       
        return match (true) {
    $score >= 55 => 'High',
    $score >= 30 => 'Medium',
    default      => 'Low',
};
    }
}