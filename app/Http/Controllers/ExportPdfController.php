<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Filament\Notifications\Notification;

class ExportPdfController extends Controller
{
    public function exportPdf(Request $request)
    {
        // Bagian ini sama: Mendapatkan rentang tanggal dari form dan memproses data
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        if ($endDate < $startDate)
        {
            Notification::make('Absen tidak valid')
                ->send()
                ->danger()
                ->title('Tanggal Absen Tidak Valid')
                ->body('Pastikan tanggal mulai lebih kecil dari batas tanggal')
                ->send();
            return redirect()->route('filament.admin.pages.dashboard');
        }
        
        $users = User::with(['absen' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        }])->get();

        $exportData = collect();
        $dateRange = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate->addDay());

        foreach ($users as $user) {
            $absensByDate = $user->absen->groupBy(function ($absen) {
                return Carbon::parse($absen->created_at)->format('Y-m-d');
            });

            $userData = [
                'name' => $user->name,
                'nip' => $user->nip,
                'status_by_date' => collect(),
                'total' => [
                    'hadir' => 0, 'izin' => 0, 'keluar_kantor' => 0, 'tanpa_keterangan' => 0, 'dinas_luar' => 0, 'lembur' => 0
                ]
            ];

            foreach ($dateRange as $date) {
                $formattedDate = $date->format('Y-m-d');
                $status = 'libur'; 

                if ($absensByDate->has($formattedDate)) {
                    $record = $absensByDate->get($formattedDate)->first();
                    $status = $record->keterangan;
                    if($record->selesai_lembur == null){
                        $userData['total'][$status]++;
                    }else{
                        $userData['total']['lembur'] += $record->jam_lembur;
                    }
                }
                $userData['status_by_date']->put($formattedDate, $status);
            }
            $exportData->push($userData);
        }

        // --- Bagian ini yang berubah total untuk penggunaan Dompdf langsung ---
        
        // 1. Render Blade view menjadi string HTML
        $html = view('pdf.rekap_absen', [
            'users' => $exportData,
            'dateRange' => $dateRange,
            'startDate' => $startDate->format('d F Y'),
            'endDate' => $endDate->format('d F Y')
        ])->render();

        // 2. Buat instance Dompdf baru
        $dompdf = new Dompdf();

        // 3. Muat HTML ke Dompdf
        $dompdf->loadHtml($html);

        // 4. (Opsional) Atur ukuran kertas
        $dompdf->setPaper('A4', 'landscape');

        // 5. Render HTML menjadi PDF
        $dompdf->render();

        // 6. Unduh file PDF
        return $dompdf->stream('rekap-absen-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
    }
}
