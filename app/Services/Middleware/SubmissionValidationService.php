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
        $recentDuplicate = FollowUpSubmission::where('patient_id', $submission->patient_id)
            ->where('id', '!=', $submission->id)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->exists();

        if ($recentDuplicate) {
            return ValidationResult::fail(
                'A follow-up report was already submitted within the last 30 minutes.'
            );
        }

        return ValidationResult::pass();
    }
}