<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class DoctorResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Hospital Management';
    protected static ?string $navigationLabel = 'Doctors';
    protected static ?string $pluralModelLabel = 'Doctors';

    public static function getEloquentQuery(): Builder
    {
        // Only show users with the "doctor" role
        return parent::getEloquentQuery()->where('role', 'doctor');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('license_number')
                ->label('License Number'),

            Forms\Components\Toggle::make('license_verified')
                ->label('License Verified'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label('ID'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('license_number')
                    ->label('License #'),

                Tables\Columns\IconColumn::make('license_verified')
                    ->boolean()
                    ->label('Verified'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Joined'),
            ])
            ->actions([
                Action::make('verify')
                    ->label('Verify License')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => ! $record->license_verified)
                    ->action(function ($record, $livewire) {
                        $user = User::find($record->id);

                        if ($user) {
                            $user->update(['license_verified' => true]);
                        }

                        Notification::make()
                            ->title('Doctor license verified')
                            ->success()
                            ->send();

                        // ✅ Proper Filament v3 table refresh
                        $livewire->dispatch('refreshTable');
                    }),

                Action::make('unverify')
                    ->label('Unverify')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->license_verified)
                    ->action(function ($record, $livewire) {
                        $user = User::find($record->id);

                        if ($user) {
                            $user->update(['license_verified' => false]);
                        }

                        Notification::make()
                            ->title('Doctor license unverified')
                            ->danger()
                            ->send();

                        // ✅ Refresh the table
                        $livewire->dispatch('refreshTable');
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit'   => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
