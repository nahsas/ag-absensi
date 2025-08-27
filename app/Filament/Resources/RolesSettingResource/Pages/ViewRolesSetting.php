<?php

namespace App\Filament\Resources\RolesSettingResource\Pages;

use App\Filament\Resources\RolesSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRolesSetting extends ViewRecord
{
    protected static string $resource = RolesSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
