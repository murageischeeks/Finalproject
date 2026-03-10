<?php

namespace App\Jobs;

use App\Models\FollowUpSubmission;
use App\Services\AuditLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SyncSubmissionToEMR implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 120, 600];

    public function __construct(
        public FollowUpSubmission $submission,
        public array $payload
    ) {}

    public function handle(): void
    {
        // ── Add submission ID to payload for cross-reference ──
        $this->payload['follow_up_submission_id'] = $this->submission->id;

        AuditLogService::log(
            action:       'emr_sync_initiated',
            resourceType: 'follow_up_submission',
            resourceId:   $this->submission->id,
            outcome:      'success',
            meta:         [
                'endpoint'    => config('openmrs.base_url') . '/api/emr/observations',
                'person'      => $this->payload['person'] ?? 'unknown',
                'concept'     => $this->payload['concept'],
                'obsDatetime' => $this->payload['obsDatetime'],
            ]
        );

        // ── POST to simulated OpenMRS receiver ─────────────────
        $response = Http::withHeaders([
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(
            config('openmrs.base_url') . '/api/emr/observations',
            $this->payload
        );

        if ($response->status() === 201) {
            // ── Sync successful ────────────────────────────────
            $observationUuid = $response->json('uuid');

            $this->submission->update([
                'sync_status'              => 'Synced',
                'openmrs_observation_uuid' => $observationUuid,
            ]);

            AuditLogService::log(
                action:       'emr_sync_success',
                resourceType: 'follow_up_submission',
                resourceId:   $this->submission->id,
                outcome:      'success',
                meta:         [
                    'observation_uuid' => $observationUuid,
                    'emr_response'     => $response->json(),
                    'http_status'      => 201,
                ]
            );

        } else {
            // ── Sync failed — trigger retry ────────────────────
            AuditLogService::log(
                action:       'emr_sync_failed',
                resourceType: 'follow_up_submission',
                resourceId:   $this->submission->id,
                outcome:      'failure',
                meta:         [
                    'http_status'   => $response->status(),
                    'response_body' => $response->body(),
                    'attempt'       => $this->attempts(),
                ]
            );

            throw new \Exception(
                "EMR sync failed with HTTP {$response->status()}: {$response->body()}"
            );
        }
    }

    public function failed(\Throwable $e): void
    {
        $this->submission->update(['sync_status' => 'Failed']);

        AuditLogService::log(
            action:       'emr_sync_permanently_failed',
            resourceType: 'follow_up_submission',
            resourceId:   $this->submission->id,
            outcome:      'failure',
            meta:         [
                'error'    => $e->getMessage(),
                'attempts' => $this->tries,
            ]
        );
    }
}