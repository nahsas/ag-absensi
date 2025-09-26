<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Absen;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use App\Filament\Resources\SettingResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SettingResource\RelationManagers;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    public static function form(Form $form): Form
    {
        $data = $form->getModelInstance();

        $editForm = [];

        $editForm[] = TextInput::make('name')
                        ->disabled();

        if($data->type == 'bool'){
            $editForm[] = Toggle::make('value')
                            ->inline(false);
        }

        if($data->type == 'int' || $data->type == 'float'){
            $editForm[] = TextInput::make('value')
                            ->required()
                            ->numeric();
        }

        if($data->type == 'time'){
            $editForm[] = TimePicker::make('value')
                            ->required();
        }

        if($data->type == "range_date"){
            $editForm[] = Grid::make(2)->schema([
                DatePicker::make('range_start')
                    ->label('Dari Tanggal')
                    ->required(),
                DatePicker::make('range_end')
                    ->label('Hingga Tanggal')
                    ->required(),
            ])->columnSpan(2);
        }

        return $form
            ->schema($editForm)->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function(Builder $query){
                return $query->orderBy('display_name','asc')->where('is_hidden', false);
            })
            ->columns([
                Stack::make([]),
                    TextColumn::make('display_name'),
                    TextColumn::make('range_start')
                        ->formatStateUsing(function ($state,$record) {
                            // dd($record);
                            return $state ? $state." Hingga ".$record['range_end'] : '-';
                        }),
                    TextColumn::make('value')
                        ->formatStateUsing(function ($record, $state) {
                            switch ($record->type) {
                                case 'bool':
                                    // Ubah boolean menjadi teks yang mudah dibaca
                                    return $state ? "<span style='color: blue;'>Status: <b>Ya</b></span>" : "<span style='color: red;'>Status: <b>Tidak</b></span>";

                                case 'int':
                                case 'double': // Tipe float di PHP diwakili oleh 'double'
                                    return "<span style='color: green;'>Angka: <b>$state</b></span>";

                                case 'string':
                                    // Beri tanda kutip pada string
                                    return "<span style='color: brown;'>Teks: <b>\"$state\"</b></span>";

                                case 'NULL':
                                    return "<span style='color: grey;'>Data: <i>Tidak Ada</i></span>";

                                case 'time':
                                    // Gunakan var_export untuk representasi array yang bisa dibaca
                                    return "<span style='color: purple;'>Waktu: <b>" . $state . "</b></span>";

                                case 'float':
                                    // Tampilkan nama class dari objek
                                    return "<span style='color: orange;'>Desimal: <b>" . $state . "</b></span>";

                                default:
                                    // Tangani semua tipe data lain
                                    return "<span style='color: black;'>Tipe Lain: <b>$state</b></span>";
                            }
                        })->html(true)
                ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->label('Ubah')
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
            'index' => Pages\ListSettings::route('/'),
            // 'edit' => Pages\EditSetting::route('/{record}'),
        ];
    }
}
