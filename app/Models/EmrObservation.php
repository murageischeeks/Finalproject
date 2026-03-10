<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmrObservation extends Model
{
    protected $fillable = [
        'uuid',
        'person',
        'concept',
        'obs_datetime',
        'value',
        'comment',
        'follow_up_submission_id',
    ];

    protected $casts = [
        'obs_datetime' => 'datetime',
    ];

    public function followUpSubmission(): BelongsTo
    {
        return $this->belongsTo(FollowUpSubmission::class);
    }
}