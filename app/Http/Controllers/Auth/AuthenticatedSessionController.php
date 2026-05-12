<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login form.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * Doctors authenticate against the 'doctor' guard, patients against the
     * default 'web' guard. This gives each role its own session key
     * (login_doctor_XXXHASH vs login_web_XXXHASH), allowing both to be
     * active simultaneously in the same browser.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // First try the doctor guard
        if (Auth::guard('doctor')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::guard('doctor')->user();

            if ($user->role === 'doctor') {
                return redirect()->route('doctor.dashboard');
            }

            // Not a doctor — log them out of doctor guard and fall through
            Auth::guard('doctor')->logout();
        }

        // Try the web (patient/admin) guard
        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::guard('web')->user();

            if ($user->role === 'patient') {
                return redirect()->route('patient.dashboard');
            }

            if ($user->role === 'admin') {
                return redirect('/admin');
            }

            // Unknown role
            Auth::guard('web')->logout();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session (logout).
     *
     * Each logout form specifies which guard to log out of via a hidden field,
     * so logging out as a patient does NOT end the doctor session and vice versa.
     */
    public function destroy(Request $request)
    {
        $guard = $request->input('guard', 'web');

        // Only allow known guards
        if (!in_array($guard, ['web', 'doctor'])) {
            $guard = 'web';
        }

        Auth::guard($guard)->logout();

        // Only invalidate the full session if no one is still logged in
        if (!Auth::guard('web')->check() && !Auth::guard('doctor')->check()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/');
    }
}
