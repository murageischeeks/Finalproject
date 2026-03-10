<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\LabResult;
use App\Models\Prescription;
use App\Models\FollowUpSubmission;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $doctorId = Auth::id();
        $profile  = Auth::user();

        // Optional date filter
        $filterDate        = $request->query('date');
        $appointmentsQuery = Appointment::with('patient')
            ->where('doctor_id', $doctorId)
            ->orderBy('appointment_date');

        if ($filterDate) {
            $appointmentsQuery->whereDate('appointment_date', $filterDate);
        }

        $appointments = $appointmentsQuery->get();

        // Stats
        $today = Carbon::today()->toDateString();
        $stats = [
            'today_appointments' => Appointment::where('doctor_id', $doctorId)
                ->whereDate('appointment_date', $today)->count(),
            'upcoming'  => Appointment::where('doctor_id', $doctorId)
                ->whereDate('appointment_date', '>', $today)->count(),
            'pending'   => Appointment::where('doctor_id', $doctorId)
                ->where('status', 'pending')->count(),
            'completed' => Appointment::where('doctor_id', $doctorId)
                ->where('status', 'completed')->count(),
        ];

        // Live queue
        $queue = Appointment::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $today)
            ->orderBy('ticket_number')
            ->get();

        // Recent lab results & prescriptions
        $labResults = LabResult::with('patient')
            ->where('doctor_id', $doctorId)
            ->latest()->take(5)->get();

        $prescriptions = Prescription::with('patient')
            ->where('doctor_id', $doctorId)
            ->latest()->take(5)->get();

        // ── Follow-Up Data ─────────────────────────────────────
        $highUrgencyCount = FollowUpSubmission::whereNull('reviewed_at')
            ->where('doctor_id', $doctorId)
            ->where('urgency_level', 'High')
            ->count();

        $recentFollowUps = FollowUpSubmission::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereNull('reviewed_at')
            ->byUrgency()
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        return view('doctor.dashboard', compact(
            'profile', 'stats', 'queue', 'appointments', 'filterDate',
            'labResults', 'prescriptions',
            'highUrgencyCount', 'recentFollowUps'
        ));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        if ($appointment->status === 'cancelled') {
            return redirect()->route('doctor.dashboard')
                ->with('error', 'Cannot update a cancelled appointment.');
        }

        if ($appointment->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $appointment->status = $request->status;
        $appointment->save();

        return redirect()->route('doctor.dashboard')
            ->with('success', 'Appointment status updated.');
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        if ($appointment->status === 'cancelled') {
            return redirect()->route('doctor.dashboard')
                ->with('error', 'Cannot reschedule a cancelled appointment.');
        }

        if ($appointment->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $appointment->scheduled_at     = $request->scheduled_at;
        $appointment->appointment_date = Carbon::parse($request->scheduled_at)->toDateString();
        $appointment->status           = 'pending';
        $appointment->save();

        return redirect()->route('doctor.dashboard')
            ->with('success', 'Appointment rescheduled.');
    }

    public function editProfile()
    {
        $doctor = Auth::user();
        return view('doctor.profile.edit', compact('doctor'));
    }

    public function updateProfile(Request $request)
    {
        $doctor = Auth::user();

        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $doctor->id,
            'specialization' => 'nullable|string|max:255',
            'department'     => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
        ]);

        $doctor->update($request->only(
            'name', 'email', 'specialization', 'department', 'license_number'
        ));

        return redirect()->route('doctor.dashboard')
            ->with('success', 'Profile updated.');
    }
}