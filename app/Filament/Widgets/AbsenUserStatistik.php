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
            ->query(fn() => User::query())
            ->modifyQueryUsing(function (Builder $query) use (
                $waktuMulaiPagi, $waktuTutupPagi, $waktuAcuanPagi,
                $waktuMulaiIstirahat, $waktuTutupIstirahat, $waktuAcuanIstirahat,
                $waktuMulaiKembali, $waktuTutupKembali, $waktuAcuanKembali,
                $waktuMulaiPulang, $waktuTutupPulang, $waktuAcuanPulang
            ) {
                $query
                    ->join('absens', 'users.id', '=', 'absens.user_id')
                    ->whereDate('absens.tanggal_absen', now()->toDateString())
                    ->whereIn('absens.keterangan', ['hadir','tanpa_keterangan'])
                    ->select([
                        'users.id',
                        'users.name',
                        // Absen Pagi: Status & Keterangan (MySQL)
                        DB::raw('MAX(CASE
                            WHEN TIME(absens.tanggal_absen) BETWEEN \''.$waktuMulaiPagi.'\' AND \''.$waktuTutupPagi.'\' THEN
                                CASE
                                    WHEN TIME(absens.tanggal_absen) > \''.$waktuAcuanPagi.'\'
                                    THEN CONCAT(\'✓ (\', ROUND(TIMESTAMPDIFF(MINUTE, CONCAT(DATE(absens.tanggal_absen), \' \', \''.$waktuAcuanPagi.'\'), absens.tanggal_absen)), \' menit telat)\')
                                    ELSE CONCAT(\'✓ (\', ROUND(TIMESTAMPDIFF(MINUTE, absens.tanggal_absen, CONCAT(DATE(absens.tanggal_absen), \' \', \''.$waktuAcuanPagi.'\'))), \' menit lebih cepat)\')
                                END
                                WHEN absens.keterangan = \'tanpa_keterangan\' THEN CONCAT(\'Alpha\')
                            ELSE NULL
                        END) AS status_pagi'),
                        // Absen Istirahat: Status & Keterangan (MySQL)
                        DB::raw('MAX(CASE
                            WHEN TIME(absens.tanggal_absen) BETWEEN \''.$waktuMulaiIstirahat.'\' AND \''.$waktuTutupIstirahat.'\' THEN
                                CASE
                                    WHEN TIME(absens.tanggal_absen) > \''.$waktuAcuanIstirahat.'\'
                                    THEN CONCAT(\'✓ (\', ROUND(TIMESTAMPDIFF(MINUTE, CONCAT(DATE(absens.tanggal_absen), \' \', \''.$waktuAcuanIstirahat.'\'), absens.tanggal_absen)), \' menit telat)\')
                                    ELSE CONCAT(\'✓ (\', ROUND(TIMESTAMPDIFF(MINUTE, absens.tanggal_absen, CONCAT(DATE(absens.tanggal_absen), \' \', \''.$waktuAcuanIstirahat.'\'))), \' menit lebih cepat)\')
                                END
                                WHEN absens.keterangan = \'tanpa_keterangan\' THEN CONCAT(\'Alpha\')
                            ELSE NULL
                        END) AS status_istirahat'),
                        // Absen Kembali Istirahat: Status & Keterangan (MySQL)
                        DB::raw('MAX(CASE
                            WHEN TIME(absens.tanggal_absen) BETWEEN \''.$waktuMulaiKembali.'\' AND \''.$waktuTutupKembali.'\' THEN
                                CASE
                                    WHEN TIME(absens.tanggal_absen) > \''.$waktuAcuanKembali.'\'
                                    THEN CONCAT(\'✓ (\', ROUND(TIMESTAMPDIFF(MINUTE, CONCAT(DATE(absens.tanggal_absen), \' \', \''.$waktuAcuanKembali.'\'), absens.tanggal_absen)), \' menit telat)\')
                                    ELSE CONCAT(\'✓ (\', ROUND(TIMESTAMPDIFF(MINUTE, absens.tanggal_absen, CONCAT(DATE(absens.tanggal_absen), \' \', \''.$waktuAcuanKembali.'\'))), \' menit lebih cepat)\')
                                END
                                WHEN absens.keterangan = \'tanpa_keterangan\' THEN CONCAT(\'Alpha\')
                            ELSE NULL
                        END) AS status_kembali_istirahat'),
                        // Absen Pulang: Status & Keterangan (MySQL)
                        DB::raw('MAX(CASE
                            WHEN TIME(absens.tanggal_absen) BETWEEN \''.$waktuMulaiPulang.'\' AND \''.$waktuTutupPulang.'\' THEN
                                CASE
                                    WHEN TIME(absens.tanggal_absen) > \''.$waktuAcuanPulang.'\'
                                    THEN CONCAT(\'✓ (\', ROUND(TIMESTAMPDIFF(MINUTE, CONCAT(DATE(absens.tanggal_absen), \' \', \''.$waktuAcuanPulang.'\'), absens.tanggal_absen)), \' menit telat)\')
                                    ELSE CONCAT(\'✓ (\', ROUND(TIMESTAMPDIFF(MINUTE, absens.tanggal_absen, CONCAT(DATE(absens.tanggal_absen), \' \', \''.$waktuAcuanPulang.'\'))), \' menit lebih cepat)\')
                                END
                                WHEN absens.keterangan = \'tanpa_keterangan\' THEN CONCAT(\'Alpha\')
                            ELSE NULL
                        END) AS status_pulang'),
                    ])
                    ->groupBy('users.id', 'users.name');
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Pengguna'),
                Tables\Columns\TextColumn::make('status_pagi')->label('Absen Pagi')
                    ->badge()
                    ->colors([
                        "danger"=>"Alpha"
                    ])
                    ->grow(false),
                Tables\Columns\TextColumn::make('status_istirahat')->label('Istirahat')
                    ->badge()
                    ->colors([
                        "danger"=>"Alpha"
                    ])
                    ->grow(false),
                Tables\Columns\TextColumn::make('status_kembali_istirahat')->label('Kembali')
                    ->badge()
                    ->colors([
                        "danger"=>"Alpha"
                    ])
                    ->grow(false),
                Tables\Columns\TextColumn::make('status_pulang')->label('Pulang')
                    ->badge()
                    ->colors([
                        "danger"=>"Alpha"
                    ])
                    ->grow(false),
            ]);
    }
}
