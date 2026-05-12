@php
    use Illuminate\Support\Facades\Auth;

    // Check each guard independently
    $doctorUser  = Auth::guard('doctor')->user();
    $patientUser = Auth::guard('web')->user();

    // The "active" user for this request is whichever guard matches the current URL
    $isDoctor  = $doctorUser  && $doctorUser->role  === 'doctor';
    $isPatient = $patientUser && $patientUser->role === 'patient';

    // Determine which user context we're operating in based on the URL prefix
    $inDoctorArea  = request()->is('doctor*') || request()->is('admin*');
    $currentUser   = $inDoctorArea ? $doctorUser : ($patientUser ?? $doctorUser);

    if ($isDoctor && $inDoctorArea) {
        $dashboardRoute = route('doctor.dashboard');
        $profileRoute   = route('doctor.profile');
        $activeGuard    = 'doctor';
        $navLinks = [
            ['label' => 'Dashboard',    'route' => route('doctor.dashboard'),         'path' => 'doctor/dashboard'],
            ['label' => 'Triage',       'route' => route('doctor.followup.index'),    'path' => 'doctor/followup'],
            ['label' => 'Lab Results',  'route' => route('doctor.lab_results.index'), 'path' => 'doctor/lab-results'],
            ['label' => 'Prescriptions','route' => route('doctor.prescriptions.index'),'path'=> 'doctor/prescriptions'],
        ];
    } elseif ($isPatient) {
        $dashboardRoute = route('patient.dashboard');
        $profileRoute   = route('patient.profile');
        $activeGuard    = 'web';
        $navLinks = [
            ['label' => 'Dashboard',     'route' => route('patient.dashboard'),          'path' => 'patient/dashboard'],
            ['label' => 'Lab Results',   'route' => route('patient.labResults.index'),   'path' => 'patient/lab-results'],
            ['label' => 'Prescriptions', 'route' => route('patient.prescriptions.index'),'path' => 'patient/prescriptions'],
            ['label' => 'Follow-Up',     'route' => route('patient.followup.create'),    'path' => 'patient/followup'],
        ];
    } else {
        $dashboardRoute = url('/');
        $profileRoute   = '#';
        $activeGuard    = 'web';
        $navLinks       = [];
    }

    $anyLoggedIn = $isDoctor || $isPatient;
@endphp

<nav x-data="{ mobileOpen: false, profileOpen: false }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">

        <!-- Logo -->
        <a href="{{ $dashboardRoute }}" class="flex items-center gap-2.5 flex-shrink-0">
            <div class="w-8 h-8 rounded-md bg-brand-700 flex items-center justify-center text-white font-extrabold text-sm tracking-tight shadow-sm">H</div>
            <span class="text-lg font-bold text-surface-900 tracking-tight">
                Bleak<span class="text-brand-600">Hospital</span>
            </span>
        </a>

        <!-- Desktop Nav Links -->
        @if($anyLoggedIn)
        <div class="hidden md:flex items-center gap-1">
            @foreach($navLinks as $link)
                @php $active = request()->is($link['path'] . '*'); @endphp
                <a href="{{ $link['route'] }}"
                   class="px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-150 {{ $active ? 'bg-brand-50 text-brand-700' : 'text-surface-600 hover:bg-surface-100 hover:text-surface-900' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
        @endif

        <!-- Right: User Menu -->
        <div class="flex items-center gap-3">
            @if($anyLoggedIn)
                <!-- Role badge -->
                <span class="hidden sm:inline-flex {{ $isDoctor && $inDoctorArea ? 'badge-blue' : 'badge-green' }} badge text-xs">
                    {{ $isDoctor && $inDoctorArea ? 'Clinician' : 'Patient' }}
                </span>

                <!-- User dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-surface-200 bg-white text-sm font-medium text-surface-700 hover:bg-surface-50 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20">
                        <div class="w-7 h-7 rounded-full bg-brand-700 flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="max-w-[120px] truncate">{{ $currentUser->name ?? '' }}</span>
                        <svg class="w-3.5 h-3.5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-card-lg border border-surface-200 py-1 z-50">
                        <div class="px-4 py-2 border-b border-surface-100">
                            <p class="text-xs font-semibold text-surface-800 truncate">{{ $currentUser->name ?? '' }}</p>
                            <p class="text-xs text-surface-400 truncate">{{ $currentUser->email ?? '' }}</p>
                        </div>
                        <a href="{{ $profileRoute }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-surface-700 hover:bg-surface-50 transition">
                            <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Profile
                        </a>

                        {{-- Show switch link if BOTH roles are logged in --}}
                        @if($isDoctor && $isPatient)
                        <div class="border-t border-surface-100 mt-1 pt-1">
                            @if($inDoctorArea)
                            <a href="{{ route('patient.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-teal-700 hover:bg-teal-50 transition">
                                <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                Switch to Patient View
                            </a>
                            @else
                            <a href="{{ route('doctor.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-brand-700 hover:bg-brand-50 transition">
                                <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                Switch to Doctor View
                            </a>
                            @endif
                        </div>
                        @endif

                        <div class="border-t border-surface-100 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <input type="hidden" name="guard" value="{{ $activeGuard }}">
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-urgent-600 hover:bg-urgent-50 transition text-left">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-secondary btn-sm">Sign In</a>
                <a href="{{ route('register') }}" class="btn-primary btn-sm">Register</a>
            @endif

            <!-- Mobile Menu Button -->
            @if($anyLoggedIn)
            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg text-surface-500 hover:bg-surface-100 transition">
                <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            @endif
        </div>
    </div>

    <!-- Mobile Nav -->
    @if($anyLoggedIn)
    <div x-show="mobileOpen" x-transition class="md:hidden border-t border-surface-200 py-3 space-y-0.5">
        @foreach($navLinks as $link)
            @php $active = request()->is($link['path'] . '*'); @endphp
            <a href="{{ $link['route'] }}"
               class="flex px-3 py-2.5 rounded-lg text-sm font-medium {{ $active ? 'bg-brand-50 text-brand-700' : 'text-surface-600 hover:bg-surface-50' }}">
                {{ $link['label'] }}
            </a>
        @endforeach
    </div>
    @endif
</nav>
