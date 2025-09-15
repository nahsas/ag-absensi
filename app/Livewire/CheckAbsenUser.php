<?php


namespace App\Livewire;

use Filament\Tables;
use App\Models\Absen;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class CheckAbsenUser extends BaseWidget
{
    public array|string|int $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Absen::query()
                ->whereDate('tanggal_absen', now()->toDateString())
                ->with('user')
            )
            ->modifyQueryUsing(function($query){
                return $query
                    ->select([
                        DB::raw('tanggal_absen as tanggal')
                    ]);
            })
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Pengguna')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->badge()
                    ->enum([
                        'hadir' => 'Hadir',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'dinas_luar' => 'Dinas Luar',
                        'tanpa_keterangan' => 'Tanpa Keterangan',
                    ])
                    ->colors([
                        'success' => 'hadir',
                        'warning' => 'sakit',
                        'info' => 'izin',
                        'primary' => 'dinas_luar',
                        'danger' => 'tanpa_keterangan',
                    ]),
                TextColumn::make('tanggal')
                    ->label('Waktu Absen')
                    ->dateTime('H:i')
                    ->sortable(),
            ]);
    }
}