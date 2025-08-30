<?php

use App\Models\User;
use App\Models\Absen;
use App\Models\SettingJam;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withSchedule(function (Schedule $schedule){
         $schedule->call(function(){
            $now = Carbon::now();
            $today = $now->toDateString();

            $settings = SettingJam::all()->keyBy('nama_jam');
            
            // Definisikan slot waktu
            $slots = [
                'absen_pagi' => ['start' => $settings['absen_pagi']->jam, 'end' => $settings['absen_pagi']->batas_jam],
                'absen_istirahat' => ['start' => $settings['absen_istirahat']->jam, 'end' => $settings['absen_istirahat']->batas_jam],
                'kembali_istirahat' => ['start' => $settings['kembali_istirahat']->jam, 'end' => $settings['kembali_istirahat']->batas_jam],
                'absen_pulang' => ['start' => $settings['absen_pulang']->jam, 'end' => $settings['absen_pulang']->batas_jam],
            ];

            foreach ($slots as $slotName => $times) {
                $startTime = Carbon::parse($today . ' ' . $times['start']);
                $endTime = Carbon::parse($today . ' ' . $times['end']);

                if ($now->greaterThanOrEqualTo($startTime) && $now->lessThanOrEqualTo($endTime)) {
                    // Cek user yang sudah absen hari ini
                    $usersWithAttendance = Absen::whereDate('tanggal_absen', $today)
                        ->where('keterangan', '!=', 'tanpa_keterangan') // Cek yang bukan tanpa_keterangan
                        ->pluck('user_id')
                        ->toArray();

                    // Ambil semua user
                    $users = User::all();

                    $absencesToCreate = [];

                    // Iterasi setiap user
                    foreach ($users as $user) {
                        // Cek apakah user sudah absen
                        if (!in_array($user->id, $usersWithAttendance)) {
                            $absencesToCreate[] = [
                                'id' => (string) \Illuminate\Support\Str::uuid(),
                                'user_id' => $user->id,
                                'keterangan' => 'tanpa_keterangan',
                                'bukti' => null,
                                'point' => 0,
                                'tanggal_absen' => $endTime->format('Y-m-d H:i:s'),
                                'show' => true,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }
                    }

                    // Masukkan ke database secara massal
                    if (!empty($absencesToCreate)) {
                        Absen::insert($absencesToCreate);
                    }
                }
            }
        })->everyFifteenMinutes(); // Ganti dengan jadwal yang kamu mau
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
