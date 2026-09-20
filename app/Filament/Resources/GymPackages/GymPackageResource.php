<?php

namespace App\Filament\Resources\GymPackages;

use App\Filament\Resources\GymPackages\Pages\CreateGymPackage;
use App\Filament\Resources\GymPackages\Pages\EditGymPackage;
use App\Filament\Resources\GymPackages\Pages\ListGymPackages;
use App\Filament\Resources\GymPackages\Schemas\GymPackageForm;
use App\Filament\Resources\GymPackages\Tables\GymPackagesTable;
use App\Models\GymPackage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GymPackageResource extends Resource
{
    protected static ?string $model = GymPackage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GymPackageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GymPackagesTable::configure($table);
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
            'index' => ListGymPackages::route('/'),
            'create' => CreateGymPackage::route('/create'),
            'edit' => EditGymPackage::route('/{record}/edit'),
        ];
    }
}
