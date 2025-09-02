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
            $users = User::all();
            foreach($users as $user){
                $times = SettingJam::all()->sortBy('batas_jam')->pluck('batas_jam','jam');
                foreach($times as $key=>$value){
                    $batas_absen_bawah = Carbon::now()->setTimeFrom($key)->startOfMinute();
                    $batas_absen_atas = Carbon::now()->setTimeFrom($value)->startOfMinute();
                    if(Carbon::now()->startOfMinute() >= $batas_absen_atas){
                        $checkAbsen = Absen::where('user_id',$user->id)->whereIn('keterangan',["sakit","izin","hadir","tanpa_keterangan","dinas_luar"])->whereBetween('tanggal_absen', [$batas_absen_bawah, $batas_absen_atas])->first();
                        if(!$checkAbsen){
                            Absen::create([
                                'user_id'=>$user->id,
                                'keterangan'=>'tanpa_keterangan',
                                'bukti'=>null,
                                'point'=>-50,
                                'tanggal_absen'=>$batas_absen_atas,
                                'show'=>true,
                            ]);
                        }
                        echo "\n {$user->name} Alpha \n";
                    }else{
                        echo "No checking for this time";
                    }
                }
                
            }
        })->everyTwoSeconds(); // Ganti dengan jadwal yang kamu mau
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
