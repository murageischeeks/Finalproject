<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LabResultResource\Pages;
use App\Models\LabResult;
use App\Models\Doctor;
use App\Models\Patient;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;

class LabResultResource extends Resource
{
    protected static ?string $model = LabResult::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Hospital Management';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('doctor_id')
                ->label('Doctor')
                ->options(Doctor::all()->pluck('name', 'id'))
                ->searchable(),
            Forms\Components\Select::make('patient_id')
                ->label('Patient')
                ->options(Patient::all()->pluck('name', 'id'))
                ->searchable(),
            Forms\Components\TextInput::make('test_type')->required(),
            Forms\Components\Textarea::make('notes')->rows(3),
            Forms\Components\FileUpload::make('file_path')->label('Upload Result File'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->poll('5s')
            ->columns([
                Tables\Columns\TextColumn::make('doctor.name')->label('Doctor'),
                Tables\Columns\TextColumn::make('patient.name')->label('Patient'),
                Tables\Columns\TextColumn::make('test_type'),
                Tables\Columns\TextColumn::make('file_path')->label('File'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLabResults::route('/'),
            'create' => Pages\CreateLabResult::route('/create'),
            'edit' => Pages\EditLabResult::route('/{record}/edit'),
        ];
    }
}
