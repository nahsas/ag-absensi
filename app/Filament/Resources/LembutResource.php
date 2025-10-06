<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Tables;
use App\Models\Lembur;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LembutResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LembutResource\RelationManagers;

class LembutResource extends Resource
{
    protected static ?string $model = Lembur::class;
    protected static ?string $navigationLabel = 'Daftar Karyawan Lembur';
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Report';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    TextInput::make('code')
                        ->required()
                        ->readonly()
                        ->maxLength(255)
                        ->label('Kode Lembur')
                        ->default(function () {
                            $last = \App\Models\Lembur::orderByDesc('id')->count();
                            $nextNumber = $last ? ($last + 1) : 1;
                            return 'LMBR-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                        }),
                    DatePicker::make('start_date')
                        ->required()
                        ->label('Tanggal Mulai')
                        ->reactive()
                        ->afterStateUpdated(fn($get, $set)=>$get('end_date')<=$get('start_date')?$set('start_date', now()->toDateString()):true)
                        ->default(now()->toDateString()),
                    DatePicker::make('end_date')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn($get, $set)=>$get('end_date')<=$get('start_date')?$set('end_date', now()->toDateString()):true)
                        ->label('Tanggal Selesai')
                        ->default(now()->toDateString()),
                ]),
                Select::make('users')
                    ->relationship('users', 'name')
                    ->multiple()
                    ->preload()
                    ->columnSpan('full')
                    ->searchable()
                    ->label('Nama Karyawan'),   
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kode Lembur')->sortable()->searchable(),
                TextColumn::make('users_count')->label('Jumlah Karyawan')->counts('users')->sortable(),
                TextColumn::make('users.name')->label('Karyawan')->wrap()->badge()->separator(',')->limit(50),
                TextColumn::make('start_date')->label('Tanggal Lembur')->date()->sortable(),
                TextColumn::make('end_date')->label('Lembur Berakhir')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListLembuts::route('/'),
            // 'create' => Pages\CreateLembut::route('/create'),
            // 'edit' => Pages\EditLembut::route('/{record}/edit'),
        ];
    }
}
