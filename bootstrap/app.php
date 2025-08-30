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
                $jams = SettingJam::all();
                foreach($jams as $jam){
                    // $startTime = Carbon::parse()
                    // <!-- $endTime =  -->
                    // Absen::whereBetween('tanggal_absen',[Carbon::today()->setTime(9,30,0),Carbon::today()->setTime(12,59,0)]);
                }
            }
            Absen::create([
                'user_id'=>$user->id,
                'keterangan'=>"hadir",
                'bukti'=>null,
                'point'=>0,
                'tanggal_absen'=>Carbon::now(),
                'show'=>true,
            ]);
        })->everyMinute();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
