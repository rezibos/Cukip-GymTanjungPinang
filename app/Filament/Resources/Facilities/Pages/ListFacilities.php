<?php

namespace App\Filament\Resources\Facilities\Pages;

use App\Filament\Resources\Facilities\FacilityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ListFacilities extends ListRecords
{
    protected static string $resource = FacilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah')
                ->icon(Heroicon::Plus)
                ->modal()
                ->modalHeading('Tambah Fasilitas')
                ->modalDescription('Lengkapi data fasilitas baru di bawah ini.')
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
