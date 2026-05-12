@extends('layouts.public')

@section('title', 'Clinical Excellence, Digital Precision')

@section('content')

{{-- ── HERO ── --}}
<section class="relative bg-surface-0 border-b border-surface-100 overflow-hidden">
    <!-- Background blurred orb -->
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-brand-300/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-teal-300/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/3 pointer-events-none"></div>
    
    <div class="max-w-7xl mx-auto px-6 py-16 lg:py-24 flex flex-col lg:flex-row items-center gap-12 relative z-10">

        {{-- Left copy --}}
        <div class="flex-1 text-left animate-fade-in-up">
            <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-brand-600 bg-brand-50 border border-brand-100 rounded-full px-3 py-1 mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-pulse-slow"></span>
                Official Patient & Provider Portal
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-surface-900 tracking-tight leading-tight mb-6">
                Clinical Excellence,<br>
                <span class="text-gradient">Digital Precision.</span>
            </h1>
            <p class="text-lg text-surface-500 leading-relaxed mb-8 max-w-xl">
                BleakHospital provides seamless outpatient follow-ups, secure digital records, and real-time clinician communication — all in one HIPAA-aligned platform.
            </p>

            <div class="flex flex-col sm:flex-row gap-3">
                @auth
                    <a href="{{ auth()->user()->role === 'doctor' ? route('doctor.dashboard') : route('patient.dashboard') }}"
                       class="btn-modern btn-lg">
                        Access Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn-modern btn-lg">
                        Register as Patient
                    </a>
                    <a href="{{ route('login') }}" class="btn-modern-outline btn-lg">
                        Provider Login
                    </a>
                @endauth
            </div>

            {{-- Trust indicators --}}
            <div class="mt-10 flex flex-wrap items-center gap-6">
                <div class="flex items-center gap-2 text-sm text-surface-500">
                    <svg class="w-4 h-4 text-medical-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    HIPAA-Aligned
                </div>
                <div class="flex items-center gap-2 text-sm text-surface-500">
                    <svg class="w-4 h-4 text-medical-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    KenyaEMR Integrated
                </div>
                <div class="flex items-center gap-2 text-sm text-surface-500">
                    <svg class="w-4 h-4 text-medical-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    FHIR R4 Compliant
                </div>
            </div>
        </div>

        {{-- Right: Platform Visual --}}
        <div class="flex-1 w-full max-w-lg animate-fade-in-up delay-200">
            <div class="relative">
                {{-- Main card --}}
                <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-white/60 shadow-glass p-8">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-600 mb-5">Platform at a glance</p>

                    {{-- Stats grid --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-brand-50 rounded-2xl p-5 border border-brand-100">
                            <div class="text-3xl font-black text-brand-700 mb-1">FHIR</div>
                            <div class="text-xs font-semibold text-brand-500">R4 Compliant</div>
                            <div class="text-xs text-surface-400 mt-1">HL7 Standard</div>
                        </div>
                        <div class="bg-medical-50 rounded-2xl p-5 border border-medical-100">
                            <div class="text-3xl font-black text-medical-700 mb-1">256</div>
                            <div class="text-xs font-semibold text-medical-600">AES Encrypted</div>
                            <div class="text-xs text-surface-400 mt-1">Data at rest</div>
                        </div>
                        <div class="bg-teal-50 rounded-2xl p-5 border border-teal-100">
                            <div class="text-3xl font-black text-teal-700 mb-1">TLS</div>
                            <div class="text-xs font-semibold text-teal-600">1.3 Secured</div>
                            <div class="text-xs text-surface-400 mt-1">In transit</div>
                        </div>
                        <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100">
                            <div class="text-3xl font-black text-amber-700 mb-1">EMR</div>
                            <div class="text-xs font-semibold text-amber-600">KenyaEMR Sync</div>
                            <div class="text-xs text-surface-400 mt-1">Real-time</div>
                        </div>
                    </div>

                    {{-- Pipeline flow indicator --}}
                    <div class="bg-surface-50 rounded-xl p-4 border border-surface-100">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-surface-400 mb-3">Middleware pipeline</p>
                        <div class="flex items-center gap-1 text-xs">
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-md font-semibold">Auth</span>
                            <span class="text-surface-300">→</span>
                            <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded-md font-semibold">Validate</span>
                            <span class="text-surface-300">→</span>
                            <span class="px-2 py-1 bg-purple-100 text-purple-600 rounded-md font-semibold">Transform</span>
                            <span class="text-surface-300">→</span>
                            <span class="px-2 py-1 bg-green-100 text-green-600 rounded-md font-semibold">Sync</span>
                        </div>
                    </div>
                </div>

                {{-- Floating badge --}}
                <div class="absolute -top-4 -right-4 bg-brand-700 text-white rounded-2xl px-4 py-2 shadow-card-lg">
                    <p class="text-[10px] font-bold uppercase tracking-wider opacity-80">Status</p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-medical-400 animate-pulse-slow"></span>
                        <span class="text-sm font-bold">System Online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── CAPABILITIES STRIP ── --}}
<section class="bg-surface-50 border-b border-surface-200 py-14">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-surface-900">Built for Modern Healthcare</h2>
            <p class="mt-2 text-surface-500">Enterprise tools for clinical workflows.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['icon'=>'clock','title'=>'Automated Triage','desc'=>'Real-time symptom scoring instantly escalates high-urgency cases.','color'=>'text-brand-600 bg-brand-50'],
                ['icon'=>'shield-check','title'=>'EMR Synchronization','desc'=>'Patient data syncs directly to KenyaEMR via FHIR R4 standard.','color'=>'text-teal-600 bg-teal-50'],
                ['icon'=>'document-text','title'=>'Immutable Audit Logs','desc'=>'Every action is recorded in a tamper-proof audit trail.','color'=>'text-medical-600 bg-medical-50'],
                ['icon'=>'lock-closed','title'=>'RBAC Security','desc'=>'Strict role-based access keeps patient data private and secure.','color'=>'text-purple-600 bg-purple-50'],
            ] as $feature)
            <div class="bg-white border border-surface-200 rounded-xl p-6 hover:shadow-card-md transition-shadow reveal-hidden">
                <div class="w-10 h-10 rounded-lg {{ $feature['color'] }} flex items-center justify-center mb-4">
                    @if($feature['icon'] === 'clock')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($feature['icon'] === 'shield-check')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    @elseif($feature['icon'] === 'document-text')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    @endif
                </div>
                <h3 class="font-semibold text-surface-800 mb-1.5">{{ $feature['title'] }}</h3>
                <p class="text-sm text-surface-500 leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── HOW IT WORKS ── --}}
<section class="bg-white border-b border-surface-200 py-14">
    <div class="max-w-5xl mx-auto px-6">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-surface-900">How It Works</h2>
            <p class="mt-2 text-surface-500">The full pipeline, from patient to clinician in seconds.</p>
        </div>
        <div class="grid sm:grid-cols-3 gap-8">
            @foreach([
                ['step'=>'1','title'=>'Patient Submits','desc'=>'Patient logs in and submits a follow-up with symptoms, severity, and notes from their dashboard.'],
                ['step'=>'2','title'=>'Triage Scores','desc'=>'Our engine evaluates urgency and dispatches an alert to the assigned clinician in real-time.'],
                ['step'=>'3','title'=>'EMR Synchronized','desc'=>'The observation is automatically written to KenyaEMR as a FHIR-standard record with full audit trail.'],
            ] as $step)
            <div class="text-center reveal-hidden delay-{{ ($loop->index) * 100 }}">
                <div class="w-12 h-12 rounded-full bg-brand-700 text-white font-bold text-lg flex items-center justify-center mx-auto mb-4 shadow-card-md">
                    {{ $step['step'] }}
                </div>
                <h3 class="font-semibold text-surface-800 mb-2">{{ $step['title'] }}</h3>
                <p class="text-sm text-surface-500 leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── CTA BANNER ── --}}
@guest
<section class="bg-brand-700 py-14">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Ready to get started?</h2>
        <p class="text-brand-200 mb-8 text-lg">Join thousands of patients managing their healthcare digitally.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('register') }}" class="btn bg-white text-brand-700 hover:bg-brand-50 btn-lg font-bold shadow-card-lg">
                Create Patient Account
            </a>
            <a href="{{ route('login') }}" class="btn border-2 border-white/40 text-white hover:bg-white/10 btn-lg">
                Provider Sign In
            </a>
        </div>
    </div>
</section>
@endguest

@endsection
