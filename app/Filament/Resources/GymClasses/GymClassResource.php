<?php

namespace App\Filament\Resources\GymClasses;

use App\Filament\Resources\GymClasses\Pages\CreateGymClass;
use App\Filament\Resources\GymClasses\Pages\EditGymClass;
use App\Filament\Resources\GymClasses\Pages\ListGymClasses;
use App\Filament\Resources\GymClasses\Schemas\GymClassForm;
use App\Filament\Resources\GymClasses\Tables\GymClassesTable;
use App\Models\GymClass;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GymClassResource extends Resource
{
    protected static ?string $model = GymClass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Jenis kelas';

    protected static ?string $modelLabel = 'Jenis kelas';

    protected static ?string $pluralModelLabel = 'Jenis kelas';

    protected static string|\UnitEnum|null $navigationGroup = 'Kelas & latihan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return GymClassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GymClassesTable::configure($table);
    }

    public static function makeCreateAction(): CreateAction
    {
        return CreateAction::make()
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
            'index' => ListGymClasses::route('/'),
            'create' => CreateGymClass::route('/create'),
            'edit' => EditGymClass::route('/{record}/edit'),
        ];
    }
}
