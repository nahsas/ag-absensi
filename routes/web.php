<?php

use App\Http\Controllers\ExportPdfController;
use App\Models\User;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

// Livewire::setUpdateRoute(function ($handle) {
    // return Route::post('/absensi/livewire/update', $handle);
// });

Route::get('/unduh-excel-range', function(Request $request){
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $users = User::with(['absen' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal_absen', [$startDate->startOfDay(), $endDate->endOfDay()]);
        }])->get();

        $exportData = collect();
        $dateRange = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate->addDay());

        foreach ($users as $user) {
            $absensByDate = $user->absen->groupBy(function ($absen) {
                return Carbon::parse($absen->tanggal_absen)->format('Y-m-d');
            });

            $userData = [
                'name' => $user->name,
                'nip' => $user->nip,
                'status_by_date' => collect(),
                'total' => [
                    'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'tanpa_keterangan' => 0, 'dinas_luar' => 0, 'lembur'=>0
                ]
            ];

            foreach ($dateRange as $date) {
                $formattedDate = $date->format('Y-m-d');
                $status = 'libur'; 

                if ($absensByDate->has($formattedDate)) {
                    $record = $absensByDate->get($formattedDate)->first();
                    $status = $record->keterangan;
                    if($status != 'lembur'){
                        $userData['total'][$status]++;
                    }else{
                        $userData['total']['lembur']+=$record->jam_lembur;
                    }
                }
                $userData['status_by_date']->put($formattedDate, $status);
            }
            $exportData->push($userData);
        }

        return Excel::download(
            new UsersExport($exportData, $dateRange, $startDate->format('d F Y'), $endDate->format('d F Y')),
            'rekap-absen.xlsx'
        );
    })->name('unduh-excel-range');

Route::get('/unduh-pdf', [ExportPdfController::class, 'exportPdf'])->name('unduh-pdf');