<?php

namespace App\Filament\Resources\GymLocations\Pages;

use App\Filament\Resources\GymLocations\GymLocationResource;
use Filament\Resources\Pages\ListRecords;

class ListGymLocations extends ListRecords
{
    protected static string $resource = GymLocationResource::class;

    public function getPageClasses(): array
    {
        return ['fi-resource-facilities'];
    }

    protected function getHeaderActions(): array
    {
        return [
            GymLocationResource::makeCreateAction(),
        ];
    }
}
