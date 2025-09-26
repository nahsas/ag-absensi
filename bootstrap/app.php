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
            $checkJam = now()->dayOfWeek != 6 ? Carbon::now()->setTimeFromTimeString(Setting::where('name','normal_alpha_time')->first()->value) : Carbon::now()->setTimeFromTimeString(Setting::where('name','saturday_alpha_time')->first()->value);
            if (strtotime('now')>=strtotime($checkJam->toDateTimeString())){
                $absen_user_ids = Absen::query()->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->pluck('user_id');
                $users = User::whereNotIn('id',$absen_user_ids)->get();
                foreach($users as $user){
                    if (!Absen::where('user_id', $user->id)->whereBetween('created_at',[now()->startOfDay(), now()->endOfDay()])->first())
                    {
                        Absen::create([
                            "user_id"=>$user->id,
                            "keterangan"=>'tanpa_keterangan',
                            "created_at"=>now()
                        ]);
                        echo '\n '.$user->name." Alpha \n";
                    }else{
                        echo 'Skip';
                    }
                }
            }
        })->everyFifteenMinutes(); // Ganti dengan jadwal yang kamu mau
    })
    ->withSchedule(function(Schedule $schedule){
        $schedule->call(function(){
            $today = now();
            $liburPanjangStart = Carbon::parse(Setting::where('name','Libur Panjang')->first()['range_start']);
            $liburPanjangEnd = Carbon::parse(Setting::where('name','Libur Panjang')->first()['range_end']);

            if (($today->dayOfWeek === 0 ) || ($liburPanjangStart <= $today && $today <= $liburPanjangEnd)) {
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