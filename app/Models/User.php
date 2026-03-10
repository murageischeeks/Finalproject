<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'openmrs_uuid',
        'license_number',
        'license_verified',
        'department',
        'specialization',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'license_verified'  => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function doctorAppointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function patientAppointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function followUpSubmissions()
    {
        return $this->hasMany(FollowUpSubmission::class, 'patient_id');
    }

    // ── Role Helpers ───────────────────────────────────────────
    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}