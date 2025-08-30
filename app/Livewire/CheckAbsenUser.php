<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Tables;
use App\Models\Absen;
use App\Models\SettingJam;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Relations\Relation;

class CheckAbsenUser extends BaseWidget
{
    protected static ?string $model = Absen::class; // Perbaiki: Gunakan properti statis
    public array|string|int $columnSpan = 'full';

    public function table(Table $table): Table
    {
        // Ambil data jam dari tabel setting_jams
        $pagi = SettingJam::where('nama_jam', 'Jam masuk')->first();
        $istirahat = SettingJam::where('nama_jam', 'Istirahat')->first();
        $kembali = SettingJam::where('nama_jam', 'Masuk kembali')->first();
        $pulang = SettingJam::where('nama_jam', 'Pulang')->first();

        // Pastikan nilai-nilai tersedia, berikan default jika tidak
        $waktuMulaiPagi = $pagi->jam ?? '07:00:00';
        $waktuTutupPagi = $pagi->batas_jam ?? '11:59:59';
        $waktuAcuanPagi = '08:00:00'; // Asumsi: Acuan tetap jam 8 pagi

        $waktuMulaiIstirahat = $istirahat->jam ?? '12:00:00';
        $waktuTutupIstirahat = $istirahat->batas_jam ?? '13:00:00';
        $waktuAcuanIstirahat = '12:00:00'; // Asumsi: Acuan tetap jam 12 siang

        $waktuMulaiKembali = $kembali->jam ?? '13:00:01';
        $waktuTutupKembali = $kembali->batas_jam ?? '17:00:00';
        $waktuAcuanKembali = '13:00:01'; // Asumsi: Acuan tetap jam 1 siang

        $waktuMulaiPulang = $pulang->jam ?? '17:00:01';
        $waktuTutupPulang = $pulang->batas_jam ?? '23:59:59';
        $waktuAcuanPulang = '17:00:01'; // Asumsi: Acuan tetap jam 5 sore


        return $table
            ->query(fn()=>User::query())
            ->modifyQueryUsing(function (Builder $query) use (
                $waktuMulaiPagi, $waktuTutupPagi, $waktuAcuanPagi,
                $waktuMulaiIstirahat, $waktuTutupIstirahat, $waktuAcuanIstirahat,
                $waktuMulaiKembali, $waktuTutupKembali, $waktuAcuanKembali,
                $waktuMulaiPulang, $waktuTutupPulang, $waktuAcuanPulang
            ) {
                $query
                    ->join('absens', 'users.id', '=', 'absens.user_id')
                    ->whereDate('absens.tanggal_absen', now()->toDateString())
                    ->whereIn('absens.keterangan',['hadir','tanpa_keterangan'])
                    ->select([
                        'users.id',
                        'users.name',
                        // Absen Pagi: Status & Keterangan
                        DB::raw('MAX(CASE 
                            WHEN CAST(absens.tanggal_absen AS time) BETWEEN \''.$waktuMulaiPagi.'\' AND \''.$waktuTutupPagi.'\' THEN 
                                CASE 
                                    WHEN CAST(absens.tanggal_absen AS time) > \''.$waktuAcuanPagi.'\' 
                                    THEN \'✓ (\' || ROUND(EXTRACT(EPOCH FROM (absens.tanggal_absen - (absens.tanggal_absen::date + \''.$waktuAcuanPagi.'\'::time))) / 60) || \' menit telat)\'
                                    ELSE \'✓ (\' || ROUND(EXTRACT(EPOCH FROM ((absens.tanggal_absen::date + \''.$waktuAcuanPagi.'\'::time) - absens.tanggal_absen)) / 60) || \' menit lebih cepat)\'
                                END
                            ELSE NULL 
                        END) AS status_pagi'),
                        // Absen Istirahat: Status & Keterangan
                        DB::raw('MAX(CASE
                            WHEN CAST(absens.tanggal_absen AS time) BETWEEN \''.$waktuMulaiIstirahat.'\' AND \''.$waktuTutupIstirahat.'\' THEN
                                CASE
                                    WHEN CAST(absens.tanggal_absen AS time) > \''.$waktuAcuanIstirahat.'\'
                                    THEN \'✓ (\' || ROUND(EXTRACT(EPOCH FROM (absens.tanggal_absen - (absens.tanggal_absen::date + \''.$waktuAcuanIstirahat.'\'::time))) / 60) || \' menit telat)\'
                                    ELSE \'✓ (\' || ROUND(EXTRACT(EPOCH FROM ((absens.tanggal_absen::date + \''.$waktuAcuanIstirahat.'\'::time) - absens.tanggal_absen)) / 60) || \' menit lebih cepat)\'
                                END
                            ELSE NULL
                        END) AS status_istirahat'),
                        // Absen Kembali Istirahat: Status & Keterangan
                        DB::raw('MAX(CASE
                            WHEN CAST(absens.tanggal_absen AS time) BETWEEN \''.$waktuMulaiKembali.'\' AND \''.$waktuTutupKembali.'\' THEN
                                CASE
                                    WHEN CAST(absens.tanggal_absen AS time) > \''.$waktuAcuanKembali.'\'
                                    THEN \'✓ (\' || ROUND(EXTRACT(EPOCH FROM (absens.tanggal_absen - (absens.tanggal_absen::date + \''.$waktuAcuanKembali.'\'::time))) / 60) || \' menit telat)\'
                                    ELSE \'✓ (\' || ROUND(EXTRACT(EPOCH FROM ((absens.tanggal_absen::date + \''.$waktuAcuanKembali.'\'::time) - absens.tanggal_absen)) / 60) || \' menit lebih cepat)\'
                                END
                            ELSE NULL
                        END) AS status_kembali_istirahat'),
                        // Absen Pulang: Status & Keterangan
                        DB::raw('MAX(CASE
                            WHEN CAST(absens.tanggal_absen AS time) BETWEEN \''.$waktuMulaiPulang.'\' AND \''.$waktuTutupPulang.'\' THEN
                                CASE
                                    WHEN CAST(absens.tanggal_absen AS time) > \''.$waktuAcuanPulang.'\'
                                    THEN \'✓ (\' || ROUND(EXTRACT(EPOCH FROM (absens.tanggal_absen - (absens.tanggal_absen::date + \''.$waktuAcuanPulang.'\'::time))) / 60) || \' menit telat)\'
                                    ELSE \'✓ (\' || ROUND(EXTRACT(EPOCH FROM ((absens.tanggal_absen::date + \''.$waktuAcuanPulang.'\'::time) - absens.tanggal_absen)) / 60) || \' menit lebih cepat)\'
                                END
                            ELSE NULL
                        END) AS status_pulang'),
                    ])
                    ->groupBy('users.id', 'users.name');
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Pengguna'),
                Tables\Columns\TextColumn::make('status_pagi')->label('Absen Pagi'),
                Tables\Columns\TextColumn::make('status_istirahat')->label('Istirahat'),
                Tables\Columns\TextColumn::make('status_kembali_istirahat')->label('Kembali'),
                Tables\Columns\TextColumn::make('status_pulang')->label('Pulang'),
            ]);
    }
}
