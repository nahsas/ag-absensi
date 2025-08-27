<?php

namespace App\Filament\Resources\DinasLuarResource\Pages;

use Filament\Actions;
use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\DinasLuarResource;

class ViewDinasLuar extends ViewRecord
{
    protected static string $resource = DinasLuarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn ($record) => ! $record->approved || auth()->user()->isSuperAdmin()),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Dinas Luar')
                    ->schema([
                        TextEntry::make('judul')->label('Judul'),
                        TextEntry::make('tanggal_mulai')->label('Tanggal Mulai')->date(),
                        TextEntry::make('tanggal_selesai')->label('Tanggal Selesai')->date(),
                        // Icon untuk status disetujui
                        IconEntry::make('approved')
                            ->label('Status Persetujuan')
                            ->boolean()
                            ->icon(fn (bool $state): string => match ($state) {
                                true => 'heroicon-o-check-circle',
                                false => 'heroicon-o-x-circle',
                            })
                            ->color(fn (bool $state): string => match ($state) {
                                true => 'success',
                                false => 'danger',
                            }),
                    ])->columns(2),

                Section::make('Pengguna Terkait')
                    ->schema([
                        // Menampilkan nama pengguna yang terkait dalam format tag
                        TextEntry::make('users.name')
                            ->label('Daftar Pengguna')
                            ->badge(), // Ini akan menampilkan setiap nama dalam badge terpisah
                    ]),

                Section::make('Deskripsi')
                    ->schema([
                        // Menggunakan TextEntry untuk me-render RichEditor
                        TextEntry::make('deskripsi')
                            ->label('Deskripsi Lengkap')
                            ->columnSpan('full')
                            ->html(), // Mengaktifkan rendering HTML/Markdown
                    ])
            ]);
    }
}
