<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Sakit;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use App\Filament\Resources\SakitResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SakitResource\RelationManagers;

class SakitResource extends Resource
{
    protected static ?string $model = Sakit::class;

    protected static ?string $navigationGroup = 'Report';
    protected static ?string $navigationLabel = 'Daftar Karyawan Izin';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 3;

    public static function getBreadcrumb(): string
    {
        return 'Izin';
    }    
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query->orderByDesc('tanggal');
            })
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama'),
                TextColumn::make('code')
                    ->label('Code Izin'),
                TextColumn::make('tanggal')
                    ->searchable()
                    ->sortable()
                    ->label('Tanggal Izin'),
                TextColumn::make('alasan')
                    ->label('Alasan'),
                BooleanColumn::make('approved')->label('Disetujui'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
            ])
            ->bulkActions([
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
            'index' => Pages\ListSakits::route('/'),
            'create' => Pages\CreateSakit::route('/create'),
            'view' => Pages\ViewSakit::route('/{record}'),
            'edit' => Pages\EditSakit::route('/{record}/edit'),
        ];
    }
}
