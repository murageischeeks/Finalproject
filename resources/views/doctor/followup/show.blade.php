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
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $pastUC['badge'] }}">
                    {{ $pastUC['icon'] }} {{ $past->urgency_level }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
</div>
@endsection