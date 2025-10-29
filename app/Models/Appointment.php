<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\DoctorAvailability;
use App\Models\DoctorAvailabilityException;

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
        'scheduled_at'     => 'datetime',
        'appointment_date' => 'date',
    ];

    /**
     * Relationship: belongs to patient
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Relationship: belongs to doctor
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Return next ticket number for a doctor on a specific date
     */
    public static function nextTicketNumber(int $doctorId, string $date): int
    {
        $max = self::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->max('ticket_number');

        return ($max ? $max : 0) + 1;
    }

    /**
     * Check if this appointment conflicts with doctor's availability
     */
    public static function isDoctorAvailable(int $doctorId, Carbon $datetime): bool
    {
        // Check specific exceptions
        $exceptionExists = DoctorAvailabilityException::where('doctor_id', $doctorId)
            ->whereDate('date', $datetime->toDateString())
            ->exists();

        if ($exceptionExists) return false;

        // Check weekly availability
        $available = DoctorAvailability::where('doctor_id', $doctorId)
            ->where('day_of_week', $datetime->format('l'))
            ->where('is_active', true)
            ->whereTime('start_time', '<=', $datetime->format('H:i:s'))
            ->whereTime('end_time', '>', $datetime->format('H:i:s'))
            ->exists();

        return $available;
    }
}
