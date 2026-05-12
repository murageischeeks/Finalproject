@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Follow-Up Triage Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pending patient follow-up reports sorted by urgency</p>
        </div>
        @if($highUrgencyCount > 0)
        <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 font-semibold px-4 py-2.5 rounded-xl text-sm">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $highUrgencyCount }} High Urgency {{ Str::plural('Report', $highUrgencyCount) }}
        </div>
        @endif
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('doctor.followup.index') }}"
          class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-wrap gap-4 items-end">

        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Urgency</label>
            <select name="urgency"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
                <option value="">All Levels</option>
                @foreach(['High' => '🔴 High', 'Medium' => '🟡 Medium', 'Low' => '🟢 Low'] as $val => $label)
                <option value="{{ $val }}" {{ request('urgency') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}"
                   class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}"
                   class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pipeline Status</label>
            <select name="sync_status"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
                <option value="">All Statuses</option>
                <option value="Synced" {{ request('sync_status') === 'Synced' ? 'selected' : '' }}>✓ Synced to EMR</option>
                <option value="Failed" {{ request('sync_status') === 'Failed' ? 'selected' : '' }}>⚠ Sync Failed</option>
                <option value="Pending" {{ request('sync_status') === 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
            </select>
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Symptom</label>
            <select name="symptom"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
                <option value="">All Symptoms</option>
                @foreach([
                    'fever'                  => '🌡️ Fever',
                    'pain'                   => '😣 Pain',
                    'swelling'               => '🫧 Swelling',
                    'medication_side_effect' => '💊 Medication Side Effect',
                    'wound_concern'          => '🩹 Wound Concern',
                    'general_deterioration'  => '📉 General Deterioration',
                ] as $value => $label)
                <option value="{{ $value }}" {{ request('symptom') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition shadow-md shadow-blue-200">
                Filter
            </button>
            <a href="{{ route('doctor.followup.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-5 py-2 rounded-xl transition">
                Reset
            </a>
        </div>
    </form>

    {{-- Submissions --}}
    @if($submissions->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-gray-700 font-semibold">All caught up!</p>
            <p class="text-sm text-gray-400 mt-1">No pending follow-up reports to review.</p>
        </div>
    @else
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Patient</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Symptoms</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Severity</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Recovery</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Urgency</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Submitted</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($submissions as $submission)
                @php
                    $urgencyConfig = [
                        'High'   => ['badge' => 'bg-red-100 text-red-700',    'bar' => 'border-l-4 border-red-400',    'icon' => '🔴'],
                        'Medium' => ['badge' => 'bg-yellow-100 text-yellow-700', 'bar' => 'border-l-4 border-yellow-400', 'icon' => '🟡'],
                        'Low'    => ['badge' => 'bg-green-100 text-green-700',  'bar' => '',                             'icon' => '🟢'],
                    ];
                    $uc = $urgencyConfig[$submission->urgency_level] ?? ['badge' => 'bg-gray-100 text-gray-600', 'bar' => '', 'icon' => '⚪'];
                @endphp
                <tr class="hover:bg-gray-50 transition {{ $uc['bar'] }}">
                    <td class="px-5 py-4">
                        <p class="font-semibold text-gray-800">{{ $submission->patient->name }}</p>
                        <p class="text-gray-400 text-xs mt-0.5 mb-1">{{ $submission->patient->email }}</p>
                        {{-- Pipeline Status Indicator --}}
                        @if($submission->sync_status === 'Synced')
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                                Pipeline: ✓ All stages passed | EMR Synced ✓
                            </span>
                        @elseif($submission->sync_status === 'Failed')
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full">
                                Pipeline: ⚠ Sync Failed
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-yellow-700 bg-yellow-100 px-2 py-0.5 rounded-full">
                                Pipeline: ⏳ {{ $submission->sync_status }}
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-600 max-w-xs">
                        <div class="flex flex-wrap gap-1">
                            @foreach($submission->symptom_categories as $symptom)
                            <span class="bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-full">
                                {{ ucwords(str_replace('_', ' ', $symptom)) }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <div class="w-2 h-2 rounded-full {{ $i <= $submission->severity ? 'bg-orange-400' : 'bg-gray-200' }}"></div>
                            @endfor
                            <span class="text-xs text-gray-500 ml-1">{{ $submission->severity }}/5</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-600 text-sm">
                        {{ $submission->recovery_status }}
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $uc['badge'] }}">
                            {{ $uc['icon'] }} {{ $submission->urgency_level }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-gray-400 text-xs">
                        <p>{{ $submission->created_at->format('d M Y') }}</p>
                        <p>{{ $submission->created_at->format('h:i A') }}</p>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('doctor.followup.show', $submission->id) }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition">
                                Review
                            </a>
                            <button x-data @click="$dispatch('open-pipeline-modal', { url: '{{ route('middleware.trace', $submission->id) }}' })"
                                    class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3.5 py-2 rounded-lg transition shadow-sm">
                                View Pipeline
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>{{ $submissions->links() }}</div>
    @endif

</div>
</div>

<script>
    setInterval(() => {
        fetch('{{ route('doctor.followup.refresh') }}')
            .then(r => r.json())
            .then(data => {
                if (data.high_urgency_count > 0) location.reload();
            });
    }, 60000);
</script>

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