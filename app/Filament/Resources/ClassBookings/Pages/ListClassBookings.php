<?php

namespace App\Filament\Resources\ClassBookings\Pages;

use App\Filament\Resources\ClassBookings\ClassBookingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClassBookings extends ListRecords
{
    protected static string $resource = ClassBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
