<?php

namespace App\Filament\Resources\TrainerSlots;

use App\Filament\Resources\TrainerSlots\Pages\CreateTrainerSlot;
use App\Filament\Resources\TrainerSlots\Pages\EditTrainerSlot;
use App\Filament\Resources\TrainerSlots\Pages\ListTrainerSlots;
use App\Filament\Resources\TrainerSlots\Schemas\TrainerSlotForm;
use App\Filament\Resources\TrainerSlots\Tables\TrainerSlotsTable;
use App\Models\TrainerSlot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TrainerSlotResource extends Resource
{
    protected static ?string $model = TrainerSlot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Kelas & latihan';

    protected static ?string $navigationLabel = 'Jadwal personal trainer';

    protected static ?string $modelLabel = 'Jadwal trainer';

    public static function form(Schema $schema): Schema
    {
        return TrainerSlotForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainerSlotsTable::configure($table);
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
            'index' => ListTrainerSlots::route('/'),
            'create' => CreateTrainerSlot::route('/create'),
            'edit' => EditTrainerSlot::route('/{record}/edit'),
        ];
    }
}
