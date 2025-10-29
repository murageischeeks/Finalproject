@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">📊 Lab Results</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Upload Lab Result Form -->
    <form method="POST" action="{{ route('doctor.lab_results.store') }}" enctype="multipart/form-data" class="bg-white shadow p-6 rounded mb-6">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Select Patient</label>
            <select name="patient_id" class="border rounded w-full p-2" required>
                <option value="">-- Choose Patient --</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Test Type</label>
            <input type="text" name="test_type" class="border rounded w-full p-2" placeholder="e.g., Blood Test" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Notes</label>
            <textarea name="notes" class="border rounded w-full p-2" placeholder="Optional notes..."></textarea>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Upload File</label>
            <input type="file" name="file" class="border rounded w-full p-2" required>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Upload Lab Result</button>
    </form>

    <!-- Existing Lab Results Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 border-b">Patient</th>
                    <th class="px-4 py-3 border-b">Test Type</th>
                    <th class="px-4 py-3 border-b">Notes</th>
                    <th class="px-4 py-3 border-b">Doctor</th>
                    <th class="px-4 py-3 border-b">File</th>
                    <th class="px-4 py-3 border-b">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($labResults as $result)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $result->patient->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-3">{{ $result->test_type ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $result->notes ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $result->doctor->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-3">
                            @if($result->file_path)
                                <a href="{{ asset('storage/'.$result->file_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $result->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center">No lab results yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
