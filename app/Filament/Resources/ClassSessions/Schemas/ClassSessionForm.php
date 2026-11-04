<?php

namespace App\Filament\Resources\ClassSessions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClassSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('gym_class_id')->label('Kelas')->relationship('gymClass', 'name')->required()->searchable()->preload(),
            DatePicker::make('date')->label('Tanggal')->required(),
            TextInput::make('capacity')->label('Kapasitas')->required()->numeric()->integer()->minValue(1),
        ]);
    }
}