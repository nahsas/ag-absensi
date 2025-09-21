<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\SettingJam;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SettingJamResource\Pages;
use App\Filament\Resources\SettingJamResource\RelationManagers;

class SettingJamResource extends Resource
{
    protected static ?string $model = SettingJam::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'List Jam Kerja';
    public static function ShouldRegisterNavigation(): bool{
        return auth()->user()->role->name == 'superadmin';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                TextInput::make('nama_jam')->required()->maxLength(255),
                TimePicker::make('jam')->required()->label('Jam Ideal'), // time(0)
                TimePicker::make('batas_jam')->required()->label('Batas Akhir Absen'), // time(0)
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query)=>$query->orderBy('jam'))
            ->columns([
                Split::make([
                        TextColumn::make('nama_jam')->searchable(),
                        TextColumn::make('jam')->label('Jam Ideal')
                            ->grow(false)
                            ->formatStateUsing(fn($state)=>'Dari '.$state),
                        TextColumn::make('batas_jam')->label('Batas Absen')
                            ->formatStateUsing(fn($state)=>'Hingga '.$state),
                    ])
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
            'index' => Pages\ListSettingJams::route('/'),
            'create' => Pages\CreateSettingJam::route('/create'),
            'view' => Pages\ViewSettingJam::route('/{record}'),
            'edit' => Pages\EditSettingJam::route('/{record}/edit'),
        ];
    }
}
