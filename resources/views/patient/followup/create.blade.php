@extends('layouts.app')

@section('content')
<div class="py-10 px-4">
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('patient.dashboard') }}"
           class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Submit Follow-Up Report</h1>
            <p class="text-sm text-gray-500 mt-0.5">Tell your doctor how you are recovering after your visit</p>
        </div>
    </div>

    {{-- Validation Errors & Security Firewall Blocks --}}
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                @if($errors->has('security_block'))
                    <h3 class="text-red-800 font-bold mb-1">🔥 FIREWALL INTERCEPTION</h3>
                    <p class="text-sm text-red-700 font-semibold">{{ $errors->first('security_block') }}</p>
                @else
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('patient.followup.store') }}" class="space-y-5">
        @csrf

        {{-- Doctor Selection --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 bg-brand-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">Select Doctor <span class="text-red-500">*</span></h2>
                    <p class="text-xs text-gray-400">Which doctor did you consult with?</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($doctors as $doctor)
                <label class="flex items-center gap-3 p-3.5 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-brand-300 hover:bg-brand-50 transition-all duration-150">
                    <input
                        type="radio"
                        name="doctor_id"
                        value="{{ $doctor->id }}"
                        class="w-4 h-4 text-brand-600 rounded-full"
                        {{ old('doctor_id') == $doctor->id ? 'checked' : '' }}
                        required
                    >
                    <div class="w-8 h-8 rounded-full bg-brand-700 text-white flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr($doctor->name, 0, 1)) }}
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-gray-700 block">{{ $doctor->name }}</span>
                        <span class="text-xs text-gray-400">{{ $doctor->specialization ?? 'Specialist' }}</span>
                    </div>
                </label>
                @endforeach
            </div>
            @if($doctors->isEmpty())
                <p class="text-amber-600 text-xs mt-3 bg-amber-50 p-3 rounded-lg border border-amber-100">
                    ⚠️ You haven't had any appointments yet. Please book an appointment first.
                </p>
            @endif
            @error('doctor_id')
                <p class="text-red-500 text-xs mt-3">{{ $message }}</p>
            @enderror
        </div>

        {{-- Symptom Categories --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">Symptoms <span class="text-red-500">*</span></h2>
                    <p class="text-xs text-gray-400">Select all that apply</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                @foreach([
                    'fever'                  => ['label' => 'Fever',                  'icon' => '🌡️'],
                    'pain'                   => ['label' => 'Pain',                   'icon' => '😣'],
                    'swelling'               => ['label' => 'Swelling',               'icon' => '🫧'],
                    'medication_side_effect' => ['label' => 'Medication Side Effect', 'icon' => '💊'],
                    'wound_concern'          => ['label' => 'Wound Concern',          'icon' => '🩹'],
                    'general_deterioration'  => ['label' => 'General Deterioration',  'icon' => '📉'],
                    'other'                  => ['label' => 'Other / Unlisted',       'icon' => '❓'],
                ] as $value => $item)
                <label class="flex items-center gap-3 p-3.5 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all duration-150">
                    <input
                        type="checkbox"
                        name="symptom_categories[]"
                        value="{{ $value }}"
                        class="w-4 h-4 text-blue-600 rounded"
                        {{ in_array($value, old('symptom_categories', [])) ? 'checked' : '' }}
                    >
                    <span class="text-lg leading-none">{{ $item['icon'] }}</span>
                    <span class="text-sm font-medium text-gray-700">{{ $item['label'] }}</span>
                </label>
                @endforeach
            </div>
            @error('symptom_categories')
                <p class="text-red-500 text-xs mt-3">{{ $message }}</p>
            @enderror
        </div>

        {{-- Severity --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">Severity Level <span class="text-red-500">*</span></h2>
                    <p class="text-xs text-gray-400">1 = Minimal &nbsp;·&nbsp; 5 = Severe</p>
                </div>
            </div>
            <div class="flex gap-2">
                @php
                    $severityLabels = [1 => 'Minimal', 2 => 'Mild', 3 => 'Moderate', 4 => 'Serious', 5 => 'Severe'];
                    $severityColors = [
                        1 => 'checked:bg-green-500',
                        2 => 'checked:bg-lime-500',
                        3 => 'checked:bg-yellow-500',
                        4 => 'checked:bg-orange-500',
                        5 => 'checked:bg-red-500',
                    ];
                    $bgColors = [
                        1 => 'bg-green-500',
                        2 => 'bg-lime-500',
                        3 => 'bg-yellow-500',
                        4 => 'bg-orange-500',
                        5 => 'bg-red-500',
                    ];
                @endphp
                @for ($i = 1; $i <= 5; $i++)
                <label class="flex-1 text-center cursor-pointer group">
                    <input type="radio" name="severity" value="{{ $i }}"
                           id="severity_{{ $i }}"
                           class="sr-only"
                           {{ old('severity') == $i ? 'checked' : '' }}>
                    <div id="severity_box_{{ $i }}"
                         onclick="selectSeverity({{ $i }})"
                         class="py-3 rounded-xl border-2 border-gray-200 font-bold text-gray-500 text-lg
                                hover:border-gray-300 transition-all duration-150 cursor-pointer
                                {{ old('severity') == $i ? $bgColors[$i] . ' text-white border-transparent' : '' }}">
                        {{ $i }}
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $severityLabels[$i] }}</p>
                </label>
                @endfor
            </div>
            @error('severity')
                <p class="text-red-500 text-xs mt-3">{{ $message }}</p>
            @enderror
        </div>

        {{-- Recovery Status --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">Recovery Status <span class="text-red-500">*</span></h2>
                    <p class="text-xs text-gray-400">How are you feeling overall?</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                @php
                    $statusConfig = [
                        'Improving' => ['icon' => '📈', 'border' => 'border-green-500',  'bg' => 'bg-green-50'],
                        'Stable'    => ['icon' => '➡️',  'border' => 'border-blue-500',   'bg' => 'bg-blue-50'],
                        'Worsening' => ['icon' => '📉', 'border' => 'border-red-500',    'bg' => 'bg-red-50'],
                        'Uncertain' => ['icon' => '❓', 'border' => 'border-yellow-500', 'bg' => 'bg-yellow-50'],
                    ];
                @endphp
                @foreach($statusConfig as $status => $config)
                <label class="flex items-center gap-3 p-3.5 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-gray-300 transition-all duration-150"
                       id="status_label_{{ $loop->index }}"
                       onclick="selectStatus('{{ $status }}', '{{ $config['border'] }}', '{{ $config['bg'] }}')">
                    <input type="radio" name="recovery_status" value="{{ $status }}"
                           id="status_{{ $status }}"
                           class="w-4 h-4 text-blue-600"
                           {{ old('recovery_status') === $status ? 'checked' : '' }}>
                    <span class="text-lg leading-none">{{ $config['icon'] }}</span>
                    <span class="text-sm font-medium text-gray-700">{{ $status }}</span>
                </label>
                @endforeach
            </div>
            @error('recovery_status')
                <p class="text-red-500 text-xs mt-3">{{ $message }}</p>
            @enderror
        </div>

        {{-- Notes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">
                        Additional Notes
                        <span class="text-gray-400 font-normal text-sm">(optional)</span>
                    </h2>
                    <p class="text-xs text-gray-400">Maximum 500 characters</p>
                </div>
            </div>
            <textarea
                name="notes"
                rows="4"
                maxlength="500"
                placeholder="Describe anything else your doctor should know..."
                class="w-full border border-gray-200 rounded-xl p-3.5 text-sm text-gray-700
                       focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent
                       resize-none transition bg-gray-50"
            >{{ old('notes') }}</textarea>
            @error('notes')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                       text-white font-semibold py-3.5 rounded-2xl transition-all duration-150
                       flex items-center justify-center gap-2 text-base">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Submit Follow-Up Report
        </button>

    </form>
</div>
</div>



{{-- Modal 2: Page-load Interception Report (Backend Blocked) --}}
@if($errors->has('security_block') && session('failed_submission_id'))
<div id="interception-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm transition-opacity"></div>
    {{-- Modal Panel --}}
    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-red-200 flex flex-col animate-alert-bounce">
        <div class="bg-red-600 px-6 py-4 flex items-center gap-3 text-white">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-black tracking-wide">🔥 ATTACK INTERCEPTED</h3>
                <p class="text-xs text-red-200 font-semibold">Active Security Firewall Block</p>
            </div>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div class="space-y-1">
                <p class="text-sm font-bold text-slate-800">Security Gate 0 Triggered</p>
                <p class="text-sm text-slate-600 leading-relaxed">
                    A critical security policy violation was intercepted. The transaction has been permanently blocked, and an immutable log entry has been appended to the clinical audit trail.
                </p>
            </div>
            <div class="bg-red-50/50 border border-red-100 rounded-xl p-4 space-y-2">
                <div class="flex items-center gap-2 text-xs font-semibold text-red-800">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-ping"></span>
                    Status: Active Interception Blocked
                </div>
                <p class="text-xs text-red-700 font-mono">
                    {{ $errors->first('security_block') }}
                </p>
            </div>
        </div>
        <div class="bg-slate-50 px-6 py-4 flex rounded-b-2xl">
            <button type="button" onclick="closeInterception()" class="w-full inline-flex justify-center rounded-xl bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white font-bold px-4 py-3 text-sm transition shadow-md shadow-slate-300">
                Dismiss and Close
            </button>
        </div>
    </div>
</div>
@endif

<style>
    @keyframes alertBounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .animate-alert-bounce {
        animation: alertBounce 1.5s ease-in-out 3;
    }
</style>

<script>
    // Severity selector
    const severityColors = {
        1: 'bg-green-500',
        2: 'bg-lime-500',
        3: 'bg-yellow-500',
        4: 'bg-orange-500',
        5: 'bg-red-500',
    };

    function selectSeverity(value) {
        for (let i = 1; i <= 5; i++) {
            const box = document.getElementById('severity_box_' + i);
            if (box) {
                box.className = box.className
                    .replace(/bg-\w+-500/g, '')
                    .replace(/text-white/g, '')
                    .replace(/border-transparent/g, '');
                box.classList.add('border-gray-200', 'text-gray-500');
            }
        }
        const selected = document.getElementById('severity_box_' + value);
        if (selected) {
            selected.classList.remove('border-gray-200', 'text-gray-500');
            selected.classList.add(severityColors[value], 'text-white', 'border-transparent');
        }
        const radio = document.getElementById('severity_' + value);
        if (radio) {
            radio.checked = true;
        }
    }

    // Recovery status selector
    function selectStatus(status, borderClass, bgClass) {
        document.querySelectorAll('[id^="status_label_"]').forEach(el => {
            el.className = el.className
                .replace(/border-\w+-500/g, 'border-gray-100')
                .replace(/bg-\w+-50/g, '');
        });
        const label = document.querySelector(`label[onclick*="${status}"]`);
        if (label) {
            label.classList.add(borderClass, bgClass);
        }
    }



    // Initialize old values if they exist
    window.addEventListener('DOMContentLoaded', () => {
        const activeSeverityInput = document.querySelector('input[name="severity"]:checked');
        if (activeSeverityInput) {
            selectSeverity(activeSeverityInput.value);
        }
        const activeRecoveryInput = document.querySelector('input[name="recovery_status"]:checked');
        if (activeRecoveryInput) {
            const statusVal = activeRecoveryInput.value;
            const recoveryMap = {
                'Improving': { border: 'border-green-500', bg: 'bg-green-50' },
                'Stable': { border: 'border-blue-500', bg: 'bg-blue-50' },
                'Worsening': { border: 'border-red-500', bg: 'bg-red-50' },
                'Uncertain': { border: 'border-yellow-500', bg: 'bg-yellow-50' }
            };
            const map = recoveryMap[statusVal];
            if (map) {
                selectStatus(statusVal, map.border, map.bg);
            }
        }
    });

    function closeInterception() {
        const modal = document.getElementById('interception-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>

@endsection
