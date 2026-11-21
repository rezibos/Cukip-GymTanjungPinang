<?php

namespace App\Filament\Resources\ClassBookings\Schemas;

use App\Models\ClassSession;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ClassBookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')->label('User')
                ->relationship('user', 'name')
                ->searchable(['name', 'email'])
                ->preload()
                ->required(),
            Select::make('class_session_id')->label('Sesi Kelas')
                ->relationship('session', 'date')
                ->getOptionLabelFromRecordUsing(fn (ClassSession $record): string => sprintf(
                    '%s · %s · %s',
                    $record->gymClass->name,
                    $record->date->translatedFormat('d M Y'),
                    $record->gymClass->time_range,
                ))
                ->searchable()
                ->preload()
                ->required(),
            Select::make('status')->label('Status')
                ->searchable(false)
                ->position('top')
                ->options(['booked' => 'Terdaftar', 'cancelled' => 'Dibatalkan'])
                ->default('booked')
                ->required(),
        ]);
    }
}
