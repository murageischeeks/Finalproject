@extends('layouts.app')

@section('title', 'Clinical Dashboard')

@section('content')

{{-- ── PAGE TITLE ── --}}
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Clinical Dashboard</h1>
        <p class="text-sm text-surface-500 mt-0.5">{{ now()->format('l, d F Y') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('doctor.followup.index') }}" class="btn-primary btn-sm relative">
            Follow-Up Triage
            @if(isset($highUrgencyCount) && $highUrgencyCount > 0)
                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-urgent-600 text-white text-[10px] font-bold flex items-center justify-center">
                    {{ $highUrgencyCount }}
                </span>
            @endif
        </a>
    </div>
</div>

{{-- ── LICENSE ALERT ── --}}
@if(!($profile->license_verified ?? false))
    <div class="alert alert-warning mb-6">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <p class="font-semibold">License Verification Pending</p>
            <p class="text-sm mt-0.5">Your credentials are under admin review. Full access will be granted upon verification.</p>
        </div>
    </div>
@endif

{{-- ── PIPELINE NOTIFICATION ── --}}
@if(isset($failedSyncCount) && $failedSyncCount > 0)
    <div class="alert bg-amber-50 border border-amber-200 text-amber-800 mb-6 flex items-center justify-between p-4 rounded-xl shadow-sm">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="font-bold text-sm">⚠ {{ $failedSyncCount }} submissions pending EMR sync</p>
                <p class="text-xs mt-0.5 text-amber-700">Pipeline failures detected. These follow-ups could not be synchronized with the EMR.</p>
            </div>
        </div>
        <a href="{{ route('doctor.followup.index', ['sync_status' => 'Failed']) }}" class="btn bg-amber-600 hover:bg-amber-700 text-white btn-sm shadow-md">
            View Details
        </a>
    </div>
@endif

{{-- ── PROFILE CARD ── --}}
<div class="card mb-6">
    <div class="bg-gradient-to-r from-brand-700 to-brand-600 p-6 text-white rounded-t-xl">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                {{-- Avatar --}}
                <div class="w-16 h-16 rounded-xl bg-white/20 border-2 border-white/30 flex items-center justify-center text-2xl font-extrabold text-white shadow">
                    {{ strtoupper(substr($profile->name ?? 'D', 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ $profile->name ?? '—' }}</h2>
                    <p class="text-brand-200 text-sm">{{ $profile->email ?? '—' }}</p>
                    <div class="flex flex-wrap gap-3 mt-2 text-sm text-brand-100">
                        <span><strong class="text-white">Specialization:</strong> {{ $profile->specialization ?? '—' }}</span>
                        <span><strong class="text-white">Department:</strong> {{ $profile->department ?? '—' }}</span>
                        <span><strong class="text-white">License #:</strong> {{ $profile->license_number ?? '—' }}</span>
                        <span>
                            <strong class="text-white">Status:</strong>
                            @if($profile->license_verified ?? false)
                                <span class="text-green-300">● Verified</span>
                            @else
                                <span class="text-yellow-300">● Pending</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            {{-- Quick Actions --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('doctor.profile.edit') }}" class="btn bg-white/20 border border-white/30 text-white hover:bg-white/30 btn-sm backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Profile
                </a>
                <a href="{{ route('doctor.lab_results.index') }}" class="btn bg-teal-500 border-transparent text-white hover:bg-teal-600 btn-sm">
                    Lab Results
                </a>
                <a href="{{ route('doctor.prescriptions.index') }}" class="btn bg-purple-500 border-transparent text-white hover:bg-purple-600 btn-sm">
                    Prescriptions
                </a>
            </div>
        </div>
    </div>
</div>

@if($profile->license_verified ?? false)

{{-- ── STATS GRID ── --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
    @php
        $statsData = [
            ['key'=>'today_appointments', 'label'=>'Today',        'icon'=>'calendar', 'color'=>'text-brand-600 bg-brand-50 border-brand-100'],
            ['key'=>'upcoming',           'label'=>'Upcoming',     'icon'=>'clock',    'color'=>'text-teal-600 bg-teal-50 border-teal-100'],
            ['key'=>'pending',            'label'=>'Pending',      'icon'=>'hourglass','color'=>'text-amber-600 bg-amber-50 border-amber-100'],
            ['key'=>'completed',          'label'=>'Completed',    'icon'=>'check',    'color'=>'text-medical-600 bg-medical-50 border-medical-100'],
        ];
    @endphp
    @foreach($statsData as $s)
    <div class="bg-white border {{ explode(' ', $s['color'])[2] }} rounded-xl p-4 shadow-card hover:shadow-card-md transition-shadow">
        <p class="text-xs font-semibold uppercase tracking-wide text-surface-400 mb-1">{{ $s['label'] }}</p>
        <p class="text-3xl font-extrabold {{ explode(' ', $s['color'])[0] }}" data-count="{{ $stats[$s['key']] ?? 0 }}">
            {{ $stats[$s['key']] ?? 0 }}
        </p>
    </div>
    @endforeach
    {{-- High Urgency --}}
    <div class="bg-white border border-urgent-100 rounded-xl p-4 shadow-card hover:shadow-card-md transition-shadow">
        <p class="text-xs font-semibold uppercase tracking-wide text-surface-400 mb-1">High Urgency</p>
        <p class="text-3xl font-extrabold text-urgent-600" data-count="{{ $highUrgencyCount ?? 0 }}">
            {{ $highUrgencyCount ?? 0 }}
        </p>
    </div>
</div>

{{-- ── MAIN GRID: Follow-Ups + Queue ── --}}
<div class="grid lg:grid-cols-3 gap-6 mb-6">

    {{-- ── Follow-Up Reports (2/3 width) ── --}}
    <div class="lg:col-span-2">
        <div class="card h-full">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Pending Follow-Up Reports</h2>
                    @if(isset($highUrgencyCount) && $highUrgencyCount > 0)
                        <p class="text-xs text-urgent-600 font-semibold mt-0.5">{{ $highUrgencyCount }} high-urgency case(s) require attention</p>
                    @endif
                </div>
                <a href="{{ route('doctor.followup.index') }}" class="btn-ghost btn-sm">View All</a>
            </div>
            <div class="divide-y divide-surface-100">
                @if(isset($recentFollowUps) && $recentFollowUps->isNotEmpty())
                    @foreach($recentFollowUps as $sub)
                    @php
                        $urgencyBadge = ['High'=>'badge-red','Medium'=>'badge-yellow','Low'=>'badge-green'][$sub->urgency_level] ?? 'badge-gray';
                        $isHigh = $sub->urgency_level === 'High';
                    @endphp
                    <div class="relative flex items-center justify-between px-6 py-4 hover:bg-surface-50 transition">
                        @if($isHigh)<div class="priority-high"></div>@endif
                        <div class="flex-1 min-w-0 {{ $isHigh ? 'pl-3' : '' }}">
                            <p class="font-semibold text-sm text-surface-800 truncate">{{ $sub->patient->name }}</p>
                            <p class="text-xs text-surface-500 mt-0.5 truncate">
                                {{ implode(', ', array_map(fn($s) => ucwords(str_replace('_', ' ', $s)), $sub->symptom_categories)) }}
                            </p>
                            <p class="text-xs text-surface-400 mt-0.5">{{ $sub->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2 ml-4 flex-shrink-0">
                            <span class="{{ $urgencyBadge }}">{{ $sub->urgency_level }}</span>
                            <a href="{{ route('doctor.followup.show', $sub->id) }}" class="text-xs text-brand-600 font-semibold hover:underline">
                                Review →
                            </a>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="px-6 py-10 text-center">
                        <svg class="w-10 h-10 text-surface-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm text-surface-400 font-medium">No pending follow-up reports.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Live Queue (1/3 width) ── --}}
    <div>
        <div class="card h-full">
            <div class="card-header">
                <h2 class="card-title">Live Queue</h2>
                <span class="badge-green flex items-center gap-1 text-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-medical-500 animate-pulse-slow"></span>
                    Today
                </span>
            </div>
            <div class="divide-y divide-surface-100">
                @if(isset($queue) && $queue->isNotEmpty())
                    @foreach($queue as $appt)
                    <div class="flex items-center justify-between px-4 py-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-surface-800">
                                <span class="text-brand-600 mr-1">#{{ $appt->ticket_number ?? '—' }}</span>
                                {{ $appt->patient->name ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-surface-400">
                                {{ isset($appt->appointment_date) ? \Carbon\Carbon::parse($appt->appointment_date)->format('H:i') : '—' }}
                            </p>
                        </div>
                        <span class="status-{{ $appt->status ?? 'pending' }}">{{ ucfirst(str_replace('_',' ',$appt->status ?? 'pending')) }}</span>
                    </div>
                    @endforeach
                @else
                    <div class="px-4 py-10 text-center">
                        <p class="text-sm text-surface-400">No patients in queue today.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── APPOINTMENTS TABLE ── --}}
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Appointments</h2>
            @if(!empty($filterDate))
                <p class="text-xs text-surface-400 mt-0.5">Filtered for {{ \Carbon\Carbon::parse($filterDate)->format('d F Y') }}</p>
            @endif
        </div>
        <form method="GET" action="{{ route('doctor.dashboard') }}" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $filterDate ?? '' }}"
                   class="border border-surface-200 rounded-lg px-3 py-1.5 text-sm text-surface-700 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20">
            <button type="submit" class="btn-primary btn-sm">Filter</button>
            @if(!empty($filterDate))
                <a href="{{ route('doctor.dashboard') }}" class="btn-secondary btn-sm">Clear</a>
            @endif
        </form>
    </div>

    @if(isset($appointments) && $appointments->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Patient</th>
                        <th>Scheduled</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appt)
                    <tr>
                        <td class="font-semibold text-brand-600">#{{ $appt->ticket_number ?? '—' }}</td>
                        <td class="font-medium text-surface-800">{{ $appt->patient->name ?? 'N/A' }}</td>
                        <td>{{ isset($appt->appointment_date) ? \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y H:i') : '—' }}</td>
                        <td>
                            <span class="status-{{ $appt->status ?? 'pending' }}">
                                {{ ucfirst(str_replace('_',' ',$appt->status ?? '—')) }}
                            </span>
                        </td>
                        <td>
                            @if(!empty($appt->notes))
                                <button x-data @click="$dispatch('open-modal', { content: '{{ addslashes($appt->notes) }}' })"
                                        class="btn-secondary btn-sm">Note</button>
                            @else
                                <span class="text-surface-300">—</span>
                            @endif
                        </td>
                        <td>
                            @if(($appt->status ?? '') !== 'cancelled')
                                <form method="POST" action="{{ route('doctor.appointments.updateStatus', $appt->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="border border-surface-200 rounded-md text-sm py-1 px-2 focus:ring-brand-600 focus:border-brand-600 text-surface-700">
                                        <option value="">Change status…</option>
                                        <option value="pending"     {{ ($appt->status ?? '') == 'pending'     ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ ($appt->status ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed"   {{ ($appt->status ?? '') == 'completed'   ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </form>
                            @else
                                <span class="badge-red">Cancelled</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="px-6 py-10 text-center">
            <svg class="w-10 h-10 text-surface-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm text-surface-400 font-medium">No appointments found for this date.</p>
        </div>
    @endif
</div>

@endif {{-- end license_verified --}}

{{-- ── Patient Note Modal ── --}}
<div x-data="{ show: false, content: '' }"
     x-on:open-modal.window="show = true; content = $event.detail.content"
     x-show="show"
     class="fixed inset-0 bg-surface-900/60 flex items-center justify-center z-50 px-4"
     style="display: none;">
    <div @click.away="show = false"
         class="bg-white rounded-2xl shadow-card-lg max-w-md w-full p-6 animate-fade-in-up">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-surface-800">Patient Note</h3>
            <button @click="show = false" class="w-8 h-8 rounded-lg bg-surface-100 hover:bg-surface-200 text-surface-500 flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <p x-text="content" class="text-surface-600 text-sm leading-relaxed"></p>
        <button @click="show = false" class="btn-primary btn-sm mt-5 w-full">Close</button>
    </div>
</div>

@endsection