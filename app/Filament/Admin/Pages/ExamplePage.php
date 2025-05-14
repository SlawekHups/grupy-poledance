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
    $terms = \App\Models\Term::where('active', true)->get();

    return Infolist::make()
        ->schema(
            $terms->map(function ($term) {
                return TextEntry::make('content_' . $term->id)
                    ->label($term->name ?? 'Regulamin')
                    ->html()
                    ->state($term->content);
            })->toArray()
        );
}
}