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

        // Rule 2: No symptoms recorded
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