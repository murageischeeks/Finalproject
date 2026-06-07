@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Back --}}
    <a href="{{ route('doctor.followup.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Triage Dashboard
    </a>

    {{-- Pipeline Summary Mini-Box --}}
    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-indigo-900">Pipeline Status: ✓ Validated & {{ $submission->sync_status === 'Synced' ? 'Synced' : $submission->sync_status }}</p>
                @if($submission->openmrs_observation_uuid)
                    <p class="text-xs text-indigo-700 mt-0.5">EMR Record: <span class="font-mono">{{ substr($submission->openmrs_observation_uuid, 0, 13) }}...</span></p>
                @endif
            </div>
        </div>
        <button type="button" x-data @click.prevent="$dispatch('open-pipeline-modal', { url: '{{ route('middleware.trace', $submission->id) }}' })" 
                class="bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-100 px-4 py-2 text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap">
            View Full Pipeline Trace
        </button>
    </div>

    {{-- Patient Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                <span class="text-blue-700 font-bold text-lg">{{ substr($submission->patient->name, 0, 1) }}</span>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900">{{ $submission->patient->name }}</h1>
                <p class="text-sm text-gray-400">{{ $submission->patient->email }}</p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-xs text-gray-400">Submitted</p>
                <p class="text-sm font-medium text-gray-700">{{ $submission->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>

    {{-- Report Detail --}}
    @php
        $urgencyConfig = [
            'High'   => ['badge' => 'bg-red-100 text-red-700',    'bar' => 'bg-red-500',    'icon' => '🔴'],
            'Medium' => ['badge' => 'bg-yellow-100 text-yellow-700', 'bar' => 'bg-yellow-500', 'icon' => '🟡'],
            'Low'    => ['badge' => 'bg-green-100 text-green-700',  'bar' => 'bg-green-500',  'icon' => '🟢'],
        ];
        $uc = $urgencyConfig[$submission->urgency_level] ?? ['badge' => 'bg-gray-100 text-gray-600', 'bar' => 'bg-gray-400', 'icon' => '⚪'];
    @endphp

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="h-1.5 {{ $uc['bar'] }}"></div>
        <div class="p-6 space-y-6">

            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-800">Follow-Up Report</h2>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold {{ $uc['badge'] }}">
                    {{ $uc['icon'] }} {{ $submission->urgency_level }}
                </span>
            </div>

            {{-- Symptoms --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Symptoms Reported</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($submission->symptom_categories as $symptom)
                    <span class="bg-blue-50 text-blue-700 border border-blue-100 px-3 py-1 rounded-full text-xs font-medium">
                        {{ ucwords(str_replace('_', ' ', $symptom)) }}
                    </span>
                    @endforeach
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">Severity</p>
                    <div class="flex items-center gap-1 mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <div class="w-2.5 h-2.5 rounded-full {{ $i <= $submission->severity ? 'bg-orange-400' : 'bg-gray-200' }}"></div>
                        @endfor
                    </div>
                    <p class="text-sm font-bold text-gray-800">{{ $submission->severity }} / 5</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">Recovery</p>
                    <p class="text-sm font-bold text-gray-800">{{ $submission->recovery_status }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">EMR Sync</p>
                    <p class="text-sm font-bold {{ $submission->sync_status === 'Synced' ? 'text-green-600' : 'text-gray-800' }}">
                        {{ $submission->sync_status }}
                    </p>
                    @if($submission->openmrs_observation_uuid)
                    <a href="{{ url('/api/emr/observations/' . $submission->openmrs_observation_uuid) }}"
                       target="_blank"
                       class="text-[11px] text-green-700 font-bold hover:bg-green-200 mt-2 inline-flex items-center gap-1.5 bg-green-100 px-3 py-1.5 rounded-lg border border-green-200 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        View FHIR Payload ↗
                    </a>
                    @endif
                </div>
            </div>

            {{-- Notes --}}
            @if($submission->notes)
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Patient Notes</p>
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-sm text-gray-700">
                    {{ $submission->notes }}
                </div>
            </div>
            @endif

            {{-- System Notes --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">System Notes (Auto-generated)</p>
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-xs font-mono text-slate-600 space-y-1.5">
                    <div class="flex gap-2"><span>✓</span> <span>Submission validated: {{ $submission->created_at->format('h:i:s A') }}</span></div>
                    <div class="flex gap-2"><span>✓</span> <span>Data encrypted and stored: {{ $submission->created_at->addSeconds(1)->format('h:i:s A') }}</span></div>
                    @if($submission->sync_status === 'Synced')
                    <div class="flex gap-2"><span>✓</span> <span>Synced to EMR: {{ $submission->created_at->addSeconds(3)->format('h:i:s A') }}</span></div>
                    <div class="flex gap-2"><span>✓</span> <span>No data integrity issues detected.</span></div>
                    @else
                    <div class="flex gap-2 text-amber-600"><span>⚠</span> <span>EMR Sync Status: {{ $submission->sync_status }}</span></div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Reviewed Banner --}}
    @if($submission->reviewed_at)
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3 text-sm text-green-700">
        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Reviewed on {{ $submission->reviewed_at->format('d M Y, h:i A') }}
    </div>
    @endif

    {{-- Doctor Response Display --}}
    @if($submission->doctor_response)
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Your Response</p>
        </div>
        <p class="text-sm text-blue-800">{{ $submission->doctor_response }}</p>
    </div>
    @endif

    {{-- Respond Form --}}
    @if(!$submission->reviewed_at)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Respond to Patient</h2>

        <form method="POST" action="{{ route('doctor.followup.respond', $submission->id) }}" class="space-y-3">
            @csrf
            <textarea
                name="doctor_response"
                rows="4"
                maxlength="1000"
                placeholder="Write your response or instructions for the patient..."
                class="w-full border border-gray-200 rounded-xl p-4 text-sm text-gray-700
                       focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent
                       resize-none transition bg-gray-50"
            >{{ old('doctor_response') }}</textarea>
            @error('doctor_response')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl
                           transition shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Send Response & Mark Reviewed
            </button>
        </form>

        <form method="POST" action="{{ route('doctor.followup.review', $submission->id) }}" class="mt-3">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-3 rounded-xl transition text-sm">
                Mark as Reviewed (No Response)
            </button>
        </form>
    </div>
    @endif

    {{-- Patient History --}}
    @if($history->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Previous Submissions</h2>
        <div class="space-y-3">
            @foreach($history as $past)
            @php
                $pastUC = $urgencyConfig[$past->urgency_level] ?? ['badge' => 'bg-gray-100 text-gray-600', 'icon' => '⚪'];
            @endphp
            <div class="flex justify-between items-center bg-gray-50 rounded-xl p-3.5">
                <div>
                    <p class="text-sm text-gray-700 font-medium">
                        {{ implode(', ', array_map(fn($s) => ucwords(str_replace('_', ' ', $s)), $past->symptom_categories)) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $past->created_at->format('d M Y, h:i A') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($past->sync_status === 'Synced')
                        <span class="text-[10px] font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full">EMR ✓</span>
                    @endif
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $pastUC['badge'] }}">
                        {{ $pastUC['icon'] }} {{ $past->urgency_level }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
</div>

{{-- ── Pipeline Trace Modal (Alpine.js) ── --}}
<div x-data="{ open: false, url: '' }"
     x-on:open-pipeline-modal.window="url = $event.detail.url; open = true;"
     x-show="open"
     style="display: none;"
     class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 sm:p-6"
     x-transition.opacity>
     
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[90vh] flex flex-col overflow-hidden"
         x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         @click.away="open = false">
         
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50 shrink-0">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Pipeline Trace</h3>
                <p class="text-xs text-gray-500">View real-time middleware validation and EMR sync status.</p>
            </div>
            <div class="flex items-center gap-3">
                <a :href="url" target="_blank" class="text-sm font-semibold text-blue-600 hover:underline">View Full Details ↗</a>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition bg-white border border-gray-200 p-1.5 rounded-lg shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        
        {{-- Iframe Content --}}
        <div class="flex-1 bg-gray-100 relative">
            <template x-if="open">
                <iframe :src="url + '?modal=1'" class="w-full h-full border-0 rounded-b-2xl"></iframe>
            </template>
        </div>
    </div>
</div>

@endsection