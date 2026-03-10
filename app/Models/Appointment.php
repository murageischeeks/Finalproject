<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'scheduled_at',
        'appointment_date',
        'ticket_number',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'appointment_date' => 'date',
    ];

    // Relationship: belongs to patient (user)
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // Relationship: belongs to doctor (also User model; doctor is a user with role 'doctor')
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Return the next ticket number for a given doctor and date (1-based).
     */
    public static function nextTicketNumber(int $doctorId, string $date): int
    {
        $max = self::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->max('ticket_number');

        return ($max ? $max : 0) + 1;
    }
}
