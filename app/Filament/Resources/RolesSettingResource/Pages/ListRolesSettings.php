<?php

namespace App\Filament\Resources\RolesSettingResource\Pages;

use App\Filament\Resources\RolesSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRolesSettings extends ListRecords
{
    protected static string $resource = RolesSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
