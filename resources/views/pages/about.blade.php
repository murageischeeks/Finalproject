@extends('layouts.public')

@section('title', 'About Us')

@section('content')

<div class="page-hero">
    <div class="max-w-7xl mx-auto px-6">
        <nav class="text-brand-300 text-sm mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">Home</a>
            <span class="mx-2">›</span>
            <span class="text-white">About</span>
        </nav>
        <h1>About BleakHospital</h1>
        <p>Bridging clinical excellence with modern digital healthcare infrastructure.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-14">

    {{-- Mission block --}}
    <div class="grid lg:grid-cols-2 gap-12 mb-14 items-center">
        <div class="reveal-hidden">
            <h2 class="text-3xl font-bold text-surface-900 mb-4">Our Mission</h2>
            <p class="text-surface-600 leading-relaxed mb-4">
                At BleakHospital, we identified a critical gap in outpatient follow-up care. Patients leave clinics with little structured support. We built a platform to ensure no patient falls through the cracks after their visit.
            </p>
            <p class="text-surface-600 leading-relaxed mb-6">
                Our middleware sits between the patient and the national health infrastructure — receiving follow-up data, triaging it intelligently, and pushing it to KenyaEMR for permanent clinical record-keeping.
            </p>
            <div class="grid grid-cols-3 gap-4 text-center">
                @foreach([['25+','Specialists'],['5,000+','Patients Served'],['2010','Est.']] as [$num,$lbl])
                <div class="bg-surface-50 border border-surface-200 rounded-xl p-4">
                    <div class="text-2xl font-extrabold text-brand-700">{{ $num }}</div>
                    <div class="text-xs text-surface-500 mt-0.5">{{ $lbl }}</div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="bg-brand-50 border border-brand-100 rounded-2xl p-8 reveal-hidden">
            <h3 class="text-xl font-bold text-brand-800 mb-4">Why We Built This</h3>
            <ul class="space-y-4">
                @foreach([
                    'Disease-specific tools like Ushauri exist for HIV patients, but nothing existed for general outpatient follow-up.',
                    'Patients with post-surgical care, chronic conditions, or mental health needs had no structured digital touchpoint.',
                    'Clinical observations written on paper were never making it into KenyaEMR — creating dangerous gaps in patient records.',
                ] as $point)
                <li class="flex items-start gap-3 text-sm text-brand-700 leading-relaxed">
                    <svg class="w-5 h-5 text-brand-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ $point }}
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Values --}}
    <div class="mb-14">
        <h2 class="text-2xl font-bold text-surface-900 text-center mb-8">Our Core Values</h2>
        <div class="grid sm:grid-cols-3 gap-6">
            @foreach([
                ['Patient Safety First',   'Every design decision is evaluated through the lens of patient outcomes and safety.', 'text-urgent-600 bg-urgent-50'],
                ['Digital Interoperability', 'We build to open standards — FHIR, SNOMED, HL7 — so data flows freely and securely.', 'text-teal-600 bg-teal-50'],
                ['Radical Transparency',   'Every action in our system is auditable. We build accountability into the architecture.', 'text-medical-600 bg-medical-50'],
            ] as [$val,$desc,$color])
            <div class="card reveal-hidden text-center p-8">
                <div class="w-12 h-12 rounded-full {{ $color }} flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-surface-800 mb-2">{{ $val }}</h3>
                <p class="text-sm text-surface-500 leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Join CTA --}}
    @guest
    <div class="card bg-teal-700 border-teal-600 text-white text-center p-8">
        <h2 class="text-2xl font-bold mb-2">Join our Provider Network</h2>
        <p class="text-teal-200 mb-6">Are you a licensed healthcare professional? Register today and join our verified specialist network.</p>
        <a href="{{ route('register') }}" class="btn bg-white text-teal-700 hover:bg-teal-50 font-bold">Create Provider Account</a>
    </div>
    @endguest
</div>

@endsection
