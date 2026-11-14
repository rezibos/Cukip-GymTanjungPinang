<?php

namespace App\Filament\Resources\TrainerSlots\Pages;

use App\Filament\Resources\TrainerSlots\TrainerSlotResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrainerSlot extends EditRecord
{
    protected static string $resource = TrainerSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
