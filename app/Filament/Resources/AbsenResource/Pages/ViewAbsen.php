<?php

namespace App\Filament\Resources\AbsenResource\Pages;

use App\Models\Izin;
use App\Models\Sakit;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Illuminate\Support\Carbon;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Field;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\View;
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
        $column_bukti = 0;
        $record = $infolist->record;
        $list_absen = ['pagi','istirahat','kembali_kerja','pulang'];
        $images = [];
        $image_lembur = [
            View::make('OnlineImage')
                ->viewData(["image"=>$record->bukti_lembur_mulai,"title"=>'Bukti Lembur Mulai']),
            View::make('OnlineImage')
                ->viewData(["image"=>$record->bukti_lembur_selesai,"title"=>'Bukti Lembur Selesai'])
        ];

        foreach ($list_absen as $data){
            if($record[$data]!=null and $column_bukti < 4 and $record['keterangan'] == 'hadir'){
                $column_bukti+=1;
                $images[] = View::make('OnlineImage')
                    ->viewData(["image"=>$record['bukti_'.$data],"title"=>'Absen '.ucfirst(str_replace('_',' ',$data))]);
            }
            if ($record['keterangan']=='keluar_kantor'){
                $images[] = View::make('OnlineImage')
                    ->viewData(["image"=>$record->izin->bukti_kembali,"title"=>'Bukti Kembali']);
                break;
            }
            if ($record['keterangan']=='izin'){
                $images[] = View::make('OnlineImage')
                    ->viewData(["image"=>$record->sakit->bukti_sakit,"title"=>'Bukti Sakit']);
                break;
            }
        }
        

        return $infolist
            ->schema([
                Grid::make(1)->schema([
                    Card::make(fn($record)=>"Detail ".str_replace('_',' ',$record->keterangan).' '.Carbon::parse($record->created_at)->format('d M Y'))->schema([
                        Grid::make(3)
                            ->schema([
                                    TextEntry::make('user.name')
                                        ->label('Nama'),
                                    TextEntry::make('user.nip')
                                        ->label('NIP'),
                                    TextEntry::make('user.nik')
                                        ->label('NIK'),
                            ]),
                        Section::make('Izin')
                            ->schema([
                                Grid::make(4)
                                    ->schema([    
                                        TextEntry::make('sakit.code')
                                            ->label('Kode Izin'),
                                        TextEntry::make('sakit.alasan')
                                            ->label('Alasan'),
                                        TextEntry::make('sakit.tanggal')
                                            ->label('Tanggal')
                                            ->date(),
                                        TextEntry::make('sakit.approved')
                                            ->label('Status')
                                            ->formatStateUsing(fn($state)=>$state==true?'Disetujui':'Tidak Disetujui')
                                            ->icon(function($state){
                                                if ($state == true){
                                                    return 'heroicon-o-check-circle';
                                                }
                                                if ($state == false){
                                                    return 'heroicon-o-x-circle';
                                                }
                                            })
                                            ->iconColor(function($state){
                                                if ($state == true){
                                                    return 'success';
                                                }
                                                if ($state == false){
                                                    return 'danger';
                                                }
                                            })
                                            ->color(function($state){
                                                if ($state == true){
                                                    return 'success';
                                                }
                                                if ($state == false){
                                                    return 'danger';
                                                }
                                            }),
                                    ])
                            ])->visible(fn($record)=>$record->keterangan=='izin'),
                        Section::make('Keluar kantor')
                            ->schema([
                                Grid::make(5)
                                    ->schema([    
                                        TextEntry::make('izin.judul')
                                            ->label('Judul'),
                                        TextEntry::make('izin.alasan')
                                            ->label('Alasan'),
                                        TextEntry::make('izin.tanggal_izin')
                                            ->label('Jam Keluar')
                                            ->time(),
                                        TextEntry::make('izin.jam_kembali')
                                            ->label('Jam Kembali')
                                            ->time(),
                                        TextEntry::make('izin.keluar_selama')
                                            ->formatStateUsing(fn($state)=>($state * 60).' Menit')
                                            ->label('Lama keluar'),
                                    ])
                            ])->visible(fn($record)=>$record->keterangan=='keluar_kantor'),
                        Section::make('Lembur')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('mulai_lembur')
                                            ->time()
                                            ->badge()
                                            ->color('success'),
                                        TextEntry::make('selesai_lembur')
                                            ->time()
                                            ->badge()
                                            ->color('success'),
                                        TextEntry::make('lama_lembur')
                                            ->badge()
                                            ->suffix(' Jam')
                                            ->color('info'),
                                    ])
                            ])->visible(fn($record)=>$record->mulai_lembur != null),
                        Section::make('Jam absen')
                            ->schema([
                                Grid::make(5)
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
                                        TextEntry::make('lama_bekerja')
                                            ->badge()
                                            ->suffix(' Jam')
                                            ->color('info'),
                                    ])
                            ])->visible(fn($record)=>$record->keterangan == 'hadir')
                    ])->columnSpanFull(),
                    Card::make('Gambar bukti')->schema([ 
                        Grid::make($column_bukti)->schema($images)
                    ]),
                    Card::make('Gambar bukti lembur')->schema([ 
                        Grid::make(2)->schema($image_lembur)
                    ])->visible(fn($record)=>$record->mulai_lembur != null)
                ]),
            ]);
    }
}
