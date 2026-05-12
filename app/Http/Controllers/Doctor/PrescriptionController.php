<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    public function index()
    {
        // Get all patients (users with role patient)
        $patients = User::where('role', 'patient')->get();

        // Get prescriptions with doctor & patient to avoid null errors
        $prescriptions = Prescription::with(['doctor', 'patient'])
            ->where('doctor_id', Auth::guard('doctor')->id())
            ->latest()
            ->get();

        return view('doctor.prescriptions', compact('patients', 'prescriptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'medicine'   => 'required|string|max:255',
            'dosage'     => 'required|string|max:255',
            'notes'      => 'nullable|string',
        ]);

        Prescription::create([
            'doctor_id'  => Auth::guard('doctor')->id(),
            'patient_id' => $request->patient_id,
            'medicine'   => $request->medicine,
            'dosage'     => $request->dosage,
            'notes'      => $request->notes,
        ]);

        return redirect()->route('doctor.prescriptions.index')
                         ->with('success', 'Prescription saved successfully!');
    }
}
