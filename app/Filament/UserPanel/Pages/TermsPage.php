<?php

namespace App\Filament\UserPanel\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use App\Models\Term;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\HtmlEntry;

class TermsPage extends Page
{
    protected static ?string $navigationLabel = 'Regulamin';
    protected static ?string $slug = 'Regulamin';
    protected static ?string $navigationGroup = 'Informacje';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Regulamin użytkownika';
    protected static ?int $navigationSort = 100;

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
