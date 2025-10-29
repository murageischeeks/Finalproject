<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\LabResult;
use Illuminate\Support\Facades\Auth;

class LabResultController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:patient']);
    }

    public function index()
    {
        // Load lab results with doctor relationship
        $labResults = LabResult::where('patient_id', Auth::id())
            ->with('doctor')
            ->latest()
            ->get();

        return view('patient.lab_results', compact('labResults'));
    }
}
