<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FollowUpSubmission;
use App\Models\Appointment;

class SubmissionController extends Controller
{
    /**
     * Show the follow-up submission form.
     */
    public function create()
    {
        // Get the patient's most recent appointment to confirm they have a linked doctor
        $appointment = Appointment::where('patient_id', Auth::id())
            ->whereNotNull('doctor_id')
            ->latest('appointment_date')
            ->first();

        if (! $appointment) {
            return redirect()->back()
                ->with('error', 'You must have a booked appointment before submitting a follow-up report.');
        }

        return view('patient.followup.create');
    }

    /**
     * Store the follow-up submission.
     */
    public function store(Request $request)
    {
        $request->validate([
            'symptom_categories' => 'required|array|min:1',
            'symptom_categories.*' => 'string',
            'severity'           => 'required|integer|min:1|max:5',
            'recovery_status'    => 'required|in:Improving,Stable,Worsening,Uncertain',
            'notes'              => 'nullable|string|max:1000',
        ]);

        // ── Get doctor_id from the patient's most recent appointment ──
        $appointment = Appointment::where('patient_id', Auth::id())
            ->whereNotNull('doctor_id')
            ->latest('appointment_date')
            ->first();

        if (! $appointment) {
            return redirect()->back()
                ->with('error', 'No appointment found. Please book an appointment first.');
        }

        // ── Classify urgency ──────────────────────────────────────────
        $urgencyLevel = $this->classifyUrgency(
            $request->symptom_categories,
            $request->severity,
            $request->recovery_status
        );

        // ── Save the submission ───────────────────────────────────────
        $submission = FollowUpSubmission::create([
            'patient_id'         => Auth::id(),
            'doctor_id'          => $appointment->doctor_id,  // ← key fix
            'symptom_categories' => $request->symptom_categories,
            'severity'           => $request->severity,
            'recovery_status'    => $request->recovery_status,
            'notes'              => $request->notes,
            'urgency_level'      => $urgencyLevel,
            'sync_status'        => 'pending',
        ]);

        return redirect()->route('patient.followup.confirmation', $submission->id)
            ->with('success', 'Your follow-up report has been submitted successfully.');
    }

    /**
     * Show submission confirmation screen.
     */
    public function confirmation(FollowUpSubmission $submission)
    {
        // Make sure the logged-in patient owns this submission
        if ($submission->patient_id !== Auth::id()) {
            abort(403);
        }

        return view('patient.followup.confirmation', compact('submission'));
    }

    /**
     * Show all past submissions for the logged-in patient.
     */
    public function index()
    {
        $submissions = FollowUpSubmission::where('patient_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('patient.followup.index', compact('submissions'));
    }

    // ── Private: Triage Classification Logic ─────────────────────────
    private function classifyUrgency(array $symptoms, int $severity, string $recoveryStatus): string
    {
        // Symptom weights
        $symptomWeights = [
            'fever'               => 15,
            'pain'                => 10,
            'swelling'            => 10,
            'medication_side_effect' => 20,
            'wound_concern'       => 15,
            'general_deterioration' => 25,
        ];

        // Severity multiplier (1–5 scale mapped to multiplier)
        $severityMultipliers = [1 => 0.5, 2 => 0.75, 3 => 1.0, 4 => 1.25, 5 => 1.5];

        // Recovery status modifier
        $recoveryModifiers = [
            'Improving'  => 0.7,
            'Stable'     => 1.0,
            'Uncertain'  => 1.2,
            'Worsening'  => 1.5,
        ];

        // Calculate base score from selected symptoms
        $baseScore = 0;
        foreach ($symptoms as $symptom) {
            $baseScore += $symptomWeights[$symptom] ?? 10;
        }

        // Apply severity multiplier and recovery modifier
        $multiplier = $severityMultipliers[$severity] ?? 1.0;
        $modifier   = $recoveryModifiers[$recoveryStatus] ?? 1.0;
        $finalScore = $baseScore * $multiplier * $modifier;

        // Classify based on score thresholds
        if ($finalScore >= 70) {
            return 'High';
        } elseif ($finalScore >= 40) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }
}