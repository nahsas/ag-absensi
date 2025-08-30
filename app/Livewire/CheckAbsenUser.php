<?php

namespace App\Livewire;

use Filament\Tables;
use App\Models\Absen;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Relations\Relation;

class CheckAbsenUser extends BaseWidget
{
    protected static ?string $model = Absen::class; // Perbaiki: Gunakan properti statis

    public function table(Table $table): Table
    {
        return $table
            // ->query(fn()=>Absen::sort)
            ->columns(components: [
                // ...
            ]);
    }
}
