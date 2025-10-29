<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'specialization',
        'bio',
        'rating',
        'department',
        'license_number',
        'license_verified'
    ];

    // Doctor has many appointments
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
