<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * SwitchSessionByRole
 *
 * Assigns a dedicated session cookie name based on the URL prefix:
 *   /doctor/* → cookie "bleakhospital_doctor_session"
 *   /patient/* → cookie "bleakhospital_patient_session"
 *
 * This allows a doctor and a patient to be logged in simultaneously in
 * the same browser (different tabs) without their sessions conflicting.
 *
 * Must be registered BEFORE the StartSession middleware runs so that
 * Laravel boots the correct session store from the start of the request.
 */
class SwitchSessionByRole
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('doctor*') || $request->is('admin/middleware-trace*')) {
            Config::set('session.cookie', 'bleakhospital_doctor_session');
        } elseif ($request->is('patient*')) {
            Config::set('session.cookie', 'bleakhospital_patient_session');
        }
        // All other routes (login page, landing, admin) keep the default cookie.

        return $next($request);
    }
}
