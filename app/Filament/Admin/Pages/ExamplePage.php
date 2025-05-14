<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use App\Models\Term;
use Illuminate\Database\Eloquent\Model;

class ExamplePage extends Page
{
    protected static ?string $navigationLabel = 'Przykład';
    protected static ?string $slug = 'Regulamin';
    protected static ?string $navigationGroup = 'Informacje';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.example-page';

    public function getInfolist(string $name): ?Infolist
    {
        $record = Term::find(1) ?? new class(['content' => 'Brak treści regulaminu.']) extends Model {
            protected $guarded = [];
            public $timestamps = false;
        };

        return Infolist::make()
            ->record($record)
            ->schema([
                TextEntry::make('content')->label(false),
            ]);
    }
}