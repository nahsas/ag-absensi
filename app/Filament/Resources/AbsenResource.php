<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Absen;
use Filament\Forms\Form;
use App\Models\SettingJam;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\AbsenResource\Pages;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AbsenResource\RelationManagers;

class AbsenResource extends Resource
{
    protected static ?string $model = Absen::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 1;

 
    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                // Absensi seharusnya dikelola oleh sistem, jadi form ini hanya untuk tampilan admin
                TextInput::make('user.name')
                    ->label('Nama Pengguna')
                    ->disabled(),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->disabled(),
                TextInput::make('point')
                    ->label('Poin')
                    ->numeric()
                    ->disabled(),
                // ... tambahkan field lain jika diperlukan
            ])->columns(1),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query)=>$query->whereIn('keterangan',['hadir','sakit','izin']))
            ->columns([
                
                TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_absen')
                    ->label('Tanggal Absen')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->formatStateUsing(function($record,$state){
                        $waktu = Carbon::parse($record->tanggal_absen);
                        $absen_date = Carbon::parse($record->tanggal_absen)->toDate()->format('Y-m-d');
                        $absen_date_besok = Carbon::parse($record->tanggal_absen)->addDay()->toDate()->format('Y-m-d');

                        $waktuAcuanPagi = "{$absen_date} 06:00:00"; // Asumsi: Acuan tetap jam 8 pagi
                        $waktuAcuanIstirahat = "{$absen_date} 12:00:00"; // Asumsi: Acuan tetap jam 12 siang
                        $waktuAcuanKembali = "{$absen_date} 13:00:01"; // Asumsi: Acuan tetap jam 1 siang
                        $waktuAcuanPulang = "{$absen_date} 17:00:01"; // Asumsi: Acuan tetap jam 5 sore
                        $waktuAcuanBesok = "{$absen_date_besok} 06:00:00"; // Asumsi: Acuan tetap besok jam 8 sore

                        $res = ucfirst($record->keterangan);
                        if($record->keterangan == 'hadir'){
                            switch($waktu){
                                case $waktu->between($waktuAcuanPagi, $waktuAcuanIstirahat):
                                    $res = "Absen Pagi";
                                    break;
                                case $waktu->between($waktuAcuanIstirahat, $waktuAcuanKembali):
                                    $res = "Absen Istirahat";
                                    break;
                                case $waktu->between($waktuAcuanKembali, $waktuAcuanPulang):
                                    $res = "Absen Kembali";
                                    break;
                                case $waktu->between($waktuAcuanPulang, $waktuAcuanBesok):
                                    $res = "Absen Pulang";
                                    break;
                            }
                        }
                        return $res;
                    })
                    ->badge()
                    ->colors([
                        "success"=>"hadir",
                        "info"=>"izin",
                        "primary"=>"sakit",
                        "danger"=>"tanpa_keterangan",
                    ]),
                TextColumn::make('point')
                    ->suffix(' Point')
                    ->sortable()
                    ->color(fn (int $state): string => $state < 0 ? 'danger' : 'success'),
            ])
            ->filters([
                // Sembunyikan data absensi dengan keterangan 'izin' dan 'dinas_luar'
                Filter::make('filter_hadir')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereNotIn('keterangan', ['sakit','izin', 'dinas_luar'])
                    )
                    ->toggle(), // Tambahkan toggle untuk filter
                Filter::make('filter_izin')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('keterangan', 'izin')
                    )
                    ->toggle(), // Tambahkan toggle untuk filter
                Filter::make('filter_sakit')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('keterangan', 'sakit')
                    )
                    ->toggle(), // Tambahkan toggle untuk filter
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsens::route('/'),
            'create' => Pages\CreateAbsen::route('/create'),
            'view' => Pages\ViewAbsen::route('/{record}'),
            'edit' => Pages\EditAbsen::route('/{record}/edit'),
        ];
    }
}
