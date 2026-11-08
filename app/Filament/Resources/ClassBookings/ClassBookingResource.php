<?php

namespace App\Filament\Resources\ClassBookings;

use App\Filament\Resources\ClassBookings\Pages\CreateClassBooking;
use App\Filament\Resources\ClassBookings\Pages\EditClassBooking;
use App\Filament\Resources\ClassBookings\Pages\ListClassBookings;
use App\Filament\Resources\ClassBookings\Schemas\ClassBookingForm;
use App\Filament\Resources\ClassBookings\Tables\ClassBookingsTable;
use App\Models\ClassBooking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClassBookingResource extends Resource
{
    protected static ?string $model = ClassBooking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Booking kelas';

    protected static ?string $modelLabel = 'Booking kelas';

    protected static ?string $pluralModelLabel = 'Booking kelas';

    protected static string|\UnitEnum|null $navigationGroup = 'Kelas & latihan';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return ClassBookingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassBookingsTable::configure($table);
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
            'index' => ListClassBookings::route('/'),
            'create' => CreateClassBooking::route('/create'),
            'edit' => EditClassBooking::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
