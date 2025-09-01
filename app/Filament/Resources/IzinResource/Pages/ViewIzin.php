<?php

namespace App\Filament\Resources\IzinResource\Pages;

use App\Models\Absen;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\Grid;
use App\Filament\Resources\IzinResource;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Split;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\ImageEntry;

class ViewIzin extends ViewRecord
{
    protected static string $resource = IzinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Tolak Izin')
                ->requiresConfirmation()
                ->form([
                    TextInput::make('point')
                        ->numeric()
                        ->maxValue(0)
                        ->minValue(-20)
                        ->placeholder('Maximal memberikan -20 point')
                        ->default(0)
                ])
                ->color('danger')
                ->visible(fn($record)=>$record->approved==null)
                ->action(function($record,$data){
                    $absen = Absen::find($record->absen_id);

                    if(!$absen){
                        return Notification::make()
                            ->danger()
                            ->title('Gagal menolak izin')
                            ->body('Terjadi sesuatu pada program sehingga gagal untuk menolak izin ini')
                            ->send();
                    }

                    $absen->point = $data['point'];
                    $absen->save();

                    $record->approved = False;
                    $record->save();

                    return Notification::make()
                        ->success()
                        ->title('Izin berhasil di tolak')
                        ->body('Izin ini berhasil di tolak')
                        ->send();
                }),
            Action::make('Terima Izin')
                ->requiresConfirmation()
                ->form([
                    TextInput::make('point')
                        ->numeric()
                        ->maxValue(20)
                        ->minValue(0)
                        ->placeholder('Maximal memberikan 20 point')
                        ->default(0)
                ])
                ->color('success')
                ->visible(fn($record)=>$record->approved==null)
                ->action(function($record,$data){
                    $absen = Absen::find($record->absen_id);

                    if(!$absen){
                        return Notification::make()
                            ->danger()
                            ->title('Gagal approve izin')
                            ->body('Terjadi sesuatu pada program sehingga gagal untuk approve izin ini')
                            ->send();
                    }

                    $absen->point = $data['point'];
                    $absen->save();

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
                            TextEntry::make('judul'),
                            TextEntry::make('user.name')
                                ->label('Nama'),
                            TextEntry::make('keluar_selama')
                                ->label('Lama Izin')
                                ->suffix(' Menit'),
                            TextEntry::make('absen.tanggal_absen')
                                ->label('Jam Keluar')
                                ->time(),
                            TextEntry::make('jam_kembali')
                                ->time(),
                            IconEntry::make('approved')
                                ->boolean()
                                ->label('Diterima')
                                ->trueIcon('heroicon-o-check-badge')
                                ->falseIcon('heroicon-o-x-circle'),
                            TextEntry::make('absen.point')
                                ->suffix(' Point'),
                        ]),
                    TextEntry::make('alasan'),
                ]),
                Card::make([
                    ViewEntry::make('')
                        ->view('OnlineImage',fn($record)=>['image'=>$record->bukti_kembali])
                ])
            ])
            ->columnSpanFull(),
        ]);
    }
}
