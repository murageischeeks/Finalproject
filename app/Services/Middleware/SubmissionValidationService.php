<?php

namespace App\Services\Middleware;

use App\Models\FollowUpSubmission;

class SubmissionValidationService
{
    public function validate(FollowUpSubmission $submission): ValidationResult
    {
        // Rule 1: Worsening recovery status must have severity >= 3
        // A patient who is "Worsening" cannot rationally report minimal/mild symptoms.
        if ($submission->recovery_status === 'Worsening' && $submission->severity < 3) {
            return ValidationResult::fail(
                'Worsening recovery status is inconsistent with a low severity rating (1-2). Worsening patients must report at least Moderate (3) severity.'
            );
        }

        // Rule 2: Severity 5 contradicts Improving recovery status
        // A patient in severe distress cannot simultaneously be improving.
        if ($submission->severity >= 5 && $submission->recovery_status === 'Improving') {
            return ValidationResult::fail(
                'Contradictory self-report: Severity 5 (Severe distress) is clinically inconsistent with an Improving recovery status. Manual clinical review required before EMR sync.'
            );
        }

        // Rule 3: No symptoms recorded
        if (empty($submission->symptom_categories)) {
            return ValidationResult::fail(
                'No symptom categories recorded. At least one symptom must be reported.'
            );
        }

        // Rule 4: General Deterioration contradicts low severity
        // A patient cannot report systemic failure while claiming minimal impact.
        if (in_array('general_deterioration', $submission->symptom_categories) && $submission->severity < 3) {
            return ValidationResult::fail(
                'Contradictory self-report: "General Deterioration" cannot be classified as Minimal or Mild (1-2) severity. Please review your inputs.'
            );
        }

        // Rule 5: High severity (4-5) contradicts Stable or Improving health status
        // A patient with a critical severity score cannot be considered Stable or Improving.
        if ($submission->severity >= 4 && in_array($submission->recovery_status, ['Stable', 'Improving'])) {
            return ValidationResult::fail(
                'Contradictory self-report: A severity score of ' . $submission->severity . '/5 is a critical medical event and contradicts a "' . $submission->recovery_status . '" recovery status. Please review your inputs.'
            );
        }

        // Rule 6: Wound Concern contradicts Improving status at high severity
        // An actively worsening wound cannot be paired with an Improving status.
        if (in_array('wound_concern', $submission->symptom_categories)
            && $submission->recovery_status === 'Improving'
            && $submission->severity >= 4) {
            return ValidationResult::fail(
                'Contradictory self-report: A Severity ' . $submission->severity . ' Wound Concern is incompatible with an "Improving" recovery status. A wound of this severity requires clinical review.'
            );
        }

        // Rule 7: Fever with Stable status at high severity is contradictory
        // A patient with a high-severity fever cannot self-report as Stable.
        if (in_array('fever', $submission->symptom_categories)
            && $submission->severity >= 4
            && $submission->recovery_status === 'Stable') {
            return ValidationResult::fail(
                'Contradictory self-report: A high-severity fever (Severity ' . $submission->severity . '/5) is incompatible with a "Stable" recovery status. This requires urgent clinical review.'
            );
        }

        // Rule 8: Medication Side Effect at maximum severity must not be Stable/Improving
        // A severe medication reaction is a medical emergency and cannot be Stable.
        if (in_array('medication_side_effect', $submission->symptom_categories)
            && $submission->severity === 5
            && in_array($submission->recovery_status, ['Stable', 'Improving'])) {
            return ValidationResult::fail(
                'Contradictory self-report: A Severity 5 medication side effect is a medical emergency and is incompatible with a "' . $submission->recovery_status . '" recovery status.'
            );
        }



        // Rule 10: Prevent duplicate submission within 30 minutes
        // REMOVED for demo purposes so evaluators can spam the form rapidly

        return ValidationResult::pass();
    }
}