<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AttendanceResource\Pages;
use App\Filament\Admin\Resources\AttendanceResource\Widgets;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Użytkownicy i Grupy';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Obecność';
    protected static ?string $pluralModelLabel = 'Obecności';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())
            ->where('present', true)
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationColor(): ?string
    {
        return static::getModel()::whereDate('created_at', today())
            ->where('present', true)
            ->exists()
            ? 'success'
            : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('group_id')
                    ->label('Grupa')
                    ->relationship('group', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('user_id')
                            ->label('Użytkownik')
                            ->options(function (callable $get) {
                                $groupId = $get('group_id');
                                if (!$groupId) return [];
                                
                                return \App\Models\User::query()
                                    ->where('group_id', $groupId)
                                    ->whereNot('role', 'admin')
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn (callable $get) => !$get('group_id')),

                        Forms\Components\DatePicker::make('date')
                    ->label('Data zajęć')
                            ->default(now())
                    ->required(),

                        Forms\Components\TextInput::make('note')
                            ->label('Notatka')
                            ->nullable(),
                    ]),

                Forms\Components\Toggle::make('present')
                    ->label('Obecny?')
                    ->default(true)
                    ->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 1,
                'xl' => 1,
            ])
            ->recordClasses('rounded-xl border bg-white shadow-sm hover:shadow-md transition hover:bg-gray-50')
            ->columns([
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\Layout\Split::make([
                            TextColumn::make('date')
                                ->label('Data')
                                ->date('d.m.Y')
                                ->weight('bold')
                                ->sortable(),
                            TextColumn::make('present')
                                ->label('Obecność')
                                ->badge()
                                ->formatStateUsing(fn ($state) => $state ? 'Obecny' : 'Nieobecny')
                                ->color(fn ($state) => $state ? 'success' : 'warning')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-start']),

                        Tables\Columns\Layout\Split::make([
                            TextColumn::make('user.name')
                                ->label('Użytkownik')
                                ->searchable(),
                            TextColumn::make('group.name')
                                ->label('Grupa')
                                ->alignRight()
                                ->searchable(),
                        ])->extraAttributes(['class' => 'justify-between items-center']),

                        TextColumn::make('note')
                            ->label('Notatka')
                            ->wrap()
                            ->extraAttributes(['class' => 'text-sm text-gray-600']),
                    ])->space(2),
                ])->extraAttributes(['class' => 'p-4']),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Od'),
                        Forms\Components\DatePicker::make('to')->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['to'], fn($q, $date) => $q->whereDate('date', '<=', $date));
                    }),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Użytkownik')
                    ->relationship('user', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('group_id')
                    ->label('Grupa')
                    ->relationship('group', 'name')
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('present')
                    ->label('Obecność')
                    ->trueLabel('Obecny')
                    ->falseLabel('Nieobecny'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('toggle_present')
                        ->label(fn ($record) => $record->present ? 'Oznacz jako nieobecny' : 'Oznacz jako obecny')
                        ->icon(fn ($record) => $record->present ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn ($record) => $record->present ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading('Potwierdź zmianę obecności')
                        ->modalDescription(fn ($record) => $record->present ? 'Czy na pewno oznaczyć jako nieobecny?' : 'Czy na pewno oznaczyć jako obecny?')
                        ->action(function ($record) {
                            $record->update(['present' => ! $record->present]);
                        }),
                ])
                    ->button()
                    ->label('Actions')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_present')
                        ->label('Oznacz jako obecny')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Oznaczyć zaznaczone wpisy jako obecne?')
                        ->modalSubmitActionLabel('Oznacz jako obecny')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $updated = 0;
                            foreach ($records as $record) {
                                if (!$record->present) {
                                    $record->update(['present' => true]);
                                    $updated++;
                                }
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Zaktualizowano obecności')
                                ->body("Oznaczono jako obecnych: {$updated}")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('mark_absent')
                        ->label('Oznacz jako nieobecny')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Oznaczyć zaznaczone wpisy jako nieobecne?')
                        ->modalSubmitActionLabel('Oznacz jako nieobecny')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $updated = 0;
                            foreach ($records as $record) {
                                if ($record->present) {
                                    $record->update(['present' => false]);
                                    $updated++;
                                }
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Zaktualizowano obecności')
                                ->body("Oznaczono jako nieobecnych: {$updated}")
                                ->warning()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\AttendanceStats::class,
            Widgets\AttendanceGroupChart::class,
            Widgets\TopAttendersChart::class,
            Widgets\MonthlyTrendChart::class,
        ];
    }
}
