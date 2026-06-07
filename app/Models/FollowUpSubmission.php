<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FollowUpSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'symptom_categories',
        'severity',
        'recovery_status',
        'notes',
        'urgency_level',
        'sync_status',
        'openmrs_observation_uuid',
        'reviewed_at',
        'doctor_response',
    ];

    protected $casts = [
        'symptom_categories' => 'array',
        'reviewed_at'        => 'datetime',
        // NFR1: AES-256-CBC encryption at rest for sensitive free-text fields (KDPA 2019)
        'notes'              => 'encrypted',
        'doctor_response'    => 'encrypted',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'resource_id')->where('resource_type', 'follow_up_submission');
    }

    // ── Scopes for dashboard filtering ────────────────────────
    public function scopePending($query)
    {
        return $query->whereNull('reviewed_at');
    }

    public function scopeByUrgency($query)
    {
        return $query->orderByRaw("CASE urgency_level 
            WHEN 'High' THEN 1 
            WHEN 'Medium' THEN 2 
            WHEN 'Low' THEN 3 
            ELSE 4 END");
    }
}