<?php

namespace App\Filament\Resources\GymClasses\Pages;

use App\Filament\Resources\GymClasses\GymClassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ListGymClasses extends ListRecords
{
    protected static string $resource = GymClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah')
                ->icon(Heroicon::Plus)
                ->modal()
                ->modalHeading('Tambah Jenis Kelas')
                ->modalDescription('Lengkapi data jenis kelas baru di bawah ini.')
                ->modalSubmitActionLabel('Simpan')
                ->modalCancelActionLabel('Batal')
                ->modalFooterActionsAlignment(Alignment::End)
                ->modalWidth(Width::FourExtraLarge)
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->createAnother(false),
        ];
    }
}
