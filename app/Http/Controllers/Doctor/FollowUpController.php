<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\FollowUpSubmission;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    // Main triage dashboard — all pending submissions sorted by urgency
    public function index(Request $request)
    {
        $query = FollowUpSubmission::with('patient')
            ->where('doctor_id', Auth::guard('doctor')->id())
            ->whereNull('reviewed_at')
            ->byUrgency()
            ->orderBy('created_at', 'asc');

        // Filters
        if ($request->filled('urgency')) {
            $query->where('urgency_level', $request->urgency);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('symptom')) {
            $query->whereJsonContains('symptom_categories', $request->symptom);
        }

        if ($request->filled('sync_status')) {
            $query->where('sync_status', $request->sync_status);
        }

        $submissions = $query->paginate(15);

        // Count for badge on nav
        $highUrgencyCount = FollowUpSubmission::whereNull('reviewed_at')
            ->where('doctor_id', Auth::guard('doctor')->id())
            ->where('urgency_level', 'High')
            ->count();

        AuditLogService::log(
            action:       'dashboard_viewed',
            resourceType: 'follow_up_submission',
            resourceId:   0,
            outcome:      'success'
        );

        return view('doctor.followup.index', compact('submissions', 'highUrgencyCount'));
    }

    // Full detail view of a single submission
    public function show(FollowUpSubmission $submission)
    {
        // ── FIX: ensure doctor can only view their own patients' submissions ──
        abort_if((int) $submission->doctor_id !== (int) Auth::guard('doctor')->id(), 403);

        $submission->load('patient');

        // Load this patient's previous submissions for context
        $history = FollowUpSubmission::where('patient_id', $submission->patient_id)
            ->where('id', '!=', $submission->id)
            ->latest()
            ->take(5)
            ->get();

        AuditLogService::log(
            action:       'submission_viewed',
            resourceType: 'follow_up_submission',
            resourceId:   $submission->id,
            outcome:      'success'
        );

        return view('doctor.followup.show', compact('submission', 'history'));
    }

    // Mark submission as reviewed
    public function markReviewed(FollowUpSubmission $submission)
    {
        // ── FIX: ownership check ──
        abort_if((int) $submission->doctor_id !== (int) Auth::guard('doctor')->id(), 403);

        $submission->update(['reviewed_at' => now()]);

        AuditLogService::log(
            action:       'submission_reviewed',
            resourceType: 'follow_up_submission',
            resourceId:   $submission->id,
            outcome:      'success'
        );

        return redirect()
            ->route('doctor.followup.index')
            ->with('success', 'Submission marked as reviewed.');
    }

    // Doctor responds to a submission
    public function respond(Request $request, FollowUpSubmission $submission)
    {
        // ── FIX: ownership check ──
        abort_if((int) $submission->doctor_id !== (int) Auth::guard('doctor')->id(), 403);

        $request->validate([
            'doctor_response' => 'required|string|max:1000',
        ]);

        $submission->update([
            'doctor_response' => $request->doctor_response,
            'reviewed_at'     => now(),
        ]);

        AuditLogService::log(
            action:       'submission_responded',
            resourceType: 'follow_up_submission',
            resourceId:   $submission->id,
            outcome:      'success',
            meta:         ['response_length' => strlen($request->doctor_response)]
        );

        return redirect()
            ->route('doctor.followup.index')
            ->with('success', 'Response sent to patient.');
    }

    // Auto-refresh endpoint — called by JS every 60s
    public function refresh()
    {
        // ── FIX: was missing doctor_id filter, showing all doctors' counts ──
        $count = FollowUpSubmission::whereNull('reviewed_at')
            ->where('doctor_id', Auth::guard('doctor')->id())
            ->where('urgency_level', 'High')
            ->count();

        return response()->json(['high_urgency_count' => $count]);
    }
}

