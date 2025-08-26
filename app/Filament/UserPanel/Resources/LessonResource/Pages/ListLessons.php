<?php

namespace App\Filament\UserPanel\Resources\LessonResource\Pages;

use App\Filament\UserPanel\Resources\LessonResource;
use Filament\Resources\Pages\ListRecords;

class ListLessons extends ListRecords
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
