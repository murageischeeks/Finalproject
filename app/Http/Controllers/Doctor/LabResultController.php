<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LabResult;
use Illuminate\Support\Facades\Auth;

class LabResultController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:doctor']);
    }

    public function index()
    {
        // ✅ Fetch patients for the dropdown
        $patients = User::where('role', 'patient')->get();

        // ✅ Fetch existing lab results with doctor & patient info
        $labResults = LabResult::with(['patient', 'doctor'])->latest()->get();

        return view('doctor.lab_results', compact('patients', 'labResults'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'file'       => 'required|mimes:pdf,jpg,png,doc,docx|max:2048',
            'notes'      => 'nullable|string',
            'test_type'  => 'required|string|max:255',
        ]);

        // Store uploaded file
        $path = $request->file('file')->store('lab_results', 'public');

        // ✅ Create lab result and attach doctor_id
        LabResult::create([
    'doctor_id'  => Auth::id(), // now required
    'patient_id' => $request->patient_id,
    'test_type'  => $request->test_type,
    'notes'      => $request->notes,
    'file_path'  => $path,
]);

        return redirect()->route('doctor.lab_results.index')
                         ->with('success', 'Lab result uploaded successfully.');
    }
}
