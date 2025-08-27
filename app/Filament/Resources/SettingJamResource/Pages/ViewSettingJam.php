<?php

namespace App\Filament\Resources\SettingJamResource\Pages;

use App\Filament\Resources\SettingJamResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSettingJam extends ViewRecord
{
    protected static string $resource = SettingJamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
