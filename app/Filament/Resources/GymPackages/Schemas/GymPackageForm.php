<?php

namespace App\Filament\Resources\GymPackages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GymPackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('gym_id')->label('Gym')
                ->relationship('gym', 'name')->required()->searchable()->preload(),
            TextInput::make('name')->label('Nama paket')->required()->maxLength(100),
            TextInput::make('duration')->label('Durasi')->required()->maxLength(50)
                ->placeholder('1 bulan'),
            TextInput::make('price')->label('Harga')->required()
                ->numeric()->integer()->minValue(0)->maxValue(100000000)->prefix('Rp'),
            TextInput::make('description')->label('Keterangan')->maxLength(255)->columnSpanFull(),
        ]);
    }
}