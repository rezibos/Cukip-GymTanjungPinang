<?php

namespace App\Filament\Resources\PersonalTrainers\Pages;

use App\Filament\Resources\PersonalTrainers\PersonalTrainerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ListPersonalTrainers extends ListRecords
{
    protected static string $resource = PersonalTrainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah personal trainer')
                ->icon(Heroicon::Plus)
                ->modal()
                ->modalHeading('Tambah personal trainer')
                ->modalSubmitActionLabel('Simpan')
                ->modalCancelActionLabel('Batal')
                ->modalWidth(Width::FourExtraLarge)
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->createAnother(false),
        ];
    }
}
