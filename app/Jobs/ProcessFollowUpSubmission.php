<?php

namespace App\Jobs;

use App\Models\FollowUpSubmission;
use App\Services\AuditLogService;
use App\Services\Middleware\SubmissionValidationService;
use App\Services\Middleware\SubmissionTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessFollowUpSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public FollowUpSubmission $submission) {}

    public function handle(
        SubmissionValidationService $validator,
        SubmissionTransformer $transformer,
    ): void {
        // ── Stage 1: Load relationships needed downstream ─────
        $this->submission->load('patient');

        // ── Stage 2: Business Validation (Component 2) ────────
        $validation = $validator->validate($this->submission);

        if (!$validation->passes()) {
            $this->submission->update(['sync_status' => 'Failed']);

            AuditLogService::log(
                action:       'middleware_validation_failed',
                resourceType: 'follow_up_submission',
                resourceId:   $this->submission->id,
                outcome:      'failure',
                meta:         ['reason' => $validation->reason()]
            );

            return;
        }

        AuditLogService::log(
            action:       'middleware_validation_passed',
            resourceType: 'follow_up_submission',
            resourceId:   $this->submission->id,
            outcome:      'success',
            meta:         ['urgency' => $this->submission->urgency_level]
        );

        // ── Stage 3: Data Transformation (Component 3) ────────
        $payload = $transformer->toOpenMRSObservation($this->submission);

        AuditLogService::log(
            action:       'middleware_transformation_complete',
            resourceType: 'follow_up_submission',
            resourceId:   $this->submission->id,
            outcome:      'success',
            meta:         ['payload_keys' => array_keys($payload)]
        );

        // ── Stage 4: EMR Sync (stubbed until OpenMRS is ready) ─
        SyncSubmissionToEMR::dispatch($this->submission, $payload);
    }

    public function failed(\Throwable $e): void
    {
        $this->submission->update(['sync_status' => 'Failed']);

        AuditLogService::log(
            action:       'middleware_pipeline_failed',
            resourceType: 'follow_up_submission',
            resourceId:   $this->submission->id,
            outcome:      'failure',
            meta:         ['error' => $e->getMessage()]
        );
    }
}