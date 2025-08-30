<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Absen;
use App\Models\SettingJam;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;
    protected $settings;

    public function __construct($month = null)
    {
        $date = now();
        if ($month) {
            $date = $date->month($month);
        }
        $this->startDate = $date->startOfMonth()->format('Y-m-d');
        $this->endDate = $date->endOfMonth()->format('Y-m-d');
        $this->settings = SettingJam::get()->keyBy('nama_jam');
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIP',
            'Hari Kerja',
            'Total Hadir',
            'Total Sakit',
            'Total Izin',
            'Total Tanpa Keterangan',
            'Total Dinas Luar',
            'Total Telat Masuk (menit)',
            'Rata-rata Telat (menit)',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $users = User::with(['absen' => function ($query) {
            $query->whereBetween('tanggal_absen', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        }])->get();

        $exportData = new Collection();

        foreach ($users as $user) {
            $absensGroupedByDate = $user->absen->groupBy(function ($absen) {
                return Carbon::parse($absen->tanggal_absen)->format('Y-m-d');
            });

            $totalHadir = 0;
            $totalSakit = 0;
            $totalIzin = 0;
            $totalTanpaKeterangan = 0;
            $totalDinasLuar = 0;
            $totalLateMinutes = 0;
            $hadirCount = 0;

            foreach ($absensGroupedByDate as $date => $absens) {
                $status = $absens->first()->keterangan;

                switch ($status) {
                    case 'hadir':
                        $totalHadir++;
                        $hadirCount++;
                        // Calculate late minutes for 'hadir' status only
                        $absenPagi = $absens->firstWhere(function($absen){
                            return Carbon::parse($absen->tanggal_absen)->format('H:i:s') < ($this->settings['absen_istirahat']->jam ?? '12:00:00');
                        });
                        if ($absenPagi && Carbon::parse($absenPagi->tanggal_absen)->format('H:i:s') > ($this->settings['absen_pagi']->jam ?? '08:00:00')) {
                            $lateMinutes = Carbon::parse($absenPagi->tanggal_absen)->diffInMinutes(Carbon::parse($absenPagi->tanggal_absen)->startOfDay()->addHours(8));
                            $totalLateMinutes += $lateMinutes;
                        }
                        break;
                    case 'sakit':
                        $totalSakit++;
                        break;
                    case 'izin':
                        $totalIzin++;
                        break;
                    case 'tanpa_keterangan':
                        $totalTanpaKeterangan++;
                        break;
                    case 'dinas_luar':
                        $totalDinasLuar++;
                        break;
                }
            }

            $totalWorkingDays = $absensGroupedByDate->count();
            $averageLateMinutes = $hadirCount > 0 ? $totalLateMinutes / $hadirCount : 0;

            $exportData->push([
                'name' => $user->name,
                'nip' => $user->nip,
                'total_working_days' => $totalWorkingDays,
                'total_hadir' => $totalHadir,
                'total_sakit' => $totalSakit,
                'total_izin' => $totalIzin,
                'total_tanpa_keterangan' => $totalTanpaKeterangan,
                'total_dinas_luar' => $totalDinasLuar,
                'total_late_minutes' => $totalLateMinutes,
                'average_late_minutes' => round($averageLateMinutes, 2),
            ]);
        }
        
        return $exportData;
    }
}