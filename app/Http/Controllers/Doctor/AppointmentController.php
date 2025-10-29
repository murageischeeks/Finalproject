<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Appointment;
use App\Mail\AppointmentStatusMail;

class AppointmentController extends Controller
{
    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Ensure only the doctor assigned to this appointment can update
        if ($appointment->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled,approved,rejected',
        ]);

        $appointment->status = $request->status;
        $appointment->save();

        // ✅ Automatically send email to patient
        if ($appointment->patient && $appointment->patient->email) {
            Mail::to($appointment->patient->email)->send(
                new AppointmentStatusMail($appointment, $request->status)
            );
        }

        return back()->with('success', 'Appointment status updated and email sent.');
    }
}
