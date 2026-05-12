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
use Illuminate\Http\Request;
use App\Http\Controllers\EmrReceiverController;

class SyncSubmissionToEMR implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 120, 600];

    public function __construct(
        public FollowUpSubmission $submission,
        public array $payload,
        public ?int $userId = null
    ) {
        // Capture the authenticated user ID at dispatch time (HTTP context)
        $this->userId = $userId ?? auth()->id();
    }

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
            ],
            userId:       $this->userId
        );

        // ── Bypass HTTP POST to avoid deadlock on local single-threaded server ──
        // Since we are simulating OpenMRS within the same app, we can just call the controller directly.
        $request = Request::create('/api/emr/observations', 'POST', $this->payload);
        $controller = new EmrReceiverController();
        $response = $controller->store($request);

        $statusCode = $response->getStatusCode();

        if ($statusCode === 201) {
            // ── Sync successful ────────────────────────────────
            $responseData = $response->getData(true);
            $observationUuid = $responseData['uuid'];

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
                    'emr_response'     => $responseData,
                    'http_status'      => 201,
                ],
                userId:       $this->userId
            );

        } else {
            // ── Sync failed — trigger retry ────────────────────
            AuditLogService::log(
                action:       'emr_sync_failed',
                resourceType: 'follow_up_submission',
                resourceId:   $this->submission->id,
                outcome:      'failure',
                meta:         [
                    'http_status'   => $statusCode,
                    'response_body' => $response->getContent(),
                    'attempt'       => $this->attempts(),
                ],
                userId:       $this->userId
            );

            throw new \Exception(
                "EMR sync failed with HTTP {$statusCode}: {$response->getContent()}"
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
            ],
            userId:       $this->userId
        );
    }
}