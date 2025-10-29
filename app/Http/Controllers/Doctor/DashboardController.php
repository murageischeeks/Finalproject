<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\LabResult;
use App\Models\Prescription;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    // Show doctor dashboard
    public function index(Request $request)
    {
        $doctorId = Auth::id();
        $profile = Auth::user(); // Authenticated doctor

        // Optional filter by date (default = show all)
        $filterDate = $request->query('date');

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
                ->whereDate('appointment_date', $today)
                ->count(),
            'upcoming' => Appointment::where('doctor_id', $doctorId)
                ->whereDate('appointment_date', '>', $today)
                ->count(),
            'pending' => Appointment::where('doctor_id', $doctorId)
                ->where('status', 'pending')
                ->count(),
            'completed' => Appointment::where('doctor_id', $doctorId)
                ->where('status', 'completed')
                ->count(),
        ];

        // Live queue (today only, ordered by ticket)
        $queue = Appointment::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $today)
            ->orderBy('ticket_number')
            ->get();

        // Recent lab results created by this doctor
        $labResults = LabResult::with('patient')
            ->where('doctor_id', $doctorId)
            ->latest()
            ->take(5)
            ->get();

        // Recent prescriptions created by this doctor
        $prescriptions = Prescription::with('patient')
            ->where('doctor_id', $doctorId)
            ->latest()
            ->take(5)
            ->get();

        return view('doctor.dashboard', compact(
            'profile', 'stats', 'queue', 'appointments', 'filterDate',
            'labResults', 'prescriptions'
        ));
    }

    // Update appointment status
    public function updateStatus(Request $request, Appointment $appointment)
    {
        if ($appointment->status === 'cancelled') {
            return redirect()->route('doctor.dashboard')->with('error', 'Cannot update a cancelled appointment.');
        }

        if ($appointment->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $appointment->status = $request->status;
        $appointment->save();

        return redirect()->route('doctor.dashboard')->with('success', 'Appointment status updated.');
    }

    // Reschedule appointment
    public function reschedule(Request $request, Appointment $appointment)
    {
        if ($appointment->status === 'cancelled') {
            return redirect()->route('doctor.dashboard')->with('error', 'Cannot reschedule a cancelled appointment.');
        }

        if ($appointment->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $appointment->scheduled_at = $request->scheduled_at;
        $appointment->appointment_date = Carbon::parse($request->scheduled_at)->toDateString();
        $appointment->status = 'pending'; // reset to pending after reschedule
        $appointment->save();

        return redirect()->route('doctor.dashboard')->with('success', 'Appointment rescheduled.');
    }

    // Optional: Show profile edit page
    public function editProfile()
    {
        $doctor = Auth::user();
        return view('doctor.profile.edit', compact('doctor'));
    }

    // Optional: Update profile
    public function updateProfile(Request $request)
    {
        $doctor = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->id,
            'specialization' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
        ]);

        $doctor->update($request->only('name', 'email', 'specialization', 'department', 'license_number'));

        return redirect()->route('doctor.dashboard')->with('success', 'Profile updated.');
    }
}
