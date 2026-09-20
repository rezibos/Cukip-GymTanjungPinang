<?php

namespace App\Filament\Resources\GymPackages\Pages;

use App\Filament\Resources\GymPackages\GymPackageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGymPackage extends EditRecord
{
    protected static string $resource = GymPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
