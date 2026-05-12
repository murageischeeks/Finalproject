<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Determine which guard the user is authenticated under.
        // auth:doctor sets the doctor guard; auth sets the web guard.
        $user = null;

        if (in_array('doctor', $roles) && Auth::guard('doctor')->check()) {
            $user = Auth::guard('doctor')->user();
            // Make Auth::user() work in controllers/views without specifying the guard.
            Auth::setUser($user);
        }

        if (!$user && Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
        }

        // ── Not authenticated at all ──────────────────────────────────────
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please log in to continue.');
        }

        // ── Wrong role → redirect to their own dashboard ──────────────────
        if (!in_array($user->role, $roles)) {
            return match ($user->role) {
                'doctor'  => redirect()->route('doctor.dashboard'),
                'patient' => redirect()->route('patient.dashboard'),
                default   => redirect()->route('login'),
            };
        }

        return $next($request);
    }
}