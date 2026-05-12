<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1"><!-- NFR3: min responsive width 320px -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BleakHospital') }} — @yield('title', 'Portal')</title>
    <meta name="description" content="BleakHospital patient and clinical portal — manage appointments, follow-ups, lab results, and prescriptions.">

    <!-- Plus Jakarta Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="antialiased bg-surface-50 min-h-screen flex flex-col relative">

    <!-- Top Navigation Bar -->
    <header id="topbar" class="bg-white/70 backdrop-blur-md border-b border-surface-200/50 sticky top-0 z-50 transition-shadow duration-200 shadow-sm">
        @include('layouts.navigation')
    </header>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4 space-y-2">
        @if(session('success'))
            <div class="alert alert-success animate-fade-in" data-auto-dismiss="5000">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error animate-fade-in" data-auto-dismiss="6000">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error animate-fade-in" data-auto-dismiss="8000">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Page Header Slot -->
    @isset($header)
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-6">
            <div class="flex items-center justify-between">
                {{ $header }}
            </div>
        </div>
    @endisset

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 animate-fade-in">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- Footer -->
    <footer class="mt-auto bg-white border-t border-surface-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-2 text-sm text-surface-500">
                <span class="w-6 h-6 rounded bg-brand-700 flex items-center justify-center text-white font-bold text-xs">H</span>
                <span>BleakHospital Portal</span>
                <span class="text-surface-300">·</span>
                <span>&copy; {{ date('Y') }}</span>
            </div>
            <div class="flex items-center gap-4 text-xs text-surface-400">
                <a href="{{ route('privacy') }}" class="hover:text-brand-600">Privacy Policy</a>
                <a href="{{ route('terms') }}" class="hover:text-brand-600">Terms of Service</a>
                <span class="text-surface-200">|</span>
                <span>v2.0 — EMR Middleware</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
