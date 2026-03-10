@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
<div class="max-w-lg mx-auto">

    {{-- Success Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center mb-5">
        <div class="flex justify-center mb-5">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Report Submitted!</h1>
        <p class="text-gray-500 text-sm">Your follow-up report has been received and your care team has been notified.</p>
    </div>

    {{-- Summary Card --}}
    @php
        $urgencyConfig = [
            'High'   => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'icon' => '🔴'],
            'Medium' => ['bg' => 'bg-yellow-100',  'text' => 'text-yellow-700', 'icon' => '🟡'],
            'Low'    => ['bg' => 'bg-green-100',   'text' => 'text-green-700',  'icon' => '🟢'],
        ];
        $uc = $urgencyConfig[$submission->urgency_level] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => '⚪'];
    @endphp

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5 space-y-4">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Report Summary</h2>

        <div class="flex justify-between items-center py-3 border-b border-gray-50">
            <span class="text-sm text-gray-500">Urgency Level</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold {{ $uc['bg'] }} {{ $uc['text'] }}">
                {{ $uc['icon'] }} {{ $submission->urgency_level }}
            </span>
        </div>

        <div class="flex justify-between items-center py-3 border-b border-gray-50">
            <span class="text-sm text-gray-500">Recovery Status</span>
            <span class="text-sm font-medium text-gray-800">{{ $submission->recovery_status }}</span>
        </div>

        <div class="flex justify-between items-center py-3 border-b border-gray-50">
            <span class="text-sm text-gray-500">Severity</span>
            <span class="text-sm font-medium text-gray-800">{{ $submission->severity }} / 5</span>
        </div>

        <div class="flex justify-between items-center py-3">
            <span class="text-sm text-gray-500">Submitted</span>
            <span class="text-sm font-medium text-gray-800">{{ $submission->created_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>

    {{-- What happens next --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-5">
        <h3 class="text-sm font-semibold text-blue-800 mb-3">What happens next?</h3>
        <ul class="space-y-2 text-sm text-blue-700">
            <li class="flex items-start gap-2">
                <span class="mt-0.5">✅</span>
                <span>Your doctor has been notified of your report</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="mt-0.5">🔍</span>
                <span>They will review and triage based on urgency</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="mt-0.5">💬</span>
                <span>A response will appear in your report history</span>
            </li>
        </ul>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col gap-3">
        <a href="{{ route('patient.followup.index') }}"
           class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-2xl
                  transition text-center shadow-lg shadow-blue-200">
            View My Report History
        </a>
        <a href="{{ route('patient.dashboard') }}"
           class="w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3.5 rounded-2xl
                  transition text-center border border-gray-200">
            Back to Dashboard
        </a>
    </div>

</div>
</div>
@endsection