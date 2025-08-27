<?php

use App\Exports\UsersExport;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

Route::get("/laporan/absen/unduh", function(){
    $bulan_awal = Carbon::today()->startOfMonth();
    $bulan_akhir = Carbon::today()->endOfMonth();
    Excel::store(new UsersExport, public_path().'laporan_absen_'.$bulan_awal.'-'.$bulan_akhir.'.xlsx');
    Notification::make()
        ->success()
        ->title("Absen Berhasil Di Export")
        ->body('laporan_absen_'.$bulan_awal.'-'.$bulan_akhir.'.xlsx')
        ->send();
    return Storage::download(public_path().'laporan_absen_'.$bulan_awal.'-'.$bulan_akhir.'.xlsx');
})->name('unduh-excel');