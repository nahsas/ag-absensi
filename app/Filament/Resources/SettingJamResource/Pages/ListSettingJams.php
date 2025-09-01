<?php

namespace App\Filament\Resources\SettingJamResource\Pages;

use App\Filament\Resources\SettingJamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSettingJams extends ListRecords
{
    protected static string $resource = SettingJamResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
