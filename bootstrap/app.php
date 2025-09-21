<?php

use App\Http\Middleware\NgrokMiddleware;
use App\Models\Setting;
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
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web([
            NgrokMiddleware::class
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->call(function () {
            $settingPulang = SettingJam::where('nama_jam', 'Pulang')->first();
            if (!$settingPulang) {
                echo "Setting jam pulang tidak ditemukan.";
                return;
            }

            $batasPulang = Carbon::now()->setTimeFrom($settingPulang->batas_jam)->startOfMinute();
            echo ($batasPulang);
            if (Carbon::now()->startOfMinute() >= $batasPulang) {
                $users = User::all();
                foreach ($users as $user) {
                    // Cek apakah user sudah absen apapun hari ini
                    $sudahAbsen = Absen::where('user_id', $user->id)
                        ->whereDate('tanggal_absen', Carbon::now()->toDateString())
                        ->exists();
                    if (!$sudahAbsen) {
                        Absen::create([
                            'user_id' => $user->id,
                            'keterangan' => 'tanpa_keterangan',
                            'bukti' => null,
                            'point' => -50,
                            'tanggal_absen' => $batasPulang,
                            'show' => true,
                        ]);
                        echo "\n {$user->name} Alpha \n";
                    }
                }
            } else {
                echo "No checking for this time";
            }
        })->everyThirtyMinutes(); // Ganti dengan jadwal yang kamu mau
    })
    ->withSchedule(function(Schedule $schedule){
        $schedule->call(function(){
            $today = now();
            $liburPanjangStart = Carbon::parse(Setting::where('name','Libur Panjang')->first()['range_start']);
            $liburPanjangEnd = Carbon::parse(Setting::where('name','Libur Panjang')->first()['range_end']);

            if (($today->dayOfWeek === 6 || $today->dayOfWeek === 0 ) || ($liburPanjangStart <= $today && $today <= $liburPanjangEnd)) {
                $setLibur = Setting::where('name','Libur')->first();
                $setLibur['value']='1';
                $setLibur->save();
                echo 'Hari ini adalah libur!';
            } else {
                $setLibur = Setting::where('name','Libur')->first();
                $setLibur['value']='0';
                $setLibur->save();
                echo 'Hari ini bukan libur.';
            }
        })->daily();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();