<?php

namespace App\Filament\Resources\GymPackages\Pages;

use App\Filament\Resources\GymPackages\GymPackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGymPackages extends ListRecords
{
    protected static string $resource = GymPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
