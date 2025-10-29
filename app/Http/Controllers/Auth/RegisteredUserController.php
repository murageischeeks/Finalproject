<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Show registration form.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // ✅ Validation
        $request->validate([
            'name'             => ['required','string','max:255'],
            'email'            => ['required','string','email','max:255','unique:users'],
            'password'         => ['required','confirmed', Rules\Password::defaults()],
            'role'             => ['required', 'in:patient,doctor'],

            // only required if role is doctor
            'license_number'   => ['required_if:role,doctor','nullable','string','max:255'],
            'department'       => ['required_if:role,doctor','nullable','string','max:255'],
            'specialization'   => ['required_if:role,doctor','nullable','string','max:255'],
            'specialization_other' => ['nullable','string','max:255'],
        ]);

        // Handle "Other" specialization input
        $specialization = $request->input('specialization');
        if ($specialization === 'Other') {
            $specialization = $request->input('specialization_other');
        }

        // ✅ Create user
        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role'           => $request->role,
            'license_number' => $request->role === 'doctor' ? $request->license_number : null,
            'department'     => $request->role === 'doctor' ? $request->department : null,
            'specialization' => $request->role === 'doctor' ? $specialization : null,
        ]);

        // Fire Registered event + login
        event(new Registered($user));
        Auth::login($user);

        // ✅ Redirect based on role
        return match($user->role) {
            'doctor'  => redirect()->route('doctor.dashboard'),
            'patient' => redirect()->route('patient.dashboard'),
            default   => redirect(RouteServiceProvider::HOME),
        };
    }
}
