<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessFollowUpSubmission;
use App\Models\Appointment;
use App\Models\FollowUpSubmission;
use App\Models\User;
use App\Notifications\HighUrgencySubmissionAlert;
use App\Services\AuditLogService;
use App\Services\TriageClassificationService;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function create()
    {
        return view('patient.followup.create');
    }

    public function store(Request $request)
    {
        // ── Step 1: Form Validation ────────────────────────────
        $validated = $request->validate([
            'symptom_categories'   => 'required|array|min:1',
            'symptom_categories.*' => 'in:fever,pain,swelling,medication_side_effect,wound_concern,general_deterioration',
            'severity'             => 'required|integer|min:1|max:5',
            'recovery_status'      => 'required|in:Improving,Stable,Worsening,Uncertain',
            'notes'                => 'nullable|string|max:500',
        ]);

        // ── Step 2: Resolve assigned doctor ───────────────────
        $latestAppointment = Appointment::where('patient_id', auth()->id())
            ->whereNotNull('doctor_id')
            ->latest('appointment_date')
            ->first();

        // ── Step 3: Persist submission ─────────────────────────
        $submission = FollowUpSubmission::create([
            'patient_id'         => auth()->id(),
            'doctor_id'          => $latestAppointment?->doctor_id,
            'symptom_categories' => $validated['symptom_categories'],
            'severity'           => $validated['severity'],
            'recovery_status'    => $validated['recovery_status'],
            'notes'              => $validated['notes'] ?? null,
            'sync_status'        => 'Pending',
        ]);

        // ── Step 4: Triage classification ──────────────────────
        $triage  = new TriageClassificationService();
        $urgency = $triage->classify($submission);
        $submission->update(['urgency_level' => $urgency]);

        // ── Step 5: Audit log ──────────────────────────────────
        AuditLogService::log(
            action:       'submission_created',
            resourceType: 'follow_up_submission',
            resourceId:   $submission->id,
            outcome:      'success',
            meta:         ['urgency' => $urgency]
        );

        // ── Step 6: Notify doctor if High urgency ──────────────
        if ($urgency === 'High' && $latestAppointment?->doctor_id) {
            $doctor = User::find($latestAppointment->doctor_id);

            if ($doctor) {
                $doctor->notify(new HighUrgencySubmissionAlert($submission));

                AuditLogService::log(
                    action:       'high_urgency_notification_sent',
                    resourceType: 'follow_up_submission',
                    resourceId:   $submission->id,
                    outcome:      'success',
                    meta:         ['doctor_id' => $doctor->id, 'doctor_name' => $doctor->name]
                );
            }
        }

        // ── Step 7: Dispatch middleware pipeline ───────────────
        ProcessFollowUpSubmission::dispatch($submission);

        return redirect()
            ->route('patient.followup.confirmation', $submission->id)
            ->with('success', 'Your follow-up report has been submitted.');
    }

    public function confirmation(FollowUpSubmission $submission)
    {
        abort_if($submission->patient_id !== auth()->id(), 403);

        return view('patient.followup.confirmation', compact('submission'));
    }

    public function index()
    {
        $submissions = FollowUpSubmission::where('patient_id', auth()->id())
            ->latest()
            ->get();

        return view('patient.followup.index', compact('submissions'));
    }
}