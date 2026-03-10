<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TriageRule extends Model
{
    protected $fillable = ['rule_type', 'key', 'weight'];
}