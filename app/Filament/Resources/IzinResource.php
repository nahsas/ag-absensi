<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Izin;
use Filament\Tables;
use App\Models\Absen;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Card;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Split;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\IzinResource\Pages;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\IzinResource\RelationManagers;

class IzinResource extends Resource
{
    protected static ?string $model = Izin::class;

    protected static ?string $navigationLabel = 'Daftar Karyawan Keluar Kantor';
    protected static ?string $navigationGroup = 'Report';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-start-on-rectangle';

    protected static ?int $navigationSort = 2;

    public static function getBreadcrumb(): string
    {
        return 'Keluar Kantor';
    }  

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label('Pengguna'),
                Textarea::make('alasan')
                    ->required()
                    ->columnSpan('full'),
                DateTimePicker::make('jam_keluar')
                    ->required()
                    ->label('Tanggal & Jam Keluar'),
                TextInput::make('keluar_selama')
                    ->numeric()
                    ->required()
                    ->label('Durasi Keluar (menit)'),
                Toggle::make('approved')
                    ->label('Kebutuhan kantor')
                    ->default(false)
                    ->hidden(), // Sembunyikan dari form
                TextInput::make('point')
                    ->numeric()
                    ->default(0)
                    ->hidden(), // Sembunyikan dari form
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query)=>$query->orderBy('jam_kembali','DESC'))
            ->columns([
                TextColumn::make('judul')->searchable()->sortable(),
                TextColumn::make('user.name')->searchable()->sortable()
                    ->label('Nama'),
                TextColumn::make('alasan')->limit(50),
                TextColumn::make('tanggal_izin')->dateTime()->label('Waktu Keluar')->sortable(),
                TextColumn::make('jam_kembali')->dateTime()->label('Waktu Kembali')->sortable(),
                TextColumn::make('keluar_selama')->label('Lama Izin')
                    ->formatStateUsing(fn($state)=>$state*60)
                    ->suffix(' Menit'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // // Aksi 'setujui' untuk superadmin
                // Action::make('Termasuk kebutuhan kantor')
                //     ->visible(fn ($record) => ! $record->approved)
                //     ->requiresConfirmation()
                //     ->form([
                //         TextInput::make('point')
                //             ->numeric()
                //             ->maxValue(20)
                //             ->placeholder('Maximal memberikan 20 point')
                //             ->default(0)
                //     ])
                //     ->color('success')
                //     ->visible(fn($record)=>!$record->approved)
                //     ->action(function($record,$data){
                //         $absen = Absen::find($record->absen_id);

                //         if(!$absen){
                //             return Notification::make()
                //                 ->danger()
                //                 ->title('Gagal aprrove izin')
                //                 ->body('Terjadi sesuatu pada program sehingga gagal untuk approve izin ini')
                //                 ->send();
                //         }

                //         $absen->point = $data['point'];
                //         $absen->save();

                //         $record->approved = True;
                //         $record->save();

                //         return Notification::make()
                //             ->success()
                //             ->title('Izin berhasil di approve')
                //             ->body('Izin ini berhasil di approve')
                //             ->send();
                //     })
                //     ->icon('heroicon-o-check-circle')
                //     ->color('success')
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
            'index' => Pages\ListIzins::route('/'),
            'create' => Pages\CreateIzin::route('/create'),
            'view' => Pages\ViewIzin::route('/{record}'),
            'edit' => Pages\EditIzin::route('/{record}/edit'),
        ];
    }
}
