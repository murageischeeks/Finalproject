@extends('layouts.app')

@section('title', 'Patient Dashboard')

@section('content')

{{-- ── PAGE TITLE ── --}}
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-surface-900">Patient Dashboard</h1>
        <p class="text-sm text-surface-500 mt-0.5">Welcome back, {{ auth()->user()->name }}</p>
    </div>
    <a href="{{ route('patient.followup.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Follow-Up
    </a>
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success mb-6 animate-fade-in" data-auto-dismiss="5000">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- ── TOP ROW: Profile + Quick Access ── --}}
<div class="grid lg:grid-cols-3 gap-6 mb-6">

    {{-- Profile Card --}}
    <div class="card lg:col-span-1">
        <div class="bg-gradient-to-br from-teal-600 to-teal-700 p-6 rounded-t-xl text-white">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-14 h-14 rounded-xl bg-white/20 border-2 border-white/30 flex items-center justify-center text-2xl font-extrabold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-bold text-lg leading-tight">{{ auth()->user()->name }}</h2>
                    <p class="text-teal-200 text-sm">{{ auth()->user()->email }}</p>
                </div>
            </div>
            @if(auth()->user()->phone)
                <p class="text-sm text-teal-100"><strong class="text-white">Phone:</strong> {{ auth()->user()->phone }}</p>
            @endif
            @if(auth()->user()->address)
                <p class="text-sm text-teal-100 mt-0.5"><strong class="text-white">Address:</strong> {{ auth()->user()->address }}</p>
            @endif
        </div>
        <div class="p-4 border-t border-surface-100">
            <a href="{{ route('patient.profile') }}" class="btn-secondary w-full text-center btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Profile
            </a>
        </div>
    </div>

    {{-- Quick Access Cards --}}
    <div class="lg:col-span-2 grid sm:grid-cols-3 gap-4">
        <a href="{{ route('patient.labResults.index') }}"
           class="card flex flex-col items-start p-5 hover:shadow-card-md transition-shadow group no-underline">
            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mb-3 group-hover:bg-purple-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-semibold text-surface-800">Lab Results</h3>
            <p class="text-xs text-surface-500 mt-1 leading-relaxed">View your test results and history</p>
        </a>

        <a href="{{ route('patient.prescriptions.index') }}"
           class="card flex flex-col items-start p-5 hover:shadow-card-md transition-shadow group no-underline">
            <div class="w-10 h-10 rounded-lg bg-medical-50 text-medical-600 flex items-center justify-center mb-3 group-hover:bg-medical-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <h3 class="font-semibold text-surface-800">Prescriptions</h3>
            <p class="text-xs text-surface-500 mt-1 leading-relaxed">View your active medications</p>
        </a>

        <a href="{{ route('patient.followup.create') }}"
           class="card flex flex-col items-start p-5 hover:shadow-card-md transition-shadow group no-underline">
            <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center mb-3 group-hover:bg-brand-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-semibold text-surface-800">Submit Follow-Up</h3>
            <p class="text-xs text-surface-500 mt-1 leading-relaxed">Post-consultation follow-up form</p>
        </a>
    </div>
</div>

{{-- ── RECENT FOLLOW-UPS ── --}}
@php $recentSubmissions = auth()->user()->followUpSubmissions()->latest()->take(3)->get(); @endphp
<div class="card mb-6">
    <div class="card-header">
        <h2 class="card-title">Recent Follow-Up Reports</h2>
        <a href="{{ route('patient.followup.index') }}" class="btn-ghost btn-sm">View All</a>
    </div>
    @if($recentSubmissions->isNotEmpty())
        <div class="divide-y divide-surface-100">
            @foreach($recentSubmissions as $sub)
            @php
                $color = ['High'=>'badge-red','Medium'=>'badge-yellow','Low'=>'badge-green'][$sub->urgency_level] ?? 'badge-gray';
            @endphp
            <div class="flex items-center justify-between px-6 py-4 hover:bg-surface-50 transition">
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm text-surface-800 truncate">
                        {{ implode(', ', array_map(fn($s) => ucwords(str_replace('_', ' ', $s)), $sub->symptom_categories)) }}
                    </p>
                    <p class="text-xs text-surface-400 mt-0.5">{{ $sub->created_at->format('d M Y, h:i A') }}</p>
                    @if($sub->doctor_response)
                        <p class="text-xs text-medical-600 font-medium mt-0.5">✓ Doctor has responded</p>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-1.5 ml-4 flex-shrink-0">
                    <span class="{{ $color }}">{{ $sub->urgency_level ?? 'Pending' }}</span>
                    <span class="text-xs {{ $sub->reviewed_at ? 'text-medical-600' : 'text-amber-600' }}">
                        {{ $sub->reviewed_at ? 'Reviewed' : 'Pending Review' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 rounded-b-xl">
            <a href="{{ route('patient.followup.create') }}" class="btn-primary btn-sm w-full text-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Submit New Follow-Up Report
            </a>
        </div>
    @else
        <div class="px-6 py-10 text-center">
            <svg class="w-10 h-10 text-surface-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-sm text-surface-400 font-medium mb-3">No follow-up reports yet.</p>
            <a href="{{ route('patient.followup.create') }}" class="btn-primary btn-sm">Submit Your First Report</a>
        </div>
    @endif
</div>

{{-- ── FIND A DOCTOR ── --}}
<div class="card mb-6">
    <div class="card-header">
        <h2 class="card-title">Find a Doctor & Book Appointment</h2>
    </div>
    <div class="p-6 border-b border-surface-100">
        <form method="GET" action="{{ route('patient.dashboard') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name or specialization…"
                   class="flex-1">
            <select name="specialization" class="sm:w-48">
                <option value="">All Specializations</option>
                @foreach($specializations as $spec)
                    <option value="{{ $spec }}" {{ request('specialization') == $spec ? 'selected' : '' }}>{{ $spec }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Search</button>
        </form>
    </div>

    @if($doctors->isEmpty())
        <div class="px-6 py-10 text-center">
            <p class="text-sm text-surface-400">No doctors match your search criteria.</p>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
            @foreach($doctors as $doc)
            <div class="border border-surface-200 rounded-xl overflow-hidden hover:shadow-card-md transition-shadow">
                <div class="bg-surface-50 border-b border-surface-200 px-4 py-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-700 text-white font-bold flex items-center justify-center text-sm">
                        {{ strtoupper(substr($doc->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-surface-800">{{ $doc->name }}</p>
                        <p class="text-xs text-surface-500">{{ $doc->specialization ?? 'General' }}</p>
                    </div>
                    <a href="{{ route('patient.showDoctor', $doc->id) }}"
                       class="ml-auto text-xs text-brand-600 font-semibold hover:underline">View</a>
                </div>
                <form action="{{ route('patient.appointments.store') }}" method="POST" class="p-4 space-y-2">
                    @csrf
                    <input type="hidden" name="doctor_id" value="{{ $doc->id }}">
                    <div class="input-group">
                        <label class="text-xs">Appointment Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" required>
                    </div>
                    <div class="input-group">
                        <label class="text-xs">Reason for Visit</label>
                        <textarea name="notes" rows="2" placeholder="Briefly describe your concern…"></textarea>
                    </div>
                    <button type="submit" class="btn-success btn-sm w-full">Book Appointment</button>
                </form>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ── MY APPOINTMENTS ── --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">My Appointments & Tickets</h2>
    </div>
    @if($appointments->isEmpty())
        <div class="px-6 py-10 text-center">
            <p class="text-sm text-surface-400">No upcoming appointments.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Scheduled</th>
                        <th>Ticket</th>
                        <th>Queue #</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments->sortBy('scheduled_at') as $appt)
                    <tr>
                        <td class="font-medium text-surface-800">{{ $appt->doctor->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($appt->scheduled_at)->format('d M Y, H:i') }}</td>
                        <td class="font-semibold text-brand-600">#{{ $appt->ticket_number }}</td>
                        <td class="text-surface-500">{{ $positions[$appt->id] ?? '—' }}</td>
                        <td>
                            <span class="status-{{ $appt->status }}">{{ ucfirst($appt->status) }}</span>
                        </td>
                        <td class="text-surface-500 text-xs max-w-xs truncate">{{ $appt->notes ?? '—' }}</td>
                        <td class="w-64">
                            <div class="flex flex-col gap-2">
                                @if($appt->status !== 'cancelled')
                                <form action="{{ route('patient.appointments.cancel', $appt->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="w-full text-[11px] bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded border border-red-200 font-semibold transition">Cancel Appointment</button>
                                </form>
                                <form action="{{ route('patient.appointments.reschedule', $appt->id) }}" method="POST"
                                      class="flex flex-col gap-1.5 p-2 bg-surface-50 border border-surface-200 rounded-lg">
                                    @csrf @method('PATCH')
                                    <p class="text-[10px] font-semibold text-surface-500 uppercase tracking-widest">Reschedule Time</p>
                                    <input type="datetime-local" name="scheduled_at" required
                                           class="w-full border border-surface-300 rounded text-[11px] px-2 py-1 focus:border-brand-600 focus:ring-1 focus:ring-brand-600">
                                    <button class="w-full text-[11px] bg-brand-600 text-white hover:bg-brand-700 px-3 py-1.5 rounded font-semibold transition">Confirm New Time</button>
                                </form>
                                @else
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded text-[11px] font-semibold bg-red-100 text-red-700">Cancelled</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection