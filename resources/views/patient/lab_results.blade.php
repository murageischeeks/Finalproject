@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📊 My Lab Results</h1>

    @if($labResults->isEmpty())
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg">
            No lab results available yet.
        </div>
    @else
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 border-b">Test</th>
                        <th class="px-4 py-3 border-b">Notes</th>
                        <th class="px-4 py-3 border-b">Doctor</th>
                        <th class="px-4 py-3 border-b">File</th>
                        <th class="px-4 py-3 border-b">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($labResults as $result)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $result->test_type ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $result->notes ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $result->doctor->name ?? 'Unknown Doctor' }}</td>
                            <td class="px-4 py-3">
                                @if($result->file_path)
                                    <a href="{{ asset('storage/' . $result->file_path) }}" target="_blank" class="text-blue-600 underline">
                                        View File
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $result->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
