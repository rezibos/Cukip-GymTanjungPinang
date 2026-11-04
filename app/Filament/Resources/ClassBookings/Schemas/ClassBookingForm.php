<?php

namespace App\Filament\Resources\ClassBookings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ClassBookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')->label('User')->relationship('user', 'name')->disabled(),
            Select::make('class_session_id')->label('Sesi Kelas')
                ->relationship('session', 'date')->disabled(),
            Select::make('status')->label('Status')
                ->options(['booked' => 'Terdaftar', 'cancelled' => 'Dibatalkan'])
                ->required(),
        ]);
    }
}