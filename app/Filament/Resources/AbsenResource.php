<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Absen;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\AbsenResource\Pages;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AbsenResource\RelationManagers;

class AbsenResource extends Resource
{
    protected static ?string $model = Absen::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

 
    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                // Absensi seharusnya dikelola oleh sistem, jadi form ini hanya untuk tampilan admin
                TextInput::make('user.name')
                    ->label('Nama Pengguna')
                    ->disabled(),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->disabled(),
                TextInput::make('point')
                    ->label('Poin')
                    ->numeric()
                    ->disabled(),
                // ... tambahkan field lain jika diperlukan
            ])->columns(1),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query)=>$query->whereIn('keterangan',['hadir','sakit','izin']))
            ->columns([
                
                TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_absen')
                    ->label('Tanggal Absen')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('keterangan'),
                TextColumn::make('point')
                    ->suffix(' Point')
                    ->sortable()
                    ->color(fn (int $state): string => $state < 0 ? 'danger' : 'success'),
            ])
            ->filters([
                // Sembunyikan data absensi dengan keterangan 'izin' dan 'dinas_luar'
                Filter::make('keterangan_filter')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereNotIn('keterangan', ['izin', 'dinas_luar'])
                    )
                    ->toggle() // Tambahkan toggle untuk filter
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsens::route('/'),
            'create' => Pages\CreateAbsen::route('/create'),
            'view' => Pages\ViewAbsen::route('/{record}'),
            'edit' => Pages\EditAbsen::route('/{record}/edit'),
        ];
    }
}
