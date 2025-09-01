<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Table;
use App\Models\RolesSetting;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RolesSettingResource\Pages;
use App\Filament\Resources\RolesSettingResource\RelationManagers;

class RolesSettingResource extends Resource
{
    protected static ?string $model = RolesSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    public static function ShouldRegisterNavigation(): bool{
        return auth()->user()->role->name == 'superadmin';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                TextInput::make('name')
                    ->label('Judul aturan'),
                Select::make('roles_id')
                    ->relationship('role', 'name')
                    ->required(),
                Select::make('jam_id')
                    ->relationship('jam', 'nama_jam')
                    ->required(),
                Select::make('operator') // varchar(255)
                    ->options([
                        '>' => 'Lebih Besar dari (>)',
                        '<' => 'Lebih Kecil dari (<)',
                        '=' => 'Sama dengan (=)',
                        '>=' => 'Lebih Besar Sama Dengan (>=)',
                        '<=' => 'Lebih Kecil Sama Dengan (<=)',
                    ])
                    ->required(),
                TextInput::make('value') // int4
                    ->numeric()
                    ->label('Value (Menit Selisih)')
                    ->required(),
                TextInput::make('point') // int4
                    ->numeric()
                    ->label('Poin (+/-)')
                    ->required(),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                        TextColumn::make('role.name')->sortable(),
                        TextColumn::make('jam.nama_jam')->label('Tipe Jam')->sortable()
                            ->grow(false),
                        TextColumn::make('operator')->sortable()
                            ->grow(false)
                            ->formatStateUsing(function($state){
                                switch($state){
                                    case '>':
                                       return 'Lebih Dari';
                                    case '<':
                                        return 'Kurang Kecil Dari';
                                    case '=':
                                        return 'Sama Dengan';
                                    case '>=':
                                        return 'Lebih Dari Sama Dengan (>=)';
                                    case '<=':
                                        return 'Kurang Dari Sama Dengan (<=)';
                                    default:
                                        return $state;
                                }
                            }),
                        TextColumn::make('value')->label('Selisih')
                            ->suffix(' Menit'),
                        TextColumn::make('point')->color(fn (int $state): string => $state < 0 ? 'danger' : 'success')
                            ->suffix(' Point'),
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
            'index' => Pages\ListRolesSettings::route('/'),
            'create' => Pages\CreateRolesSetting::route('/create'),
            'view' => Pages\ViewRolesSetting::route('/{record}'),
            'edit' => Pages\EditRolesSetting::route('/{record}/edit'),
        ];
    }
}
