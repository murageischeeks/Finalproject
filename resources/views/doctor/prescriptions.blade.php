@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">📋 Prescriptions</h2>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Prescription Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">➕ Add Prescription</h3>
        <form method="POST" action="{{ route('doctor.prescriptions.store') }}" class="space-y-4">
            @csrf
            <!-- Patient -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Patient</label>
                <select name="patient_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">-- Choose Patient --</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Medicine -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Medicine</label>
                <input type="text" name="medicine" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- Dosage -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dosage</label>
                <input type="text" name="dosage" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow">
                💾 Save Prescription
            </button>
        </form>
    </div>

    <!-- Existing Prescriptions -->
    <h3 class="text-xl font-semibold text-gray-800 mb-4">📑 Existing Prescriptions</h3>
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700 text-sm uppercase">
                    <th class="px-4 py-3 text-left">Medicine</th>
                    <th class="px-4 py-3 text-left">Dosage</th>
                    <th class="px-4 py-3 text-left">Notes</th>
                    <th class="px-4 py-3 text-left">Patient</th>
                    <th class="px-4 py-3 text-left">Prescribed By</th>
                    <th class="px-4 py-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
                @forelse($prescriptions as $prescription)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $prescription->medicine }}</td>
                        <td class="px-4 py-2">{{ $prescription->dosage }}</td>
                        <td class="px-4 py-2">{{ $prescription->notes ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $prescription->patient->name ?? 'Unknown Patient' }}</td>
                        <td class="px-4 py-2">{{ $prescription->doctor->name ?? 'Unknown Doctor' }}</td>
                        <td class="px-4 py-2">{{ $prescription->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            No prescriptions yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
