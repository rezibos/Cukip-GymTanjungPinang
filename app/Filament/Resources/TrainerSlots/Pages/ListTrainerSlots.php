<?php

namespace App\Filament\Resources\TrainerSlots\Pages;

use App\Filament\Resources\TrainerSlots\TrainerSlotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrainerSlots extends ListRecords
{
    protected static string $resource = TrainerSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
