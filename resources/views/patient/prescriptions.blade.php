@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">My Prescriptions</h2>

    <table class="min-w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-4 py-2">Medicine</th>
                <th class="border px-4 py-2">Dosage</th>
                <th class="border px-4 py-2">Notes</th>
                <th class="border px-4 py-2">Prescribed By</th>
                <th class="border px-4 py-2">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prescriptions as $prescription)
                <tr class="cursor-pointer hover:bg-gray-100"
                    onclick="openPrescriptionModal(
                        '{{ $prescription->medicine }}',
                        '{{ $prescription->dosage }}',
                        '{{ $prescription->notes ?? 'No notes' }}',
                        '{{ $prescription->doctor->name ?? 'Unknown Doctor' }}',
                        '{{ $prescription->created_at->format('d M Y') }}'
                    )">
                    <td class="border px-4 py-2">{{ $prescription->medicine }}</td>
                    <td class="border px-4 py-2">{{ $prescription->dosage }}</td>
                    <td class="border px-4 py-2">{{ $prescription->notes ?? 'No notes' }}</td>
                    <td class="border px-4 py-2">{{ $prescription->doctor->name ?? 'Unknown Doctor' }}</td>
                    <td class="border px-4 py-2">{{ $prescription->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center p-4">No prescriptions yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="prescriptionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 w-1/2">
        <h2 class="text-xl font-bold mb-4">Prescription Details</h2>
        <p><strong>Medicine:</strong> <span id="modalMedicine"></span></p>
        <p><strong>Dosage:</strong> <span id="modalDosage"></span></p>
        <p><strong>Notes:</strong> <span id="modalNotes"></span></p>
        <p><strong>Prescribed By:</strong> <span id="modalDoctor"></span></p>
        <p><strong>Date:</strong> <span id="modalDate"></span></p>
        <button onclick="closePrescriptionModal()" class="mt-4 bg-red-500 text-white px-4 py-2 rounded">Close</button>
    </div>
</div>

<script>
function openPrescriptionModal(medicine, dosage, notes, doctor, date) {
    document.getElementById('modalMedicine').innerText = medicine;
    document.getElementById('modalDosage').innerText = dosage;
    document.getElementById('modalNotes').innerText = notes;
    document.getElementById('modalDoctor').innerText = doctor;
    document.getElementById('modalDate').innerText = date;
    document.getElementById('prescriptionModal').classList.remove('hidden');
}
function closePrescriptionModal() {
    document.getElementById('prescriptionModal').classList.add('hidden');
}
</script>
@endsection
