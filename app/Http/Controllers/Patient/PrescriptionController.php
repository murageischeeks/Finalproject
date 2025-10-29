<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Prescription;

class PrescriptionController extends Controller
{
    public function index()
    {
        // Get logged-in patient’s prescriptions
        $prescriptions = Prescription::with('doctor')
            ->where('patient_id', auth()->id())
            ->latest()
            ->get();

        return view('patient.prescriptions', compact('prescriptions'));
    }
}
