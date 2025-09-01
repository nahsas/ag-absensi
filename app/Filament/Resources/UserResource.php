<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                TextInput::make('nip')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(10),
                TextInput::make('nik')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(16),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('no_hp')
                    ->required()
                    ->prefix('+62')
                    ->maxLength(255),
                
                // Mengubah input alamat menjadi Textarea
                Textarea::make('alamat')
                    ->required()
                    ->columnSpan('full'),
                
                // Logika untuk membatasi pilihan role
                Select::make('roles_id')
                    ->relationship('role', 'name', fn (Builder $query) => 
                        // Jika pengguna yang login BUKAN superadmin, kecualikan 'admin' dan 'superadmin'
                        !auth()->user()->isSuperAdmin() ? 
                            $query->whereNotIn('name', ['admin', 'superadmin']) : 
                            $query
                    )
                    ->required()
                    // Field ini dinonaktifkan untuk admin (bukan superadmin) saat mengedit
                    ->disabled(
                        fn (string $operation): bool => $operation === 'edit' && !auth()->user()->isSuperAdmin()
                    ),
                    
                // Password otomatis di-generate saat membuat user baru
                TextInput::make('password')
                    ->label('Password Awal')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->default(fn (): string => Str::random(8))
                    ->visible(fn (string $operation): bool => $operation === 'create'),
                
                // Password tidak bisa diubah oleh admin biasa
                TextInput::make('new_password')
                    ->label('Ubah Password')
                    ->password()
                    ->revealable()
                    ->required(false)
                    ->dehydrateStateUsing(fn (?string $state): string => Hash::make($state))
                    ->visible(fn (string $operation): bool => $operation === 'edit' && auth()->user()->isSuperAdmin()),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nip')->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('role.name')->sortable(),
                TextColumn::make('no_hp'),
                TextColumn::make('alamat')->limit(50)->label('Alamat'), // Menambahkan kolom alamat di sini
                BooleanColumn::make('isFirstLogin')->label('First Login'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
