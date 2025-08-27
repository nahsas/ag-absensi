<?php

namespace App\Filament\Resources\DinasLuarResource\Pages;

use App\Filament\Resources\DinasLuarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDinasLuar extends EditRecord
{
    protected static string $resource = DinasLuarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
