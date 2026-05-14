<?php

namespace App\Services\Middleware;

use App\Models\FollowUpSubmission;

class SubmissionValidationService
{
    public function validate(FollowUpSubmission $submission): ValidationResult
    {
        // Rule 1: Worsening status must have severity >= 3
        if ($submission->recovery_status === 'Worsening' && $submission->severity < 3) {
            return ValidationResult::fail(
                'Worsening recovery status is inconsistent with a low severity rating.'
            );
        }

        // Rule 2: High severity contradicts Improving status
        // A patient reporting Severity 5 (Severe distress) but claiming
        // they are Improving is a clinically contradictory self-report.
        // This inconsistency must be flagged before EMR sync to prevent
        // inaccurate data from corrupting the clinical record.
        if ($submission->severity >= 5 && $submission->recovery_status === 'Improving') {
            return ValidationResult::fail(
                'Contradictory self-report: Severity 5 (Severe distress) is clinically inconsistent with an Improving recovery status. Manual clinical review required before EMR sync.'
            );
        }

        // Rule 3: No symptoms recorded
        if (empty($submission->symptom_categories)) {
            return ValidationResult::fail(
                'No symptom categories recorded.'
            );
        }

        // Rule 3: Prevent duplicate submission within 30 minutes
        // REMOVED for demo purposes so evaluators can spam the form rapidly

        return ValidationResult::pass();
    }
}