<?php

namespace App\Filament\Resources\AbsenResource\Pages;

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
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([

                    Section::make('Detail Absensi')
                    ->schema([
                        TextEntry::make('user.name')->label('Nama Pengguna'),
                        TextEntry::make('tanggal_absen')->label('Waktu Absen')->dateTime(),
                        TextEntry::make('keterangan'),
                        TextEntry::make('point')
                            ->suffix(' Point'),
                        ])->columns(2),
                        Card::make([
                            ViewEntry::make('bukti')
                                ->view('OnlineImage',fn($state)=>["image"=>$state,"title"=>"Bukti Absen"])
                                ->hidden(fn($record)=>$record->keterangan=='izin'),
                            ViewEntry::make('izin.bukti_kembali')
                                ->view('OnlineImage',fn($state)=>["image"=>$state])
                                ->hidden(fn($record)=>$record->keterangan!='izin'),
                        ])
                    ])->columnSpanFull()
            ]);
    }
}
