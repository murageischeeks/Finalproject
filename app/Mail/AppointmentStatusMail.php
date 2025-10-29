<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;

class AppointmentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, $status)
    {
        $this->appointment = $appointment;
        $this->status = $status;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Appointment Status Update')
                    ->view('emails.appointment-status')
                    ->with([
                        'appointment' => $this->appointment,
                        'status' => $this->status, // ✅ ensure this line exists
                    ]);
    }
}
