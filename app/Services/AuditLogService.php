<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AuditLogService
{
    /**
     * Write an immutable audit log entry.
     *
     * Uses raw DB::insert (not Eloquent) to enforce write-only semantics.
     * The audit_logs table has no updated_at column by design.
     *
     * @param  string      $action       e.g. 'submission_created', 'emr_sync_success'
     * @param  string      $resourceType e.g. 'follow_up_submission'
     * @param  int         $resourceId   Primary key of the resource
     * @param  string      $outcome      'success' | 'failure'
     * @param  array       $meta         Additional context (JSON-serialized)
     * @param  int|null    $userId       Explicit user ID — required for queue jobs
     *                                   where auth()->id() returns null
     */
    public static function log(
        string $action,
        string $resourceType,
        int    $resourceId,
        string $outcome,
        array  $meta = [],
        ?int   $userId = null
    ): void {
        DB::table('audit_logs')->insert([
            'user_id'       => $userId ?? auth()->id(),
            'action'        => $action,
            'resource_type' => $resourceType,
            'resource_id'   => $resourceId,
            'outcome'       => $outcome,
            'meta'          => json_encode($meta),
            'created_at'    => now(),
        ]);
    }
}