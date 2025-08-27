<?php

namespace App\Filament\Resources\RolesSettingResource\Pages;

use App\Filament\Resources\RolesSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRolesSetting extends EditRecord
{
    protected static string $resource = RolesSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
