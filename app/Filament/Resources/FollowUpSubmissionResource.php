<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FollowUpSubmissionResource\Pages;
use App\Models\FollowUpSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FollowUpSubmissionResource extends Resource
{
    protected static ?string $model = FollowUpSubmission::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Follow-Up Submissions';
    protected static ?string $navigationGroup = 'Clinical';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereNull('reviewed_at')
            ->where('urgency_level', 'High')
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Patient Information')
                ->schema([
                    Forms\Components\Select::make('patient_id')
                        ->relationship('patient', 'name')
                        ->disabled()
                        ->label('Patient'),
                    Forms\Components\Select::make('doctor_id')
                        ->relationship('doctor', 'name')
                        ->disabled()
                        ->label('Assigned Doctor'),
                ])->columns(2),

            Forms\Components\Section::make('Submission Details')
                ->schema([
                    Forms\Components\CheckboxList::make('symptom_categories')
                        ->options([
                            'fever'                  => 'Fever',
                            'pain'                   => 'Pain',
                            'swelling'               => 'Swelling',
                            'medication_side_effect' => 'Medication Side Effect',
                            'wound_concern'          => 'Wound Concern',
                            'general_deterioration'  => 'General Deterioration',
                        ])
                        ->disabled()
                        ->label('Symptoms'),
                    Forms\Components\TextInput::make('severity')
                        ->disabled()
                        ->label('Severity (1-5)'),
                    Forms\Components\TextInput::make('recovery_status')
                        ->disabled()
                        ->label('Recovery Status'),
                    Forms\Components\Textarea::make('notes')
                        ->disabled()
                        ->label('Patient Notes')
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Triage & Sync')
                ->schema([
                    Forms\Components\TextInput::make('urgency_level')
                        ->disabled()
                        ->label('Urgency Level'),
                    Forms\Components\TextInput::make('sync_status')
                        ->disabled()
                        ->label('EMR Sync Status'),
                    Forms\Components\Textarea::make('doctor_response')
                        ->disabled()
                        ->label('Doctor Response')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Doctor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('symptom_categories')
                    ->label('Symptoms')
                    ->formatStateUsing(fn($state) =>
                        is_array($state)
                            ? implode(', ', array_map(
                                fn($s) => ucwords(str_replace('_', ' ', $s)),
                                $state
                            ))
                            : $state
                    ),

                Tables\Columns\TextColumn::make('severity')
                    ->label('Severity')
                    ->sortable(),

                Tables\Columns\TextColumn::make('recovery_status')
                    ->label('Recovery')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'Improving' => 'success',
                        'Stable'    => 'info',
                        'Uncertain' => 'warning',
                        'Worsening' => 'danger',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('urgency_level')
                    ->label('Urgency')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'High'   => 'danger',
                        'Medium' => 'warning',
                        'Low'    => 'success',
                        default  => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('sync_status')
                    ->label('EMR Sync')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'Synced'  => 'success',
                        'Pending' => 'warning',
                        'Failed'  => 'danger',
                        default   => 'gray',
                    }),

                Tables\Columns\IconColumn::make('reviewed_at')
                    ->label('Reviewed')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('urgency_level')
                    ->label('Urgency')
                    ->options([
                        'High'   => 'High',
                        'Medium' => 'Medium',
                        'Low'    => 'Low',
                    ]),

                Tables\Filters\SelectFilter::make('sync_status')
                    ->label('Sync Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Synced'  => 'Synced',
                        'Failed'  => 'Failed',
                    ]),

                Tables\Filters\Filter::make('unreviewed')
                    ->label('Unreviewed Only')
                    ->query(fn($query) => $query->whereNull('reviewed_at')),

                Tables\Filters\Filter::make('high_urgency')
                    ->label('High Urgency Only')
                    ->query(fn($query) => $query->where('urgency_level', 'High')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('middleware_trace')
                    ->label('Pipeline Trace')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('warning')
                    ->url(fn(FollowUpSubmission $record): string =>
                        route('middleware.trace', ['submissionId' => $record->id])
                    )
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFollowUpSubmissions::route('/'),
            'view'  => Pages\ViewFollowUpSubmission::route('/{record}'),
        ];
    }
}