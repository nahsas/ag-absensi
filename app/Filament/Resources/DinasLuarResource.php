<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Absen;
use Filament\Forms\Form;
use App\Models\DinasLuar;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Card;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\DinasLuarResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DinasLuarResource\RelationManagers;

class DinasLuarResource extends Resource
{
    protected static ?string $model = DinasLuar::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                TextInput::make('judul')->required()->maxLength(255),
                // Mengubah Textarea menjadi RichEditor untuk Markdown
                RichEditor::make('deskripsi')->required()->columnSpan('full'),
                DatePicker::make('tanggal_mulai')->required(),
                DatePicker::make('tanggal_selesai')->required(),
                // Field 'approved' disembunyikan dari form
                Toggle::make('approved')
                    ->label('Disetujui')
                    ->default(false)
                    ->hidden(),
                // Menambahkan relasi ke pengguna terkait
                Select::make('users')
                    ->relationship('users', 'name')
                    ->multiple()
                    ->preload()
                    ->required()
                    ->label('Pengguna Terkait'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ... (kolom-kolom lainnya)
                TextColumn::make('judul')->searchable(),
                TextColumn::make('tanggal_mulai')->date()->sortable()
                    ->grow(false),
                TextColumn::make('tanggal_selesai')->date()->sortable(),
                TextColumn::make('users.name')->label('Pengguna Terkait')->searchable()
                    ->badge()
                    ->separator(',')
                    ->color('info'),
                BooleanColumn::make('approved')->label('Disetujui')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('setujui')
                    ->visible(fn ($record) => ! $record->approved && auth()->user()->isSuperAdmin())
                    ->action(function ($record) {
                        DB::beginTransaction();
                        try {
                            // 1. Set status dinas luar menjadi disetujui
                            $record->approved = true;
                            $record->save();

                            // 2. Ambil tanggal mulai dan tanggal selesai
                            $startDate = Carbon::parse($record->tanggal_mulai);
                            $endDate = Carbon::parse($record->tanggal_selesai);

                            // 3. Iterasi setiap tanggal
                            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                                // 4. Iterasi setiap pengguna yang terkait
                                foreach ($record->users as $user) {
                                    // 5. Buat entri absen baru
                                    Absen::create([
                                        'user_id' => $user->id,
                                        'keterangan' => 'dinas_luar',
                                        'bukti' => null, // Tidak ada bukti untuk dinas luar
                                        'point' => 0, // Atau poin lain yang sesuai
                                        'tanggal_absen' => $date,
                                        'show' => true, // Menampilkan di daftar absen pengguna
                                    ]);
                                }
                            }

                            DB::commit();

                            Notification::make()
                                ->title('Dinas luar berhasil disetujui.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            DB::rollBack();
                            Notification::make()
                                ->title('Gagal menyetujui dinas luar.')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation(),
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
            'index' => Pages\ListDinasLuars::route('/'),
            'create' => Pages\CreateDinasLuar::route('/create'),
            'view' => Pages\ViewDinasLuar::route('/{record}'),
            'edit' => Pages\EditDinasLuar::route('/{record}/edit'),
        ];
    }
}
