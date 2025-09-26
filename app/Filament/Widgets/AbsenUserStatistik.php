<?php

namespace App\Filament\Widgets;


use DateTime;
use App\Models\User;
use Filament\Tables;
use App\Models\Absen;
use App\Models\Setting;
use App\Models\SettingJam;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Database\Eloquent\Builder;

class AbsenUserStatistik extends BaseWidget
{
protected static ?string $model = Absen::class;
    public array|string|int $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $pagi = SettingJam::where('nama_jam', 'Jam masuk')->first();
        $istirahat = SettingJam::where('nama_jam', 'Istirahat')->first();
        $kembali = SettingJam::where('nama_jam', 'Masuk kembali')->first();
        $pulang = SettingJam::where('nama_jam', 'Pulang')->first();

        $waktuMulaiPagi = $pagi->jam ?? '07:00:00';
        $waktuTutupPagi = $pagi->batas_jam ?? '11:59:59';
        $waktuAcuanPagi = Setting::where('name','jam_pagi_relatif')->first()->value ?? '08:00:00';

        $waktuMulaiIstirahat = $istirahat->jam ?? '12:00:00';
        $waktuTutupIstirahat = $istirahat->batas_jam ?? '13:00:00';
        $waktuAcuanIstirahat = Setting::where('name','jam_istirahat_relatif')->first()->value ?? '12:00:00';

        $waktuMulaiKembali = $kembali->jam ?? '13:00:01';
        $waktuTutupKembali = $kembali->batas_jam ?? '17:00:00';
        $waktuAcuanKembali = Setting::where('name','jam_kembali_relatif')->first()->value ?? '13:00:00';

        $waktuMulaiPulang = $pulang->jam ?? '17:00:01';
        $waktuTutupPulang = $pulang->batas_jam ?? '23:59:59';
        $waktuAcuanPulang = Setting::where('name','jam_pulang_relatif')->first()->value ?? '17:00:00';

        return $table
            ->query(fn() => Absen::query())
            ->modifyQueryUsing(fn($query)=>$query->whereIn('keterangan',['tanpa_keterangan','hadir'])->where([['created_at','>=',now()->startOfDay()],['created_at','<=',now()->endOfDay()]]))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->date()->label('Tanggal'),
                Tables\Columns\TextColumn::make('user.name')->label('Nama Pengguna'),
                Tables\Columns\TextColumn::make('keterangan')->label('Keterangan')
                    ->formatStateUsing(fn($state)=>ucfirst(str_replace('_',' ',$state)))
                    ->badge()
                    ->color(function($state){
                        switch ($state){
                            case 'hadir':
                                return 'success';
                            case 'izin':
                                return 'warning';
                            case 'keluar_kantor':
                                return 'warning';
                            case 'dinas_luar':
                                return 'primary';
                            case 'tanpa_keterangan':
                                return 'danger';
                        }
                    }),
                Tables\Columns\TextColumn::make('pagi')->label('Absen Pagi')
                    ->badge()
                    ->time()
                    ->colors([
                        "danger"=>"Alpha"
                    ])
                    ->grow(false),
                Tables\Columns\TextColumn::make('istirahat')->label('Istirahat')
                    ->badge()
                    ->time()
                    ->colors([
                        "danger"=>"Alpha"
                    ])
                    ->grow(false),
                Tables\Columns\TextColumn::make('kembali_kerja')->label('Kembali')
                    ->badge()
                    ->time()
                    ->colors([
                        "danger"=>"Alpha"
                    ])
                    ->grow(false),
                Tables\Columns\TextColumn::make('pulang')->label('Pulang')
                    ->badge()
                    ->time()
                    ->colors([
                        "danger"=>"Alpha"
                    ])
                    ->grow(false),
            ]);
    }
}
