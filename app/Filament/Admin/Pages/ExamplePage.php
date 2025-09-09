<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use App\Models\Term;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\HtmlEntry;

class ExamplePage extends Page
{
    protected static ?string $navigationLabel = 'Regulamin ';
    protected static ?string $slug = 'regulamin';
    protected static ?string $navigationGroup = 'Regulaminy';
    //protected static ?string $navigationGroup = 'ℹ️ Informacje';
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
                        ->html() // ← to kluczowe: pozwala na wyświetlanie HTML
                        ->markdown()
                        ->state($term->content);
                })->toArray()
            );
    }
}
