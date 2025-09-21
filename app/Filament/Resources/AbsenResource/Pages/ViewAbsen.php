<?php

namespace App\Filament\Resources\AbsenResource\Pages;

use App\Models\Izin;
use App\Models\Sakit;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\Split;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\AbsenResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\ImageEntry;

class ViewAbsen extends ViewRecord
{
    protected static string $resource = AbsenResource::class;
    protected static ?string $title = "Detail";
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([

                    Section::make('Detail Absensi')
                    ->schema([
                        TextEntry::make('user.name')->label('Nama Pengguna'),
                        TextEntry::make('tanggal_absen')->label('Waktu Absen')->dateTime(),
                        TextEntry::make('keterangan')
                        ->formatStateUsing(function($state){
                            if ($state == 'sakit'){
                                return "IZIN";
                            }

                            if ($state == 'izin'){
                                return "KELUAR KANTOR";
                            }

                            if ($state == 'lembur'){
                                return "LEMBUR";
                            }

                            if ($state == 'tanpa_keterangan'){
                                return "TIDAK HADIR";
                            }

                            return strtoupper($state);
                        })
                        ->badge(),
                        TextEntry::make('point')
                            ->suffix(' Point'),
                        ])->columns(2)
                        ->hidden(function($record){
                    }),
                        Card::make([
                            ViewEntry::make('bukti')
                                ->view('OnlineImage',function($state,$record){
                                    if ($record->keterangan == 'sakit'){
                                        $state = Sakit::where('absen_id',$record->id)->first()['bukti_sakit'];
                                    }

                                    if ($record->keterangan == 'izin'){
                                        $state = Izin::where('absen_id', $record->id)->first()['bukti_kembali'];
                                    }
                                    
                                    return ["image"=>$state,"title"=>"Bukti"];
                                })
                                ->hidden(fn($record)=>$record->keterangan=='izin'),
                            ViewEntry::make('izin.bukti_kembali')
                                ->view('OnlineImage',fn($state)=>["image"=>$state])
                                ->hidden(fn($record)=>$record->keterangan!='izin'),
                        ])->hidden(fn($record)=>$record->keterangan=='tanpa_keterangan')
                    ])->columnSpanFull()
            ]);
    }
}
