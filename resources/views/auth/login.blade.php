<x-guest-layout>
@section('auth-title', 'Welcome back')
@section('auth-subtitle', 'Sign in to your BleakHospital account')

@if(session('status'))
    <div class="alert alert-success mb-4 text-sm">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <div class="input-group">
        <label for="email">Email Address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               required autofocus autocomplete="username"
               placeholder="doctor@hospital.com">
        @error('email')<p class="input-error">{{ $message }}</p>@enderror
    </div>

    <div class="input-group">
        <div class="flex items-center justify-between mb-1">
            <label for="password" class="mb-0">Password</label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-brand-600 hover:underline">
                    Forgot password?
                </a>
            @endif
        </div>
        <input id="password" type="password" name="password"
               required autocomplete="current-password"
               placeholder="••••••••">
        @error('password')<p class="input-error">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-2 mt-1">
        <input id="remember_me" type="checkbox" name="remember"
               class="w-4 h-4 rounded border-surface-300 text-brand-600 focus:ring-brand-600">
        <label for="remember_me" class="mb-0 text-sm text-surface-600 font-normal">Keep me signed in</label>
    </div>

    <button type="submit" class="btn-primary btn w-full mt-2 py-2.5">
        Sign In
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
    </button>

    <p class="text-center text-sm text-surface-500 mt-4">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-brand-600 font-semibold hover:underline">Register here</a>
    </p>
</form>
</x-guest-layout>
