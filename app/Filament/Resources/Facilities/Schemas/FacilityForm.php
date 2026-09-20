<?php

namespace App\Filament\Resources\Facilities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('gym_id')->label('Gym')
                ->relationship('gym', 'name')->required()->searchable()->preload(),
            TextInput::make('name')->label('Nama fasilitas')->required()->maxLength(100),
            TextInput::make('description')->label('Keterangan')->maxLength(255)->columnSpanFull(),
        ]);
    }
}