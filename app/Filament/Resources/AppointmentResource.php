<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Hospital Management';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('doctor_id')
                ->label('Doctor')
                ->options(Doctor::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\Select::make('patient_id')
                ->label('Patient')
                ->options(Patient::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\DateTimePicker::make('scheduled_at')->label('Scheduled At'),
            Forms\Components\DatePicker::make('appointment_date')->label('Appointment Date')->required(),
            Forms\Components\TextInput::make('ticket_number')->numeric(),
            Forms\Components\Select::make('status')->options([
                'Pending' => 'Pending',
                'Confirmed' => 'Confirmed',
                'Completed' => 'Completed',
                'Cancelled' => 'Cancelled',
            ])->default('Pending'),
            Forms\Components\Textarea::make('notes')->rows(3),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->poll('5s')
            ->columns([
                Tables\Columns\TextColumn::make('doctor.name')->label('Doctor'),
                Tables\Columns\TextColumn::make('patient.name')->label('Patient'),
                Tables\Columns\TextColumn::make('appointment_date')->date(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('ticket_number'),
                Tables\Columns\TextColumn::make('notes')->limit(25),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Created'),
            ])
            ->filters([])
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
