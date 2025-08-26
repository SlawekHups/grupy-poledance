<?php

namespace App\Filament\UserPanel\Resources\LessonResource\Pages;

use App\Filament\UserPanel\Resources\LessonResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewLesson extends ViewRecord
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Powrót')
                ->icon('heroicon-o-arrow-left')
                ->color('primary')
                ->url(fn () => route('filament.user.resources.lessons.index')),
        ];
    }

    protected function getFooterActions(): array
    {
        return [
            Action::make('close')
                ->label('Zamknij')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->url(fn () => route('filament.user.resources.lessons.index')),
        ];
    }
}
