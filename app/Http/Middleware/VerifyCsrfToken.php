<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * The simulated OpenMRS EMR receiver endpoints are excluded because
     * they are called server-to-server by the SyncSubmissionToEMR queue
     * job, which does not carry a CSRF token.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/emr/*',
    ];
}
