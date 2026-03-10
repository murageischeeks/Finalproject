@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Follow-Up Reports</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track your recovery progress and doctor responses</p>
        </div>
        <a href="{{ route('patient.followup.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5
                  rounded-xl transition shadow-md shadow-blue-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Report
        </a>
    </div>

    @if($submissions->isEmpty())
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-gray-700 font-medium">No follow-up reports yet</p>
            <p class="text-sm text-gray-400 mt-1">Submit your first report after a consultation</p>
            <a href="{{ route('patient.followup.create') }}"
               class="inline-block mt-5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                      px-5 py-2.5 rounded-xl transition">
                Submit First Report
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($submissions as $submission)
            @php
                $urgencyConfig = [
                    'High'   => ['badge' => 'bg-red-100 text-red-700 border-red-200',    'bar' => 'bg-red-500',    'icon' => '🔴'],
                    'Medium' => ['badge' => 'bg-yellow-100 text-yellow-700 border-yellow-200', 'bar' => 'bg-yellow-500', 'icon' => '🟡'],
                    'Low'    => ['badge' => 'bg-green-100 text-green-700 border-green-200',  'bar' => 'bg-green-500',  'icon' => '🟢'],
                ];
                $uc = $urgencyConfig[$submission->urgency_level] ?? ['badge' => 'bg-gray-100 text-gray-600 border-gray-200', 'bar' => 'bg-gray-400', 'icon' => '⚪'];
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Urgency color bar --}}
                <div class="h-1 {{ $uc['bar'] }}"></div>

                <div class="p-5">
                    {{-- Top row --}}
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">{{ $submission->created_at->format('d M Y, h:i A') }}</p>
                            <p class="text-gray-800 font-semibold text-sm">
                                {{ implode(', ', array_map(fn($s) => ucwords(str_replace('_', ' ', $s)), $submission->symptom_categories)) }}
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold border {{ $uc['badge'] }} shrink-0 ml-3">
                            {{ $uc['icon'] }} {{ $submission->urgency_level ?? 'Pending' }}
                        </span>
                    </div>

                    {{-- Stats row --}}
                    <div class="flex gap-4 text-xs text-gray-500 mb-4">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                            Severity <strong class="text-gray-700 ml-1">{{ $submission->severity }}/5</strong>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                            <strong class="text-gray-700">{{ $submission->recovery_status }}</strong>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $submission->sync_status === 'Synced' ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                            {{ $submission->sync_status }}
                        </div>
                    </div>

                    {{-- Doctor Response --}}
                    @if($submission->doctor_response)
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-3.5">
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-xs font-semibold text-blue-600">Doctor's Response</p>
                        </div>
                        <p class="text-sm text-blue-800">{{ $submission->doctor_response }}</p>
                    </div>
                    @endif

                    {{-- Review status --}}
                    <div class="mt-3 flex items-center justify-between">
                        @if($submission->reviewed_at)
                            <span class="text-xs text-green-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Reviewed {{ $submission->reviewed_at->format('d M Y') }}
                            </span>
                        @else
                            <span class="text-xs text-yellow-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Awaiting review
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
</div>
@endsection