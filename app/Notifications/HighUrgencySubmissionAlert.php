<?php

namespace App\Notifications;

use App\Models\FollowUpSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HighUrgencySubmissionAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public FollowUpSubmission $submission) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $patient  = $this->submission->patient;
        $symptoms = implode(', ', array_map(
            fn($s) => ucwords(str_replace('_', ' ', $s)),
            $this->submission->symptom_categories
        ));

        return (new MailMessage)
            ->subject('🔴 High Urgency Follow-Up Alert — Action Required')
            ->greeting('Hello Dr. ' . $notifiable->name . ',')
            ->line('A patient has submitted a **High Urgency** follow-up report that requires your immediate attention.')
            ->line('---')
            ->line('**Patient:** ' . $patient->name)
            ->line('**Symptoms:** ' . $symptoms)
            ->line('**Severity:** ' . $this->submission->severity . ' / 5')
            ->line('**Recovery Status:** ' . $this->submission->recovery_status)
            ->line('**Submitted:** ' . $this->submission->created_at->format('d M Y, h:i A'))
            ->action('Review Submission Now', url('/doctor/followup/' . $this->submission->id))
            ->line('Please log in to the system and review this submission as soon as possible.')
            ->salutation('BleakHospital Patient Follow-Up System');
    }
}