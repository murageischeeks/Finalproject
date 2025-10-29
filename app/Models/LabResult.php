<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'test_type',
        'notes',
        'file_path',
    ];

    // Doctor relationship
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // Patient relationship
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
