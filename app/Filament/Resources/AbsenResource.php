<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Absen;
use Filament\Forms\Form;
use App\Models\SettingJam;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
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
use Filament\Tables\Columns\Layout\Split;
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
    protected static ?string $navigationGroup = 'Report';
    protected static ?string $navigationLabel = 'Daftar Karyawan Absen';

    protected static ?int $navigationSort = 1;

 
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

        $schema = [
                "tanggal_absen"=>TextColumn::make('created_at')
                    ->date()
                    ->label('Tanggal Absen'),
                "user_name"=>TextColumn::make('user.name')->searchable(),
                "keterangan"=>TextColumn::make('keterangan')
                    ->formatStateUsing(fn($state)=>ucfirst(str_replace('_',' ',$state)))
                    ->badge()
                    ->color(function($state){
                        switch ($state){
                            case 'hadir':
                                return 'success';
                            case 'izin':
                                return 'warning';
                            case 'keluar_kantor':
                                return 'warning';
                            case 'dinas_luar':
                                return 'primary';
                            case 'tanpa_keterangan':
                                return 'danger';
                        }
                    }),
                "absen_pagi"=>TextColumn::make('pagi')
                    ->time()
                    ->color('success')
                    ->badge(),
                "absen_istirahat"=>TextColumn::make('istirahat')
                    ->time()
                    ->color('success')
                    ->badge(),
                "absen_kembali_kerja"=>TextColumn::make('kembali_kerja')
                    ->time()
                    ->color('success')
                    ->badge(),
                "absen_pulang"=>TextColumn::make('pulang')
                    ->time()
                    ->color('success')
                    ->badge(),
            ];
        
        $split = ["split"=>Split::make($schema)];

        return $table
            ->defaultSort('created_at','DESC')
            ->modifyQueryUsing(fn(Builder $query)=>$query->whereIn('keterangan',['hadir','keluar_kantor','izin','tanpa_keterangan','lembur'])->orderByDesc('created_at'))
            ->columns($split)
            ->filters([]);
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
