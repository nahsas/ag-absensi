<?php

namespace App\Filament\Resources\AbsenResource\Pages;

use App\Models\Izin;
use App\Models\Sakit;
use Filament\Actions;
use Filament\Infolists\Components\View;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\AbsenResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Components\Field;

class ViewAbsen extends ViewRecord
{
    protected static string $resource = AbsenResource::class;
    protected static ?string $title = "Detail";
    public function infolist(Infolist $infolist): Infolist
    {
        $column_bukti = 0;
        $record = $infolist->record;
        $list_absen = ['pagi','istirahat','kembali_kerja','pulang'];
        $images = [];

        foreach ($list_absen as $data){
            if($record[$data]!=null and $column_bukti < 4){
                $column_bukti+=1;
                $images[] = View::make('OnlineImage')
                    ->viewData(["image"=>$record['bukti_'.$data],"title"=>'Absen '.ucfirst(str_replace('_',' ',$data))]);
            }
        }


        return $infolist
            ->schema([
                Grid::make(1)->schema([
                    Card::make('Data absen')->schema([
                        Grid::make(3)
                            ->schema([
                                    TextEntry::make('user.name')
                                        ->label('Nama'),
                                    TextEntry::make('user.nip')
                                        ->label('NIP'),
                                    TextEntry::make('user.nik')
                                        ->label('NIK'),
                            ]),
                        Section::make('Jam absen')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('pagi')
                                            ->time()
                                            ->badge()
                                            ->color('success'),
                                        TextEntry::make('istirahat')
                                            ->time()
                                            ->badge()
                                            ->color('success'),
                                        TextEntry::make('kembali_kerja')
                                            ->time()
                                            ->badge()
                                            ->color('success'),
                                        TextEntry::make('pulang')
                                            ->time()
                                            ->badge()
                                            ->color('success'),
                                    ])
                            ])->visible(fn($record)=>$record->keterangan == 'hadir')
                    ])->columnSpanFull(),
                    Card::make('Gambar bukti')->schema([
                        Grid::make($column_bukti)->schema($images)
                    ])
                ]),
            ]);
    }
}
