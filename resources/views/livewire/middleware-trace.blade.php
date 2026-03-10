@extends('layouts.app')

@section('content')
<div class="py-10 px-4">
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ url()->previous() }}"
           class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Middleware Pipeline Trace</h1>
            <p class="text-sm text-gray-500 mt-1">
                Follow-Up Submission #{{ $submission->id }} —
                {{ $submission->patient->name }} —
                {{ $submission->created_at->format('d M Y, h:i A') }}
            </p>
        </div>
    </div>

    {{-- Submission Summary --}}
    @php
        $urgencyColors = [
            'High'   => 'bg-red-100 text-red-700 border-red-200',
            'Medium' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'Low'    => 'bg-green-100 text-green-700 border-green-200',
        ];
        $uc = $urgencyColors[$submission->urgency_level] ?? 'bg-gray-100 text-gray-700 border-gray-200';
    @endphp

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Patient</p>
            <p class="text-sm font-bold text-gray-800">{{ $submission->patient->name }}</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Symptoms</p>
            <p class="text-sm font-bold text-gray-800">
                {{ implode(', ', array_map(fn($s) => ucwords(str_replace('_', ' ', $s)), $submission->symptom_categories)) }}
            </p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Severity / Recovery</p>
            <p class="text-sm font-bold text-gray-800">{{ $submission->severity }}/5 · {{ $submission->recovery_status }}</p>
        </div>
        <div class="rounded-xl p-4 border {{ $uc }}">
            <p class="text-xs mb-1 font-semibold uppercase tracking-wide">Urgency</p>
            <p class="text-sm font-bold">{{ $submission->urgency_level }}</p>
        </div>
    </div>

    {{-- Pipeline Trace --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-6">Pipeline Execution Trace</h2>

        @php
            $stageConfig = [
                'submission_created'                 => ['label' => 'Form Submitted & Saved to Database',             'stage' => '1', 'color' => 'blue'],
                'middleware_validation_passed'        => ['label' => 'Business Validation — Passed',                   'stage' => '2', 'color' => 'green'],
                'middleware_validation_failed'        => ['label' => 'Business Validation — Failed',                   'stage' => '2', 'color' => 'red'],
                'middleware_transformation_complete'  => ['label' => 'Data Transformation — OpenMRS Payload Built',    'stage' => '3', 'color' => 'purple'],
                'emr_sync_initiated'                  => ['label' => 'EMR Sync — Sending Payload to OpenMRS Receiver', 'stage' => '4', 'color' => 'yellow'],
                'emr_sync_success'                    => ['label' => 'EMR Sync — Successfully Stored in EMR Database', 'stage' => '4', 'color' => 'green'],
                'emr_sync_skipped'                    => ['label' => 'EMR Sync — Payload Ready (OpenMRS Pending)',     'stage' => '4', 'color' => 'yellow'],
                'emr_sync_failed'                     => ['label' => 'EMR Sync — Failed',                             'stage' => '4', 'color' => 'red'],
                'emr_sync_permanently_failed'         => ['label' => 'EMR Sync — Permanently Failed (3 Attempts)',    'stage' => '4', 'color' => 'red'],
                'high_urgency_notification_sent'      => ['label' => 'High Urgency Alert — Email Sent to Doctor',     'stage' => '★', 'color' => 'orange'],
                'middleware_pipeline_failed'          => ['label' => 'Pipeline — Unexpected Error',                   'stage' => '!', 'color' => 'red'],
            ];

            $colorMap = [
                'blue'   => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'border' => 'border-blue-200',   'badge' => 'bg-blue-600'],
                'green'  => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'border' => 'border-green-200',  'badge' => 'bg-green-600'],
                'red'    => ['bg' => 'bg-red-100',     'text' => 'text-red-700',    'border' => 'border-red-200',    'badge' => 'bg-red-600'],
                'purple' => ['bg' => 'bg-purple-100',  'text' => 'text-purple-700', 'border' => 'border-purple-200', 'badge' => 'bg-purple-600'],
                'yellow' => ['bg' => 'bg-yellow-100',  'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'badge' => 'bg-yellow-500'],
                'orange' => ['bg' => 'bg-orange-100',  'text' => 'text-orange-700', 'border' => 'border-orange-200', 'badge' => 'bg-orange-500'],
                'gray'   => ['bg' => 'bg-gray-100',    'text' => 'text-gray-700',   'border' => 'border-gray-200',   'badge' => 'bg-gray-500'],
            ];
        @endphp

        <div class="relative">
            {{-- Vertical line --}}
            <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-100"></div>

            <div class="space-y-4">
                @forelse($trace as $index => $entry)
                @php
                    $config = $stageConfig[$entry['action']] ?? ['label' => $entry['action'], 'stage' => '?', 'color' => 'gray'];
                    $colors = $colorMap[$config['color']];
                    $meta   = is_array($entry['meta']) ? $entry['meta'] : json_decode($entry['meta'], true);
                @endphp

                <div class="relative flex gap-4 pl-14">
                    {{-- Stage badge --}}
                    <div class="absolute left-0 w-10 h-10 rounded-full {{ $colors['badge'] }} text-white
                                flex items-center justify-center text-xs font-bold shrink-0 z-10">
                        {{ $config['stage'] }}
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 {{ $colors['bg'] }} {{ $colors['border'] }} border rounded-2xl p-4">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm font-semibold {{ $colors['text'] }}">{{ $config['label'] }}</p>
                            <div class="flex items-center gap-2 shrink-0 ml-4">
                                <span class="text-xs {{ $colors['text'] }} opacity-70">
                                    {{ \Carbon\Carbon::parse($entry['created_at'])->format('h:i:s A') }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                                    {{ $entry['outcome'] === 'success' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                    {{ $entry['outcome'] }}
                                </span>
                            </div>
                        </div>

                        {{-- Raw action name --}}
                        <p class="text-xs font-mono {{ $colors['text'] }} opacity-60 mb-3">{{ $entry['action'] }}</p>

                        {{-- Meta data --}}
                        @if(!empty($meta))
                        <div class="space-y-2">
                            @foreach($meta as $key => $value)
                            <div class="bg-white bg-opacity-60 rounded-xl p-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                    {{ str_replace('_', ' ', $key) }}
                                </p>
                                @if(is_array($value))
                                    <pre class="text-xs text-gray-700 whitespace-pre-wrap font-mono overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                @else
                                    <p class="text-xs text-gray-700 font-mono">{{ $value }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="pl-14 text-sm text-gray-400">No pipeline trace found for this submission.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Sync Status --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">EMR Sync Status</h2>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Current Sync Status</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                    {{ $submission->sync_status === 'Synced'  ? 'bg-green-100 text-green-700' :
                      ($submission->sync_status === 'Failed'  ? 'bg-red-100 text-red-700'     :
                                                                'bg-yellow-100 text-yellow-700') }}">
                    {{ $submission->sync_status }}
                </span>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">OpenMRS Observation UUID</p>
                <p class="text-sm font-mono text-gray-700">
                    {{ $submission->openmrs_observation_uuid ?? 'Not yet synced' }}
                </p>
            </div>
        </div>

        {{-- EMR Observation Link --}}
        @if($submission->openmrs_observation_uuid)
        <div class="mt-4 bg-green-50 border border-green-100 rounded-xl p-4">
            <p class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-2">
                EMR Observation Record
            </p>
            <a href="{{ url('/api/emr/observations/' . $submission->openmrs_observation_uuid) }}"
               target="_blank"
               class="text-sm font-mono text-green-700 hover:underline break-all">
                {{ url('/api/emr/observations/' . $submission->openmrs_observation_uuid) }}
            </a>
            <p class="text-xs text-green-600 mt-1">
                Click to view the full FHIR observation record stored in the EMR database
            </p>
        </div>
        @endif

    </div>

</div>
</div>
@endsection