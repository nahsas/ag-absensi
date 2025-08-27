<?php

namespace App\Filament\Resources\SettingJamResource\Pages;

use App\Filament\Resources\SettingJamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSettingJam extends EditRecord
{
    protected static string $resource = SettingJamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
