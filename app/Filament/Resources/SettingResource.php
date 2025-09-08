<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use App\Filament\Resources\SettingResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SettingResource\RelationManagers;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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

        if($data->type == 'int'){
            $editForm[] = TextInput::make('value')
                            ->required()
                            ->numeric();
        }

        return $form
            ->schema($editForm)->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    TextColumn::make('name'),
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

                                case 'array':
                                    // Gunakan var_export untuk representasi array yang bisa dibaca
                                    return "<span style='color: purple;'>Array: <b>" . var_export($state, true) . "</b></span>";

                                case 'object':
                                    // Tampilkan nama class dari objek
                                    return "<span style='color: orange;'>Objek dari kelas: <b>" . get_class($state) . "</b></span>";

                                default:
                                    // Tangani semua tipe data lain
                                    return "<span style='color: black;'>Tipe Lain: <b>$state</b></span>";
                            }
                        })->html(true)
                ])
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
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
