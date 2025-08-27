<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['no_hp'] = preg_replace('/[^0-9]/', '', $data['no_hp']);

        $data['no_hp'] = Str::startsWith($data['no_hp'], '0') ? substr($data['no_hp'], 1) : $data['no_hp'];
        $data['no_hp'] = Str::startsWith($data['no_hp'], '62') ? substr($data['no_hp'], 2) : $data['no_hp'];

        $data['no_hp'] = preg_replace('/(\d{3})(\d{4})(\d{4})/', '$1 $2 $3', $data['no_hp']);

        $data['no_hp'] = '+62 '.$data['no_hp'];
        return $data;
    }
}
