<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TermResource\Pages;
use App\Models\Term;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Ustawienia';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Tytuł')
                    ->required(),
                MarkdownEditor::make('content')
                    ->label('Treść regulaminu')
                    ->required()
                    ->columnSpan('full'),
                Toggle::make('active')
                    ->label('Aktywny')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable(),
                TextColumn::make('name')->searchable(),
                BooleanColumn::make('active')->label('Aktywny'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Podgląd')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (Term $record): string => "Podgląd: {$record->name}")
                    ->modalContent(fn (Term $record) => view('filament.modals.term-preview', [
                        'term' => $record
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Zamknij')
                    ->modalWidth('4xl'),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTerms::route('/'),
            'create' => Pages\CreateTerm::route('/create'),
            'edit' => Pages\EditTerm::route('/{record}/edit'),
        ];
    }
    public static function getModelLabel(): string
    {
        return 'Dokument';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Dokumenty';
    }
}
