<?php

namespace App\Filament\Resources\GymLocations;

use App\Filament\Resources\GymLocations\Pages\CreateGymLocation;
use App\Filament\Resources\GymLocations\Pages\EditGymLocation;
use App\Filament\Resources\GymLocations\Pages\ListGymLocations;
use App\Filament\Resources\GymLocations\Schemas\GymLocationForm;
use App\Filament\Resources\GymLocations\Tables\GymLocationsTable;
use App\Models\GymLocation;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GymLocationResource extends Resource
{
    protected static ?string $model = GymLocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $navigationLabel = 'Lokasi Gym';

    protected static ?string $modelLabel = 'Lokasi Gym';

    protected static ?string $pluralModelLabel = 'Lokasi Gym';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten website';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GymLocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GymLocationsTable::configure($table);
    }

    public static function makeCreateAction(): CreateAction
    {
        return CreateAction::make()
            ->label('Tambah')
            ->icon(Heroicon::Plus)
            ->modal()
            ->modalHeading('Tambah Lokasi Gym')
            ->modalDescription('Lengkapi data lokasi gym yang akan tampil pada peta halaman utama.')
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalWidth(Width::FourExtraLarge)
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->createAnother(false);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGymLocations::route('/'),
            'create' => CreateGymLocation::route('/create'),
            'edit' => EditGymLocation::route('/{record}/edit'),
        ];
    }
}
