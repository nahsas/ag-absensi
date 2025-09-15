<?php

namespace App\Filament\Resources\LembutResource\Pages;

use App\Filament\Resources\LembutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLembut extends EditRecord
{
    protected static string $resource = LembutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
