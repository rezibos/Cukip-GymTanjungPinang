<?php

namespace App\Filament\Resources\Facilities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama Fasilitas')->required()->maxLength(100),
            TextInput::make('description')->label('Keterangan')->maxLength(255)->columnSpanFull(),
        ]);
    }
}