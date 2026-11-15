<?php

namespace App\Filament\Resources\GymLocations\Pages;

use App\Filament\Resources\GymLocations\GymLocationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGymLocation extends EditRecord
{
    protected static string $resource = GymLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
