<?php

namespace App\Filament\Resources\GymClasses\Schemas;

use App\Enums\DayOfWeek;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GymClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama Kelas')->required()->maxLength(100),
            TextInput::make('instructor')->label('Instruktur')->maxLength(100),

            Select::make('day_of_week')->label('Hari')
                ->options(DayOfWeek::options())->required(),

            TimePicker::make('start_time')->label('Jam Mulai')->required()->seconds(false),
            TimePicker::make('end_time')->label('Jam Selesai')->required()->seconds(false)
                ->after('start_time'),

            TextInput::make('capacity')->label('Kapasitas')->required()
                ->numeric()->integer()->minValue(1)->maxValue(200),

            Toggle::make('is_active')->label('Aktif')->default(true)
                ->helperText('Kelas nonaktif tidak ikut dibuatkan sesi baru saat generate.'),

            Textarea::make('description')->label('Deskripsi')->rows(2)->columnSpanFull(),
        ])->columns(2);
    }
}