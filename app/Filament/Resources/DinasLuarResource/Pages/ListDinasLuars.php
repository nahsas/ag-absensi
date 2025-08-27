<?php

namespace App\Filament\Resources\DinasLuarResource\Pages;

use App\Filament\Resources\DinasLuarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDinasLuars extends ListRecords
{
    protected static string $resource = DinasLuarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
