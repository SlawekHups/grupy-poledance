<?php

namespace App\Filament\UserPanel\Resources\UserMailMessageResource\Pages;

use App\Filament\UserPanel\Resources\UserMailMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListUserMailMessages extends ListRecords
{
    protected static string $resource = UserMailMessageResource::class;
} 