<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable
{
    use HasFactory;

    protected $table = 'patients'; // make sure it matches your DB

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'age',
        'gender',
    ];

    // Relationships
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function labResults()
    {
        return $this->hasMany(LabResult::class);
    }
}
