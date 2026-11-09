<?php

namespace App\Filament\Resources\Memberships\Pages;

use App\Filament\Resources\Memberships\MembershipResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ListMemberships extends ListRecords
{
    protected static string $resource = MembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah')
                ->icon(Heroicon::Plus)
                ->modal()
                ->modalHeading('Tambah Membership')
                ->modalDescription('Lengkapi detail membership baru di bawah ini.')
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
