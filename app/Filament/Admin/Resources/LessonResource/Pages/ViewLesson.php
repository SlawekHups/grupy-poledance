<?php

namespace App\Filament\Admin\Resources\LessonResource\Pages;

use App\Filament\Admin\Resources\LessonResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewLesson extends ViewRecord
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
            Action::make('back')
                ->label('Powrót')
                ->color('info')
                ->url(route('filament.admin.resources.lessons.index')),
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('close')
                ->label('Zamknij')
                ->color('success')
                ->url(route('filament.admin.resources.lessons.index')),
        ];
    }
}
