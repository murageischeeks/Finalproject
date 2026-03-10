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
                        <p class="text-gray-400 text-xs mt-0.5">{{ $submission->patient->email }}</p>
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
                        <a href="{{ route('doctor.followup.show', $submission->id) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition">
                            View Details
                        </a>
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

@endsection