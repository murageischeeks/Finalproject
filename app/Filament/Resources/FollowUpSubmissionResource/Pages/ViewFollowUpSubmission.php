<?php

namespace App\Filament\Resources\FollowUpSubmissionResource\Pages;

use App\Filament\Resources\FollowUpSubmissionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewFollowUpSubmission extends ViewRecord
{
    protected static string $resource = FollowUpSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return []; // No edit button
    }
}