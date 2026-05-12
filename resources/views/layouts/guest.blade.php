<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1"><!-- NFR3: min responsive width 320px -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BleakHospital') }} — @yield('title', 'Secure Access')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-surface-50 min-h-screen flex flex-col">

    <!-- Minimal auth nav -->
    <nav class="bg-white border-b border-surface-200">
        <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-md bg-brand-700 flex items-center justify-center text-white font-extrabold text-sm">H</div>
                <span class="text-lg font-bold text-surface-900 tracking-tight">Bleak<span class="text-brand-600">Hospital</span></span>
            </a>
            <a href="{{ route('home') }}" class="text-sm text-surface-500 hover:text-brand-600 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Home
            </a>
        </div>
    </nav>

    <!-- Content -->
    <div class="flex-1 flex flex-col justify-center items-center px-4 py-12">
        <div class="w-full max-w-md">
            <!-- Branding above card -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brand-700 text-white font-extrabold text-2xl shadow-card-md mb-4">H</div>
                <h1 class="text-2xl font-bold text-surface-900">@yield('auth-title', 'Secure Access')</h1>
                <p class="text-sm text-surface-500 mt-1">@yield('auth-subtitle', 'Sign in to your BleakHospital account')</p>
            </div>

            <!-- Auth card -->
            <div class="bg-white border border-surface-200 rounded-2xl shadow-card-md p-8">
                {{ $slot }}
            </div>

            <!-- Legal note -->
            <p class="text-center text-xs text-surface-400 mt-6">
                By accessing this portal, you agree to our
                <a href="{{ route('terms') }}" class="underline hover:text-brand-600">Terms of Service</a>
                and
                <a href="{{ route('privacy') }}" class="underline hover:text-brand-600">Privacy Policy</a>.
            </p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 text-xs text-surface-400 border-t border-surface-100">
        BleakHospital &copy; {{ date('Y') }} — Authorized Personnel Only
    </footer>
</body>
</html>
