<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ExportPdfController extends Controller
{
    public function exportPdf(Request $request)
    {
        // Bagian ini sama: Mendapatkan rentang tanggal dari form dan memproses data
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
                    'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'tanpa_keterangan' => 0, 'dinas_luar' => 0,
                ]
            ];

            foreach ($dateRange as $date) {
                $formattedDate = $date->format('Y-m-d');
                $status = 'libur'; 

                if ($absensByDate->has($formattedDate)) {
                    $record = $absensByDate->get($formattedDate)->first();
                    $status = $record->keterangan;
                    $userData['total'][$status]++;
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
