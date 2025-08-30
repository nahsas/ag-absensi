<?php

namespace App\Filament\Resources\SakitResource\Pages;

use App\Models\Absen;
use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Support\Carbon;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Split;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\SakitResource;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\ImageEntry;

class ViewSakit extends ViewRecord
{
    protected static string $resource = SakitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Setujui')
                ->requiresConfirmation()
                ->color('success')
                ->visible(fn($record)=>!$record->approved)
                ->action(function($record,$data){
                    $absen = Absen::find($record->absen_id);

                    $date = $record->tanggal;
                    $absen_to_remove = Absen::whereBetween('tanggal_absen',[Carbon::parse($date)->startOfDay(), Carbon::parse($date)->endOfDay()])->where('user_id',$record->user_id)->where('keterangan','tanpa_keterangan')->get();

                    if(!$absen){
                        return Notification::make()
                            ->danger()
                            ->title('Gagal aprrove izin')
                            ->body('Terjadi sesuatu pada program sehingga gagal untuk approve izin ini')
                            ->send();
                    }

                    foreach($absen_to_remove as $remove){
                        $remove->delete();
                    }

                    $record->approved = True;
                    $record->save();

                    return Notification::make()
                        ->success()
                        ->title('Izin berhasil di approve')
                        ->body('Izin ini berhasil di approve')
                        ->send();
                })
        ];
    }

    public function infolist(Infolist $infolist):Infolist{
        return $infolist->schema([
            Split::make([
                Card::make([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('user.name')
                                ->label('Nama'),
                            TextEntry::make('alasan'),
                            IconEntry::make('approved')
                                ->boolean()
                                ->label('Disetujui')
                                ->trueIcon('heroicon-o-check-badge')
                                ->falseIcon('heroicon-o-x-circle'),
                            TextEntry::make('absen.point')
                                ->suffix(' Point'),
                        ]),
                    TextEntry::make('alasan')
                        ->formatStateUsing(function($state){
                                $list = explode(',',trim($state, "\[\]\, "));
                                $res = "<ul>";
                                foreach($list as $data){
                                    $res = $res."<li>- ".$data."</li>";
                                }
                                $res = $res."</ul>";
                                return $res;
                            })
                        ->html(),            
                    ]),
                Card::make([
                    ViewEntry::make('')
                        ->view('OnlineImage',fn($record)=>['image'=>$record->bukti_sakit,'title'=>"Bukti Sakit"])
                ])
            ])
            ->columnSpanFull(),
        ]);
    }
}
