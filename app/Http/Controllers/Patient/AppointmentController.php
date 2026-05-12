<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentStatusMail;

class AppointmentController extends Controller
{
    /**
     * Book a new appointment
     */
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id'    => 'required|exists:users,id',
            'scheduled_at' => 'required|date|after:now',
            'notes'        => 'nullable|string|max:500',
        ]);

        $scheduledAt = Carbon::parse($request->scheduled_at);
        $appointmentDate = $scheduledAt->toDateString();

        // 🚫 Prevent double booking
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('scheduled_at', $scheduledAt)
            ->whereIn('status', ['pending', 'approved', 'in_progress'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'scheduled_at' => 'Sorry, this time slot has already been booked.',
            ])->withInput();
        }

        // 🎟️ Generate ticket number (auto-increment per doctor per day)
        $latestTicket = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('scheduled_at', $appointmentDate)
            ->max('ticket_number');

        $ticketNumber = $latestTicket ? $latestTicket + 1 : 1;

        // 💾 Create appointment
        $appointment = Appointment::create([
            'patient_id'       => Auth::id(),
            'doctor_id'        => $request->doctor_id,
            'scheduled_at'     => $scheduledAt,
            'appointment_date' => $appointmentDate,
            'ticket_number'    => $ticketNumber,
            'status'           => 'pending',
            'notes'            => $request->notes,
        ]);

        // 📧 Send booking confirmation
        Mail::to(Auth::user()->email)->send(new AppointmentStatusMail($appointment, 'booked'));

        return redirect()->route('patient.dashboard')
            ->with('success', "Appointment booked successfully! Your ticket number is #{$ticketNumber}.");
    }

    /**
     * Cancel an appointment
     */
    public function cancel(Appointment $appointment)
    {
        if ($appointment->patient_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $appointment->update(['status' => 'cancelled']);

        // 📧 Notify via email
        Mail::to(Auth::user()->email)->send(new AppointmentStatusMail($appointment, 'cancelled'));

        return redirect()->route('patient.dashboard')
            ->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Reschedule an appointment
     */
    public function reschedule(Request $request, Appointment $appointment)
    {
        if ($appointment->patient_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $scheduledAt = Carbon::parse($request->scheduled_at);
        $appointmentDate = $scheduledAt->toDateString();

        // 🚫 Prevent conflicts
        $exists = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('scheduled_at', $scheduledAt)
            ->whereIn('status', ['pending', 'approved', 'in_progress'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'scheduled_at' => 'That time slot is already booked. Please choose another.',
            ])->withInput();
        }

        // ✅ Update appointment
        $appointment->update([
            'scheduled_at'     => $scheduledAt,
            'appointment_date' => $appointmentDate,
            'status'           => 'pending',
        ]);

        // 📧 Notify user
        Mail::to(Auth::user()->email)->send(new AppointmentStatusMail($appointment, 'rescheduled'));

        return redirect()->route('patient.dashboard')
            ->with('success', 'Appointment rescheduled successfully.');
    }

    /**
     * Display all patient appointments (sorted by date)
     */
    public function index()
    {
        $appointments = Appointment::where('patient_id', Auth::id())
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return view('patient.appointments.index', compact('appointments'));
    }
}
