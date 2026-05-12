<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'BleakHospital') }} — @yield('title', 'Modern Healthcare')</title>
    <meta name="description" content="BleakHospital — clinical excellence through digital precision. Patient portal, EMR integration, and real-time triage.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-surface-50 text-surface-800 min-h-screen flex flex-col">

    <!-- ── TOP BAR (Institution info strip) ── -->
    <div class="bg-brand-800 text-brand-200 text-xs py-1.5 border-b border-brand-900">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Emergency: 0800 123 456
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mon–Fri: 08:00–20:00 | Sat: 08:00–16:00
                </span>
            </div>
            <div class="hidden md:flex items-center gap-3">
                <span>Est. 2010 · Nairobi, Kenya</span>
            </div>
        </div>
    </div>

    <!-- ── PRIMARY NAVIGATION ── -->
    <nav class="bg-white border-b border-surface-200 sticky top-0 z-50 shadow-sm" id="topbar">
        <div class="max-w-7xl mx-auto px-6 py-0 flex items-center justify-between h-16">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg bg-brand-700 flex items-center justify-center text-white font-extrabold text-base shadow-sm">H</div>
                <div>
                    <span class="text-xl font-bold text-surface-900 tracking-tight">Bleak<span class="text-brand-600">Hospital</span></span>
                    <span class="block text-[10px] text-surface-400 leading-none tracking-wider">CLINICAL PORTAL</span>
                </div>
            </a>

            <!-- Nav links -->
            <div class="hidden md:flex items-center gap-1">
                @foreach([
                    ['Features',    route('features'),    'features'],
                    ['Specialists', route('specialists'), 'specialists'],
                    ['About',       route('about'),       'about'],
                ] as [$label, $href, $path])
                <a href="{{ $href }}"
                   class="px-3 py-2 rounded-md text-sm font-medium transition-all duration-150
                          {{ request()->is($path . '*') ? 'bg-brand-50 text-brand-700' : 'text-surface-600 hover:bg-surface-100 hover:text-surface-900' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>

            <!-- Auth CTA -->
            <div class="flex items-center gap-3">
                @php
                    $docUser = Auth::guard('doctor')->user();
                    $patUser = Auth::guard('web')->user();
                    $isDocLoggedIn = $docUser && $docUser->role === 'doctor';
                    $isPatLoggedIn = $patUser && $patUser->role === 'patient';
                @endphp
                @if($isDocLoggedIn || $isPatLoggedIn)
                    @if($isDocLoggedIn)
                    <span class="hidden sm:block text-xs font-semibold px-2 py-1 rounded-full bg-blue-100 text-blue-700">Doctor</span>
                    <a href="{{ route('doctor.dashboard') }}" class="btn-primary btn-sm">Doctor Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <input type="hidden" name="guard" value="doctor">
                        <button type="submit" class="text-xs font-medium text-surface-500 hover:text-urgent-600 transition">Logout Doctor</button>
                    </form>
                    @endif
                    @if($isPatLoggedIn)
                    <span class="hidden sm:block text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">Patient</span>
                    <a href="{{ route('patient.dashboard') }}" class="btn-secondary btn-sm">Patient Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <input type="hidden" name="guard" value="web">
                        <button type="submit" class="text-xs font-medium text-surface-500 hover:text-urgent-600 transition">Logout Patient</button>
                    </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-surface-600 hover:text-brand-600 transition">Sign In</a>
                    <a href="{{ route('register') }}" class="btn-primary btn-sm">Register</a>
                @endif
            </div>
        </div>
    </nav>

    <!-- ── CONTENT ── -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- ── FOOTER ── -->
    <footer class="bg-surface-800 text-surface-400 mt-auto">
        <div class="max-w-7xl mx-auto px-6 py-12 grid sm:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center text-white font-extrabold text-sm">H</div>
                    <span class="font-bold text-white text-lg">BleakHospital</span>
                </div>
                <p class="text-sm leading-relaxed">Bridging clinical excellence with modern digital healthcare infrastructure across Kenya.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-3 uppercase tracking-wider">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('features') }}" class="hover:text-brand-400 transition">Platform Features</a></li>
                    <li><a href="{{ route('specialists') }}" class="hover:text-brand-400 transition">Our Specialists</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-brand-400 transition">About Us</a></li>
                    <li><a href="/admin" class="hover:text-brand-400 transition">Admin Portal</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-3 uppercase tracking-wider">Legal & Compliance</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('privacy') }}" class="hover:text-brand-400 transition">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-brand-400 transition">Terms of Service</a></li>
                    <li><span class="text-surface-500">HIPAA-Aligned</span></li>
                    <li><span class="text-surface-500">FHIR R4 Compliant</span></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-surface-700 py-4">
            <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-surface-500">
                <span>&copy; {{ date('Y') }} BleakHospital. All rights reserved.</span>
                <span>KenyaEMR Middleware v2.0 — FHIR R4 Integration</span>
            </div>
        </div>
    </footer>

</body>
</html>
