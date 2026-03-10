<?php

namespace App\Filament\Resources\FollowUpSubmissionResource\Pages;

use App\Filament\Resources\FollowUpSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListFollowUpSubmissions extends ListRecords
{
    protected static string $resource = FollowUpSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return []; // No create button
    }
}