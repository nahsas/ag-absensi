<?php

namespace App\Exports;

use App\Models\Absen;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $res = [
            [
                "Nama",
                "Jumlah Absen Bulan Ini",
                "Point Yang Di Peroleh",
                "Performa"
            ]
        ];

        $users = User::all();
        $now = now();
        $bulanIni = $now->format('m');
        $tahunIni = $now->format('Y');
        foreach($users as $user){
            // Ambil absen pagi (keterangan 'hadir') per hari di bulan ini
            $absenPagi = $user->absen()
                ->whereMonth('tanggal_absen', $bulanIni)
                ->whereYear('tanggal_absen', $tahunIni)
                ->whereIn('keterangan', ['hadir','dinas_luar'])
                ->orderBy('tanggal_absen', 'asc')
                ->get()
                ->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item->tanggal_absen)->format('Y-m-d');
                });
            $jumlahAbsenBulanIni = $absenPagi->count();

            // Hitung point bulan ini
            $pointBulanIni = $user->absen()
                ->whereMonth('tanggal_absen', $bulanIni)
                ->whereYear('tanggal_absen', $tahunIni)
                ->sum('point');

            // Hitung performa: persentase kehadiran pagi dibanding hari kerja (exclude 'tanpa_keterangan')
            $absenBulanIni = $user->absen()
                ->whereMonth('tanggal_absen', $bulanIni)
                ->whereYear('tanggal_absen', $tahunIni)
                ->get()
                ->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item->tanggal_absen)->format('Y-m-d');
                });
            $hariKerja = 0;
            $hadirPagi = 0;
            foreach($absenBulanIni as $tanggal => $absens){
                $adaTanpaKeterangan = $absens->where('keterangan', 'tanpa_keterangan')->count() > 0;
                if(!$adaTanpaKeterangan){
                    $hariKerja++;
                    // Cek ada absen pagi (hadir)
                    if($absens->whereIn('keterangan', ['hadir','dinas_luar'])->count() > 0){
                        $hadirPagi++;
                    }
                }
            }
            // Hitung hari kerja bulan ini (Senin-Jumat)
            $startOfMonth = \Carbon\Carbon::create($tahunIni, $bulanIni, 1);
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            $hariKerja = 0;
            for ($date = $startOfMonth->copy(); $date <= $endOfMonth; $date->addDay()) {
                if ($date->isWeekday()) {
                    $hariKerja++;
                }
            }
            $performa = $hariKerja > 0 ? round(($hadirPagi / $hariKerja) * 100, 2) . '%' : '0%';

            $res[] = [
                $user->name,
                $jumlahAbsenBulanIni,
                $pointBulanIni,
                $performa
            ];
        }

        // Contoh formula: total point seluruh user
        $totalPointFormula = '=SUM(C2:C' . (count($res)) . ')';
        $res[] = [
            'Total Point',
            '',
            $totalPointFormula,
            ''
        ];

        $res[] = [
            [
                "",
                "",
                "",
            ]
        ];

        $res[] = [
            [
                "",
                "",
                "",
            ]
        ];

        $res[] = [
            'Nama',
            'Tanggal Absen',
            'Keterangan',
            'Point',
            'Apakah Terlambat',
        ];

        // Ambil history absen, urutkan berdasarkan nama user lalu tanggal absen (desc)
        $absen = Absen::with('user')
            ->orderBy('user_id')
            ->orderByDesc('tanggal_absen')
            ->get();
        foreach($absen as $data){
            $res[] = [
                $data->user ? $data->user->name : '-',
                $data->tanggal_absen,
                $data->keterangan,
                $data->point,
                $data->point < 0 ? 'Terlambat' : 'Tepat Waktu',
            ];
        }

        $res = collect($res);

        // dd($res);

        return $res ? $res : [];
    }
}
