<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LessonResource\Pages;
use App\Models\Lesson;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\IconPosition;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $modelLabel = 'zadanie';
    protected static ?string $pluralModelLabel = 'zadania';
    protected static ?string $navigationGroup = 'Zajęcia';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('date', today())
            ->where('status', 'published')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getNavigationColor(): ?string
    {
        return static::getModel()::whereDate('date', today())
            ->where('status', 'published')
            ->exists()
            ? 'info'
            : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('group_id')
                    ->label('Grupa')
                    ->options(Group::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->hidden(fn ($livewire) => $livewire instanceof \App\Filament\Admin\Resources\LessonResource\Pages\ViewLesson),

                Forms\Components\TextInput::make('title')
                    ->label('Tytuł')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->disabled(fn ($livewire) => $livewire instanceof \App\Filament\Admin\Resources\LessonResource\Pages\ViewLesson),

                Forms\Components\DatePicker::make('date')
                    ->label('Data zajęć')
                    ->required()
                    ->default(now())
                    ->hidden(fn ($livewire) => $livewire instanceof \App\Filament\Admin\Resources\LessonResource\Pages\ViewLesson),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Szkic',
                        'published' => 'Opublikowane',
                    ])
                    ->default('draft')
                    ->required()
                    ->hidden(fn ($livewire) => $livewire instanceof \App\Filament\Admin\Resources\LessonResource\Pages\ViewLesson),

                Forms\Components\RichEditor::make('description')
                    ->label('Opis')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'bulletList',
                        'orderedList',
                    ])
                    ->columnSpanFull()
                    ->disabled(fn ($livewire) => $livewire instanceof \App\Filament\Admin\Resources\LessonResource\Pages\ViewLesson),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 1,
            ])
            ->recordClasses(function ($record) {
                $classes = ['rounded-xl shadow-md hover:shadow-xl transition-shadow duration-200'];
                if ($record->status === 'draft') {
                    $classes[] = 'opacity-70';
                }
                return implode(' ', $classes);
            })
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    // Nagłówek
                    Tables\Columns\Layout\Panel::make([
                        Tables\Columns\Layout\Stack::make([
                            // Pierwsza linia: Avatar, tytuł i grupa
                            Tables\Columns\Layout\Split::make([
                                Tables\Columns\Layout\Stack::make([
                                    Tables\Columns\Layout\Split::make([
                                        Tables\Columns\ImageColumn::make('avatar')
                                            ->label('')
                                            ->circular()
                                            ->size(60),
                                        
                                        Tables\Columns\TextColumn::make('title')
                                            ->label('')
                                            ->searchable()
                                            ->sortable()
                                            ->weight('bold')
                                            ->size('2xl')
                                            ->extraAttributes([
                                                'class' => 'text-primary-600 whitespace-nowrap overflow-hidden text-ellipsis',
                                                'style' => 'min-width: 0;'
                                            ]),
                                    ])->extraAttributes(['class' => 'items-center gap-x-3 min-w-0 flex-1']),

                                    Tables\Columns\TextColumn::make('group.name')
                                        ->label('Grupa')
                                        ->icon('heroicon-m-user-group')
                                        ->iconPosition(IconPosition::Before)
                                        ->extraAttributes(['class' => 'text-gray-600']),
                                ])->extraAttributes(['class' => 'flex-1 min-w-0']),

                                // Druga linia: Data, autor i status
                                Tables\Columns\Layout\Split::make([
                                    Tables\Columns\TextColumn::make('date')
                                        ->label('')
                                        ->date('d.m.Y')
                                        ->icon('heroicon-m-calendar')
                                        ->iconPosition(IconPosition::Before)
                                        ->extraAttributes(['class' => 'text-gray-600']),

                                    Tables\Columns\TextColumn::make('creator.name')
                                        ->label('')
                                        ->icon('heroicon-m-user')
                                        ->iconPosition(IconPosition::Before)
                                        ->extraAttributes(['class' => 'text-gray-600']),

                                    Tables\Columns\IconColumn::make('status')
                                        ->label('')
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-circle')
                                        ->falseIcon('heroicon-o-x-circle')
                                        ->trueColor('success')
                                        ->falseColor('danger')
                                        ->state(fn ($record): bool => $record->status === 'published'),
                                ])->extraAttributes(['class' => 'gap-x-6 items-center justify-end']),
                            ])->extraAttributes(['class' => 'justify-between']),
                        ])->space(3),
                    ])->extraAttributes(['class' => 'bg-gray-50 p-4 rounded-t-xl']),

                    // Opis
                    Tables\Columns\Layout\Panel::make([
                        Tables\Columns\TextColumn::make('description')
                            ->label('')
                            ->html()
                            ->words(250)
                            ->wrap()
                            ->extraAttributes([
                                'class' => 'prose max-w-none p-6',
                                'style' => 'word-wrap: break-word; white-space: pre-wrap;'
                            ]),
                    ])->extraAttributes(['class' => 'bg-white rounded-b-xl']),
                ]),
            ])
            ->defaultSort('date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('group_id')
                    ->label('Grupa')
                    ->options(Group::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Szkic',
                        'published' => 'Opublikowane',
                    ]),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Od'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Podgląd')
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('publish')
                    ->label('Opublikuj')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record): bool => $record->status === 'draft')
                    ->action(fn ($record) => $record->update(['status' => 'published'])),
                Tables\Actions\Action::make('unpublish')
                    ->label('Wycofaj')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record): bool => $record->status === 'published')
                    ->action(fn ($record) => $record->update(['status' => 'draft'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Opublikuj zaznaczone')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['status' => 'published']))),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Wycofaj zaznaczone')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['status' => 'draft']))),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessons::route('/'),
            'create' => Pages\CreateLesson::route('/create'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
            'view' => Pages\ViewLesson::route('/{record}'),
        ];
    }
} 