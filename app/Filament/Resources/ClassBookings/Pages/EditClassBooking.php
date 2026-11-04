<?php

namespace App\Filament\Resources\ClassBookings\Pages;

use App\Filament\Resources\ClassBookings\ClassBookingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassBooking extends EditRecord
{
    protected static string $resource = ClassBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
