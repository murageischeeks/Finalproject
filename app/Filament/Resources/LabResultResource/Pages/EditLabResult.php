<?php

namespace App\Filament\Resources\LabResultResource\Pages;

use App\Filament\Resources\LabResultResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLabResult extends EditRecord
{
    protected static string $resource = LabResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
