<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Audit Logs';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Event Details')
                ->schema([
                    Forms\Components\TextInput::make('action')
                        ->disabled()
                        ->label('Action'),
                    Forms\Components\TextInput::make('outcome')
                        ->disabled()
                        ->label('Outcome'),
                    Forms\Components\TextInput::make('resource_type')
                        ->disabled()
                        ->label('Resource Type'),
                    Forms\Components\TextInput::make('resource_id')
                        ->disabled()
                        ->label('Resource ID'),
                ])->columns(2),

            Forms\Components\Section::make('Actor & Timestamp')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->disabled()
                        ->label('User'),
                    Forms\Components\TextInput::make('created_at')
                        ->disabled()
                        ->label('Timestamp'),
                ])->columns(2),

            Forms\Components\Section::make('Meta Data')
                ->schema([
                    Forms\Components\KeyValue::make('meta')
                        ->disabled()
                        ->label('Additional Data')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->label('Action')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match(true) {
                        str_contains($state, 'failed') => 'danger',
                        str_contains($state, 'success'),
                        str_contains($state, 'created'),
                        str_contains($state, 'synced'),
                        str_contains($state, 'sent')    => 'success',
                        str_contains($state, 'skipped') => 'warning',
                        default                         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->default('System'),

                Tables\Columns\TextColumn::make('resource_type')
                    ->label('Resource')
                    ->sortable(),

                Tables\Columns\TextColumn::make('resource_id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('outcome')
                    ->label('Outcome')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'success' => 'success',
                        'failure' => 'danger',
                        default   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('outcome')
                    ->options([
                        'success' => 'Success',
                        'failure' => 'Failure',
                    ]),

                Tables\Filters\SelectFilter::make('action')
                    ->label('Action')
                    ->options([
                        'submission_created'              => 'Submission Created',
                        'middleware_validation_passed'    => 'Validation Passed',
                        'middleware_validation_failed'    => 'Validation Failed',
                        'middleware_transformation_complete' => 'Transformation Complete',
                        'emr_sync_skipped'                => 'EMR Sync Skipped',
                        'emr_sync_failed'                 => 'EMR Sync Failed',
                        'high_urgency_notification_sent'  => 'Notification Sent',
                        'submission_reviewed'             => 'Submission Reviewed',
                        'submission_responded'            => 'Submission Responded',
                        'dashboard_viewed'                => 'Dashboard Viewed',
                    ]),

                Tables\Filters\Filter::make('failures_only')
                    ->label('Failures Only')
                    ->query(fn($query) => $query->where('outcome', 'failure')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view'  => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}