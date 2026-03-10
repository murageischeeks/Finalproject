<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AuditLogService
{
    public static function log(
        string $action,
        string $resourceType,
        int    $resourceId,
        string $outcome,
        array  $meta = []
    ): void {
        DB::table('audit_logs')->insert([
            'user_id'       => auth()->id(),
            'action'        => $action,
            'resource_type' => $resourceType,
            'resource_id'   => $resourceId,
            'outcome'       => $outcome,
            'meta'          => json_encode($meta),
            'created_at'    => now(),
        ]);
    }
}