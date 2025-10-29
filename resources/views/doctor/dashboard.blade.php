@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- PROFILE -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 shadow sm:rounded-lg p-6 flex justify-between items-center text-white">
            <div class="flex items-center space-x-6">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow">
                    <x-application-logo class="w-12 h-12 text-blue-600" />
                </div>
                <div>
                    <h3 class="text-xl font-bold">{{ $profile->name ?? '—' }}</h3>
                    <div class="text-sm opacity-90">{{ $profile->email ?? '—' }}</div>
                    <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
                        <div><strong>Specialization:</strong> {{ $profile->specialization ?? '—' }}</div>
                        <div><strong>Department:</strong> {{ $profile->department ?? '—' }}</div>
                        <div><strong>License #:</strong> {{ $profile->license_number ?? '—' }}</div>
                        <div>
                            <strong>Status:</strong>
                            @if($profile->license_verified ?? false)
                                <span class="text-green-200 font-medium">Verified</span>
                            @else
                                <span class="text-yellow-200 font-medium">Pending</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col space-y-2">
                <a href="{{ route('doctor.profile.edit') }}" 
                   class="bg-white text-blue-600 px-4 py-2 rounded text-sm font-medium hover:bg-gray-100 transition shadow">
                    Edit Profile
                </a>
                <a href="{{ route('doctor.lab_results.index') }}" 
                   class="bg-green-500 text-white px-4 py-2 rounded text-sm font-medium hover:bg-green-600 transition shadow">
                    Lab Results
                </a>
                <a href="{{ route('doctor.prescriptions.index') }}" 
                   class="bg-purple-500 text-white px-4 py-2 rounded text-sm font-medium hover:bg-purple-600 transition shadow">
                    Prescriptions
                </a>
            </div>
        </div>

        <!-- LICENSE VERIFICATION CHECK -->
        @if(!($profile->license_verified ?? false))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow">
                <strong>⚠️ Your license is pending admin verification.</strong><br>
                You currently have limited access. Once verified, you’ll be able to manage appointments, view the queue, and issue prescriptions.
            </div>
        @else
            <!-- STATS -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                @foreach(['today_appointments' => 'Appointments Today', 'upcoming' => 'Upcoming', 'pending' => 'Pending', 'completed' => 'Completed'] as $key => $label)
                    <div class="bg-white shadow-md hover:shadow-lg transition rounded-lg p-4 text-center border-t-4 
                        @if($key == 'today_appointments') border-blue-500 
                        @elseif($key == 'upcoming') border-indigo-500 
                        @elseif($key == 'pending') border-yellow-500 
                        @elseif($key == 'completed') border-green-500 
                        @endif">
                        <div class="text-sm text-gray-500">{{ $label }}</div>
                        <div class="text-3xl font-extrabold text-gray-800">{{ $stats[$key] ?? 0 }}</div>
                    </div>
                @endforeach
            </div>

            <!-- LIVE QUEUE -->
            <div class="bg-white shadow sm:rounded-lg p-6 overflow-x-auto">
                <h2 class="text-xl font-semibold mb-4">Live Queue (Today)</h2>
                @if(isset($queue) && $queue->isNotEmpty())
                    <ul class="divide-y divide-gray-200">
                        @foreach($queue as $appointment)
                            <li class="py-3 flex justify-between items-center hover:bg-gray-50 transition">
                                <div>
                                    <span class="font-semibold text-blue-600">#{{ $appointment->ticket_number ?? '—' }}</span>
                                    — {{ $appointment->patient->name ?? 'N/A' }}
                                    <span class="text-sm text-gray-500">
                                        ({{ isset($appointment->appointment_date) ? \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') : '—' }})
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs rounded 
                                        @if(($appointment->status ?? '') == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif(($appointment->status ?? '') == 'in_progress') bg-blue-100 text-blue-800
                                        @elseif(($appointment->status ?? '') == 'completed') bg-green-100 text-green-800
                                        @elseif(($appointment->status ?? '') == 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($appointment->status ?? '—') }}
                                    </span>
                                    <button 
                                        x-data @click="$dispatch('open-modal', { content: '{{ addslashes($appointment->notes ?? 'No notes') }}' })"
                                        class="bg-gray-100 px-2 py-1 rounded text-sm hover:bg-gray-200"
                                    >
                                        View Note
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500">No patients in the queue today.</p>
                @endif
            </div>

            <!-- APPOINTMENTS TABLE -->
            <div class="bg-white shadow sm:rounded-lg p-6 overflow-x-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Appointments</h2>
                    <form method="GET" action="{{ route('doctor.dashboard') }}" class="flex space-x-2 items-center">
                        <input type="date" name="date" value="{{ $filterDate ?? '' }}" class="border rounded px-2 py-1 text-sm">
                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Filter</button>
                        @if(!empty($filterDate))
                            <a href="{{ route('doctor.dashboard') }}" class="px-3 py-1 border rounded text-sm hover:bg-gray-100">Clear</a>
                        @endif
                    </form>
                </div>

                @if(isset($appointments) && $appointments->isNotEmpty())
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Ticket #</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Patient</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Scheduled At</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Notes</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($appointments as $appointment)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 font-medium text-gray-700">#{{ $appointment->ticket_number ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ $appointment->patient->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ isset($appointment->appointment_date) ? \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y H:i') : '—' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs rounded
                                            @if(($appointment->status ?? '') == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif(($appointment->status ?? '') == 'in_progress') bg-blue-100 text-blue-800
                                            @elseif(($appointment->status ?? '') == 'completed') bg-green-100 text-green-800
                                            @elseif(($appointment->status ?? '') == 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($appointment->status ?? '—') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        @if(!empty($appointment->notes))
                                            <button 
                                                x-data @click="$dispatch('open-modal', { content: '{{ addslashes($appointment->notes) }}' })"
                                                class="bg-gray-100 px-2 py-1 rounded text-sm hover:bg-gray-200"
                                            >
                                                View Note
                                            </button>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">
                                        @if(($appointment->status ?? '') !== 'cancelled')
                                            <form method="POST" action="{{ route('doctor.appointments.updateStatus', $appointment->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" onchange="this.form.submit()" class="border rounded text-sm w-full">
                                                    <option value="">Change Status...</option>
                                                    <option value="pending" {{ ($appointment->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="in_progress" {{ ($appointment->status ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="completed" {{ ($appointment->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                </select>
                                            </form>
                                        @else
                                            <span class="text-red-600 font-semibold">Cancelled by patient</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500">No appointments found.</p>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Modal -->
<div x-data="{ show: false, content: '' }"
     x-on:open-modal.window="show = true; content = $event.detail.content"
     x-show="show"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     style="display: none;">
    <div @click.away="show = false" class="bg-white p-6 rounded shadow-lg max-w-md w-full">
        <h3 class="text-lg font-semibold mb-2">Patient Note</h3>
        <p x-text="content" class="text-gray-700"></p>
        <button @click="show = false" class="mt-4 bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Close</button>
    </div>
</div>
@endsection
