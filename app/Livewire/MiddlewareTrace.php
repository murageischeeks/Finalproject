<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\FollowUpSubmission;
use Livewire\Component;

class MiddlewareTrace extends Component
{
    public FollowUpSubmission $submission;
    public array $trace = [];

    public function mount(int $submissionId): void
    {
        $this->submission = FollowUpSubmission::with('patient', 'doctor')
            ->findOrFail($submissionId);

        $this->trace = AuditLog::where('resource_type', 'follow_up_submission')
            ->where('resource_id', $this->submission->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.middleware-trace')
            ->layout('layouts.app');
    }
}