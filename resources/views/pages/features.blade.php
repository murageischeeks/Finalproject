@extends('layouts.public')

@section('title', 'Platform Features')

@section('content')

<div class="page-hero">
    <div class="max-w-7xl mx-auto px-6">
        <nav class="text-brand-300 text-sm mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">Home</a>
            <span class="mx-2">›</span>
            <span class="text-white">Features</span>
        </nav>
        <h1>Platform Features</h1>
        <p>Everything you need to deliver, manage, and audit clinical care — digitally.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-14">

    <div class="grid md:grid-cols-2 gap-8 mb-14">

        @foreach([
            [
                'title' => 'Automated Triage Engine',
                'color' => 'text-brand-600 bg-brand-50',
                'icon'  => 'clock',
                'desc'  => 'Our proprietary triage engine evaluates patient submissions in milliseconds based on clinical guidelines. Each submission receives an urgency score (High / Medium / Low), and high-risk cases trigger an immediate alert to the assigned clinician via email.',
                'features' => ['Real-time urgency scoring', 'Instant clinician email notifications', 'Symptom-category-based routing', 'Priority queue management'],
            ],
            [
                'title' => 'KenyaEMR Synchronization',
                'color' => 'text-teal-600 bg-teal-50',
                'icon'  => 'shield',
                'desc'  => 'Eliminate double data entry. Patient follow-ups submitted through our portal are automatically transformed into FHIR R4-standard Observation resources and queued for synchronization directly into your OpenMRS/KenyaEMR instance.',
                'features' => ['Background queue processing (Redis)', 'Automated retry on failure', 'FHIR R4 standard output', 'SNOMED/LOINC code mapping'],
            ],
            [
                'title' => 'Immutable Audit Logs',
                'color' => 'text-medical-600 bg-medical-50',
                'icon'  => 'document',
                'desc'  => 'Security and regulatory compliance are built-in. Every user action, data mutation, EMR sync attempt, and authentication event is recorded in an append-only audit log with full user attribution — even for background queue jobs.',
                'features' => ['Tamper-evident log entries', 'Background job user attribution', 'Admin oversight panel (Filament)', 'Exportable audit records'],
            ],
            [
                'title' => 'Role-Based Access Control',
                'color' => 'text-purple-600 bg-purple-50',
                'icon'  => 'lock',
                'desc'  => 'A multi-guard authentication system ensures complete isolation between patients, clinicians, and administrators. Patients see only their own data. Doctors see only their assigned cases. Admins have system-wide oversight.',
                'features' => ['Patient / Doctor / Admin isolation', 'Separate auth guards', 'License verification workflow', 'Filament admin dashboard'],
            ],
        ] as $f)
        <div class="card reveal-hidden">
            <div class="card-body">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl {{ $f['color'] }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($f['icon']==='clock')<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @elseif($f['icon']==='shield')<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            @elseif($f['icon']==='document')<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            @else<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            @endif
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-surface-900 mt-1">{{ $f['title'] }}</h2>
                </div>
                <p class="text-surface-600 text-sm leading-relaxed mb-4">{{ $f['desc'] }}</p>
                <ul class="space-y-1.5">
                    @foreach($f['features'] as $feat)
                    <li class="flex items-center gap-2 text-sm text-surface-700">
                        <svg class="w-4 h-4 text-medical-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ $feat }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tech Stack --}}
    <div class="card bg-surface-50 border-surface-200">
        <div class="card-body">
            <h2 class="text-xl font-bold text-surface-900 mb-4">Technology Stack</h2>
            <div class="grid sm:grid-cols-4 gap-4">
                @foreach(['Laravel 12','MySQL 8','Redis Queues','FHIR R4'] as $tech)
                <div class="bg-white border border-surface-200 rounded-xl p-4 text-center">
                    <p class="font-semibold text-surface-800 text-sm">{{ $tech }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection
