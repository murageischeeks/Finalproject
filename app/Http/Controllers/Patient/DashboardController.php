<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use App\Models\LabResult;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    // Show patient dashboard
    public function index(Request $request)
    {
        $patient = Auth::user();

        // My upcoming appointments
        $appointments = Appointment::with('doctor')
            ->where('patient_id', $patient->id)
            ->where('appointment_date', '>=', Carbon::today())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('ticket_number', 'asc')
            ->get();

        // Search + specialization filter
        $query = User::where('role', 'doctor');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('specialization')) {
            $query->where('specialization', $request->specialization);
        }

        $doctors = $query->get();

        $specializations = User::where('role', 'doctor')
            ->whereNotNull('specialization')
            ->distinct()
            ->pluck('specialization');

        // Compute positions
        $positions = [];
        foreach ($appointments as $appt) {
            $ahead = Appointment::where('doctor_id', $appt->doctor_id)
                ->whereDate('appointment_date', $appt->appointment_date)
                ->whereIn('status', ['pending', 'in_progress'])
                ->where('ticket_number', '<', $appt->ticket_number)
                ->count();
            $positions[$appt->id] = $ahead + 1;
        }

        // Recent lab results for this patient
        $labResults = LabResult::with('doctor')
            ->where('patient_id', $patient->id)
            ->latest()
            ->take(5)
            ->get();

        // Recent prescriptions for this patient
        $prescriptions = Prescription::with('doctor')
            ->where('patient_id', $patient->id)
            ->latest()
            ->take(5)
            ->get();

        $profile = $patient; // pass patient profile to blade

        return view('patient.dashboard', compact(
            'appointments', 'doctors', 'specializations', 'positions', 'profile',
            'labResults', 'prescriptions'
        ));
    }

    // Show doctor profile & queue
    public function showDoctor(Request $request, $id)
    {
        $doctor = User::where('role', 'doctor')->findOrFail($id);
        $date = $request->input('date') ? Carbon::parse($request->input('date'))->toDateString() : Carbon::today()->toDateString();

        $queue = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date)
            ->orderBy('ticket_number', 'asc')
            ->get();

        $myTicket = $queue->firstWhere('patient_id', Auth::id());
        $myPosition = null;
        if ($myTicket) {
            $ahead = $queue->whereIn('status', ['pending', 'in_progress'])
                ->filter(fn($a) => $a->ticket_number < $myTicket->ticket_number)
                ->count();
            $myPosition = $ahead + 1;
        }

        $specializations = User::where('role', 'doctor')->whereNotNull('specialization')->distinct()->pluck('specialization');
        $doctors = User::where('role', 'doctor')->get();

        $appointments = Appointment::with('doctor')
            ->where('patient_id', Auth::id())
            ->where('appointment_date', '>=', Carbon::today())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('ticket_number', 'asc')
            ->get();

        $positions = [];
        foreach ($appointments as $appt) {
            $ahead = Appointment::where('doctor_id', $appt->doctor_id)
                ->whereDate('appointment_date', $appt->appointment_date)
                ->whereIn('status', ['pending', 'in_progress'])
                ->where('ticket_number', '<', $appt->ticket_number)
                ->count();
            $positions[$appt->id] = $ahead + 1;
        }

        $profile = Auth::user(); // patient profile

        return view('patient.dashboard', compact(
            'doctor', 'queue', 'date',
            'appointments', 'doctors', 'specializations', 'positions', 'myTicket', 'myPosition', 'profile'
        ));
    }

    // Optional: Edit patient profile
    public function editProfile()
    {
        $patient = Auth::user();
        return view('patient.profile.edit', compact('patient'));
    }

    // Optional: Update patient profile
    public function updateProfile(Request $request)
    {
        $patient = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $patient->id,
        ]);

        $patient->update($request->only('name', 'email'));

        return redirect()->route('patient.dashboard')->with('success', 'Profile updated.');
    }
}
