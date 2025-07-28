<?php

namespace App\Filament\Admin\Resources\GroupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\LessonTemplate;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Alignment;

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';
    protected static ?string $title = 'Zajęcia';
    protected static ?string $modelLabel = 'zajęcia';
    protected static ?string $pluralModelLabel = 'zajęcia';
    protected static ?string $recordTitleAttribute = 'title';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Tytuł')
                    ->required()
                    ->maxLength(255),

                Forms\Components\RichEditor::make('description')
                    ->label('Opis')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'bulletList',
                        'orderedList',
                    ])
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('date')
                    ->label('Data zajęć')
                    ->required()
                    ->default(now()),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Szkic',
                        'published' => 'Opublikowane',
                    ])
                    ->default('draft')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
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
                            // Pierwsza linia: Avatar i tytuł
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
                                ])->extraAttributes(['class' => 'flex-1 min-w-0']),

                                // Pusta kolumna dla zachowania układu
                                Tables\Columns\TextColumn::make('empty')
                                    ->label('')
                                    ->state(''),
                            ])->extraAttributes(['class' => 'items-center min-w-0']),

                            // Druga linia: Data, autor i status
                            Tables\Columns\Layout\Split::make([
                                Tables\Columns\TextColumn::make('empty')
                                    ->label('')
                                    ->state(''),

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
                                        ->state(fn (Model $record): bool => $record->status === 'published'),
                                ])->extraAttributes(['class' => 'gap-x-6 items-center justify-end']),
                            ])->extraAttributes(['class' => 'justify-end']),
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
            ->defaultSort('date', 'desc')
            ->filters([
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
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = Auth::id();
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('publish')
                    ->label('Opublikuj')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Model $record): bool => $record->status === 'draft')
                    ->action(fn (Model $record) => $record->update(['status' => 'published'])),
                Tables\Actions\Action::make('unpublish')
                    ->label('Wycofaj')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Model $record): bool => $record->status === 'published')
                    ->action(fn (Model $record) => $record->update(['status' => 'draft'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Opublikuj zaznaczone')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each(fn ($record) => $record->update(['status' => 'published']))),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Wycofaj zaznaczone')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each(fn ($record) => $record->update(['status' => 'draft']))),
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Dodaj zajęcia')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = Auth::id();
                        return $data;
                    }),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Dodaj pierwsze zajęcia')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = Auth::id();
                        return $data;
                    }),
            ]);
    }
} 