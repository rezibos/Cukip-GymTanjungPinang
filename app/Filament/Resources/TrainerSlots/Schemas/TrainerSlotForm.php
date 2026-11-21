<?php

namespace App\Filament\Resources\TrainerSlots\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class TrainerSlotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('personal_trainer_id')
                    ->label('Pelatih')
                    ->relationship('trainer', 'name')
                    ->searchable()
                    ->searchPrompt('Cari nama pelatih...')
                    ->preload()
                    ->extraFieldWrapperAttributes(['class' => 'trainer-slot-trainer-select'], merge: true)
                    ->required(),
                Select::make('status')->label('Status')->options(['available' => 'Tersedia', 'held' => 'Ditahan pembayaran', 'booked' => 'Sudah dibooking'])->default('available')->required(),
                DateTimePicker::make('starts_at')->label('Mulai sesi')->seconds(false)->required(),
            ]);
    }
}
