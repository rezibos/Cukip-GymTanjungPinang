<?php

namespace App\Filament\Resources\TrainerBookings\Pages;

use App\Filament\Resources\TrainerBookings\TrainerBookingResource;
use Filament\Resources\Pages\ListRecords;

class ListTrainerBookings extends ListRecords
{
    protected static string $resource = TrainerBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            TrainerBookingResource::makeCreateAction(),
        ];
    }
}
