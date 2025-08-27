<?php

namespace App\Filament\Resources\AbsenResource\Pages;

use App\Filament\Resources\AbsenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbsens extends ListRecords
{
    protected static string $resource = AbsenResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
