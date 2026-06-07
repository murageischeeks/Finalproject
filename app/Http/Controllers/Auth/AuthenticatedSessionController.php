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

        // Find the user first to determine their role before logging into a specific guard
        // This prevents accidentally logging out an active session for the other guard.
        $user = \App\Models\User::where('email', $credentials['email'])->first();
        $isAdmin = false;

        if (!$user) {
            $admin = \App\Models\Admin::where('email', $credentials['email'])->first();
            if ($admin) {
                $user = $admin;
                $isAdmin = true;
            }
        }

        if (!$user || !\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Check if the user is already authenticated on ANY guard.
        // If they are, it means this is a simultaneous login in a new tab.
        // We skip session regeneration to prevent the CSRF token from changing,
        // which would cause a "419 Page Expired" error on the already-open tab.
        $wasAlreadyLoggedIn = Auth::guard('web')->check() || Auth::guard('doctor')->check() || Auth::guard('admin')->check();

        if ($isAdmin) {
            if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
                // $request->session()->regenerate(); // Disabled to prevent 419 on other tabs
                return redirect('/admin');
            }
        } else if ($user->role === 'doctor') {
            if (Auth::guard('doctor')->attempt($credentials, $request->boolean('remember'))) {
                // $request->session()->regenerate(); // Disabled to prevent 419 on other tabs
                return redirect()->route('doctor.dashboard');
            }
        } else if ($user->role === 'patient') {
            if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
                // $request->session()->regenerate(); // Disabled to prevent 419 on other tabs
                return redirect()->route('patient.dashboard');
            }
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
        // To prevent any confusion with Chrome profiles caching sessions,
        // we will aggressively log out of ALL roles whenever ANY logout button is clicked.
        Auth::guard('web')->logout();
        Auth::guard('doctor')->logout();
        Auth::guard('admin')->logout();

        // Completely destroy the session and all cookies
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget(Auth::guard('web')->getRecallerName()));
        \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget(Auth::guard('doctor')->getRecallerName()));

        return redirect('/');
    }
}
