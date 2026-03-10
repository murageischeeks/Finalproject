@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-6">

    <h1 class="text-2xl font-bold mb-4">Patient Dashboard</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">{{ session('success') }}</div>
    @endif

    <!-- MY PROFILE -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-2">My Profile</h2>
        <div class="flex justify-between items-center">
            <div>
                <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                <p><strong>Phone:</strong> {{ auth()->user()->phone ?? '-' }}</p>
                <p><strong>Address:</strong> {{ auth()->user()->address ?? '-' }}</p>
            </div>
            <div>
                <a href="{{ route('patient.profile') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Edit Profile</a>
            </div>
        </div>
    </div>

    <!-- QUICK ACCESS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('patient.labResults.index') }}"
           class="bg-purple-600 hover:bg-purple-700 text-white p-6 rounded-lg shadow text-center">
            <h2 class="text-lg font-bold">📊 Lab Results</h2>
            <p class="text-sm mt-2">View your recent lab results and history</p>
        </a>

        <a href="{{ route('patient.prescriptions.index') }}"
           class="bg-green-600 hover:bg-green-700 text-white p-6 rounded-lg shadow text-center">
            <h2 class="text-lg font-bold">💊 Prescriptions</h2>
            <p class="text-sm mt-2">View and download your prescriptions</p>
        </a>

        {{-- ── Follow-Up Module ── --}}
        <a href="{{ route('patient.followup.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-lg shadow text-center">
            <h2 class="text-lg font-bold">📋 Follow-Up Report</h2>
            <p class="text-sm mt-2">Submit a post-consultation follow-up report</p>
        </a>
    </div>

    {{-- ── Recent Follow-Up Submissions ── --}}
    @php
        $recentSubmissions = auth()->user()->followUpSubmissions()->latest()->take(3)->get();
    @endphp

    @if($recentSubmissions->isNotEmpty())
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Recent Follow-Up Reports</h2>
            <a href="{{ route('patient.followup.index') }}"
               class="text-blue-600 hover:underline text-sm">View All</a>
        </div>
        <div class="space-y-3">
            @foreach($recentSubmissions as $submission)
            @php
                $colors = [
                    'High'   => 'bg-red-100 text-red-700',
                    'Medium' => 'bg-yellow-100 text-yellow-700',
                    'Low'    => 'bg-green-100 text-green-700',
                ];
                $color = $colors[$submission->urgency_level] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <div class="flex justify-between items-center border border-gray-100 rounded-lg p-3">
                <div>
                    <p class="text-sm text-gray-700 font-medium">
                        {{ implode(', ', array_map(fn($s) => ucwords(str_replace('_', ' ', $s)), $submission->symptom_categories)) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">{{ $submission->created_at->format('d M Y, h:i A') }}</p>
                    @if($submission->doctor_response)
                        <p class="text-xs text-blue-600 mt-1">✓ Doctor responded</p>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
                        {{ $submission->urgency_level ?? 'Pending' }}
                    </span>
                    @if($submission->reviewed_at)
                        <span class="text-xs text-green-600">Reviewed</span>
                    @else
                        <span class="text-xs text-yellow-600">Pending Review</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">
            <a href="{{ route('patient.followup.create') }}"
               class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                + Submit New Follow-Up Report
            </a>
        </div>
    </div>
    @else
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-xl font-semibold">Follow-Up Reports</h2>
        </div>
        <p class="text-gray-500 text-sm mb-4">No follow-up reports submitted yet.</p>
        <a href="{{ route('patient.followup.create') }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            + Submit Your First Report
        </a>
    </div>
    @endif

    <!-- SEARCH / FILTER -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('patient.dashboard') }}" class="flex flex-col sm:flex-row gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search doctor by name or specialization" class="border rounded p-2 flex-1">
            <select name="specialization" class="border rounded p-2">
                <option value="">All Specializations</option>
                @foreach($specializations as $spec)
                    <option value="{{ $spec }}" {{ request('specialization') == $spec ? 'selected' : '' }}>{{ $spec }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
        </form>
    </div>

    <!-- DOCTOR PROFILE & QUEUE -->
    @isset($doctor)
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-bold">{{ $doctor->name }}</h2>
                    <p class="text-gray-600"><strong>Specialization:</strong> {{ $doctor->specialization ?? 'N/A' }}</p>
                    <p class="mt-2"><strong>Bio:</strong> {{ $doctor->bio ?? 'No bio available' }}</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Queue for {{ $date ?? \Carbon\Carbon::today()->toDateString() }}</div>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="font-semibold mb-2">Today's Queue</h3>

                @if(empty($queue) || $queue->isEmpty())
                    <div class="text-gray-500">No tickets for this day yet.</div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach($queue as $index => $q)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <div class="text-sm font-medium">🎟️ Ticket #{{ $q->ticket_number }} — {{ $q->patient->name ?? 'Patient' }}</div>
                                    <div class="text-xs text-gray-500">Status: {{ ucfirst($q->status) }}</div>
                                </div>
                                @if($q->patient_id === auth()->id())
                                    <div class="text-right">
                                        <div class="text-sm text-indigo-600 font-medium">Your ticket</div>
                                        <div class="text-xs text-gray-600">Position: {{ $index + 1 }}</div>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="mt-4">
                <a href="{{ route('patient.dashboard') }}" class="text-blue-600 hover:underline">← Back</a>
            </div>
        </div>
    @endisset

    <!-- AVAILABLE DOCTORS -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Available Doctors</h2>

        @if($doctors->isEmpty())
            <p class="text-gray-500">No doctors match your filters.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($doctors as $doc)
                    <div class="border rounded p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold">{{ $doc->name }}</h3>
                                <div class="text-sm text-gray-500">{{ $doc->specialization ?? 'General' }}</div>
                            </div>
                            <div>
                                <a href="{{ route('patient.showDoctor', $doc->id) }}" class="text-blue-600 hover:underline text-sm">View</a>
                            </div>
                        </div>

                        <form action="{{ route('patient.appointments.store') }}" method="POST" class="mt-4 flex flex-col gap-2">
                            @csrf
                            <input type="hidden" name="doctor_id" value="{{ $doc->id }}">
                            <input type="datetime-local" name="scheduled_at" required class="border rounded p-1 text-sm">
                            <textarea name="notes" rows="2" placeholder="Reason for visit / Notes" class="border rounded p-2 text-sm"></textarea>
                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Book</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- MY UPCOMING APPOINTMENTS -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">My Upcoming Appointments & Tickets</h2>

        @if($appointments->isEmpty())
            <p class="text-gray-500">No upcoming appointments.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Doctor</th>
                        <th class="px-3 py-2 text-left">Date</th>
                        <th class="px-3 py-2 text-left">Ticket</th>
                        <th class="px-3 py-2 text-left">Position</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Notes</th>
                        <th class="px-3 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($appointments->sortBy('scheduled_at') as $appt)
                        <tr>
                            <td class="px-3 py-2">{{ $appt->doctor->name ?? 'N/A' }}</td>
                            <td class="px-3 py-2">{{ \Carbon\Carbon::parse($appt->scheduled_at)->format('d M Y H:i') }}</td>
                            <td class="px-3 py-2">#{{ $appt->ticket_number }}</td>
                            <td class="px-3 py-2">{{ $positions[$appt->id] ?? '-' }}</td>
                            <td class="px-3 py-2 capitalize">{{ $appt->status }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $appt->notes ?? '-' }}</td>
                            <td class="px-3 py-2 flex gap-2">
                                <form action="{{ route('patient.appointments.cancel', $appt->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700">Cancel</button>
                                </form>
                                <form action="{{ route('patient.appointments.reschedule', $appt->id) }}" method="POST" class="flex items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="datetime-local" name="scheduled_at" required class="border rounded p-1 text-xs">
                                    <button type="submit" class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">Reschedule</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection