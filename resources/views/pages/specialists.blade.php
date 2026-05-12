@extends('layouts.public')

@section('title', 'Our Specialists & Departments')

@section('content')

<div class="page-hero">
    <div class="max-w-7xl mx-auto px-6">
        <nav class="text-brand-300 text-sm mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">Home</a>
            <span class="mx-2">›</span>
            <span class="text-white">Specialists</span>
        </nav>
        <h1>Our Specialists</h1>
        <p>Connect with board-certified physicians across all major clinical disciplines.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-14">

    {{-- Intro --}}
    <div class="text-center mb-10">
        <p class="text-surface-600 max-w-2xl mx-auto text-base leading-relaxed">
            BleakHospital is home to over <strong class="text-surface-800">25 verified specialists</strong> across 8 clinical departments.
            All providers are licensed, background-verified, and integrated with our digital triage system.
        </p>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-14">
        @foreach([
            ['Cardiology',        'Heart & vascular conditions, coronary artery disease, heart failure, arrhythmias.', 4,  'text-urgent-600 bg-urgent-50'],
            ['Neurology',         'Brain and nervous system disorders, stroke, epilepsy, Parkinson\'s disease.', 3, 'text-brand-600 bg-brand-50'],
            ['Pediatrics',        'Comprehensive child and adolescent care from birth through age 18.', 6, 'text-teal-600 bg-teal-50'],
            ['General Practice',  'Primary care, preventive medicine, routine check-ups, referrals.', 8, 'text-medical-600 bg-medical-50'],
            ['Orthopedics',       'Bone, joint, and musculoskeletal conditions and sports injuries.', 2, 'text-amber-600 bg-amber-50'],
            ['Obstetrics & Gynecology', 'Women\'s health, maternal care, reproductive medicine.', 3, 'text-pink-600 bg-pink-50'],
            ['Psychiatry',        'Mental health, counseling, behavioral therapy, mood disorders.', 2, 'text-purple-600 bg-purple-50'],
            ['Ophthalmology',     'Eye diseases, vision correction, cataract surgery, glaucoma.', 2, 'text-indigo-600 bg-indigo-50'],
        ] as [$dept, $desc, $count, $color])
        <div class="card reveal-hidden hover:shadow-card-md transition-shadow">
            <div class="card-body">
                <div class="w-10 h-10 rounded-xl {{ $color }} flex items-center justify-center mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-surface-800 text-base mb-1">{{ $dept }}</h3>
                <p class="text-xs text-surface-500 leading-relaxed mb-3">{{ $desc }}</p>
                <div class="flex items-center justify-between">
                    <span class="badge-blue">{{ $count }} Specialist{{ $count > 1 ? 's' : '' }}</span>
                    @auth
                        <a href="{{ route('patient.dashboard') }}" class="text-xs text-brand-600 font-semibold hover:underline">Book →</a>
                    @else
                        <a href="{{ route('register') }}" class="text-xs text-brand-600 font-semibold hover:underline">Register →</a>
                    @endauth
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- CTA --}}
    <div class="bg-brand-700 rounded-2xl p-8 text-white text-center">
        <h2 class="text-2xl font-bold mb-2">Find the right specialist for you</h2>
        <p class="text-brand-200 mb-6">Register as a patient and browse available doctors in our secure portal.</p>
        @auth
            <a href="{{ route('patient.dashboard') }}" class="btn bg-white text-brand-700 hover:bg-brand-50 font-bold">
                Open Dashboard & Book
            </a>
        @else
            <div class="flex justify-center gap-3">
                <a href="{{ route('register') }}" class="btn bg-white text-brand-700 hover:bg-brand-50 font-bold">Create Account</a>
                <a href="{{ route('login') }}" class="btn border-2 border-white/40 text-white hover:bg-white/10">Sign In</a>
            </div>
        @endauth
    </div>
</div>

@endsection
