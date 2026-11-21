<?php

namespace App\Filament\Resources\PersonalTrainers;

use App\Filament\Resources\PersonalTrainers\Pages\CreatePersonalTrainer;
use App\Filament\Resources\PersonalTrainers\Pages\EditPersonalTrainer;
use App\Filament\Resources\PersonalTrainers\Pages\ListPersonalTrainers;
use App\Filament\Resources\PersonalTrainers\Schemas\PersonalTrainerForm;
use App\Filament\Resources\PersonalTrainers\Tables\PersonalTrainersTable;
use App\Models\PersonalTrainer;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PersonalTrainerResource extends Resource
{
    protected static ?string $model = PersonalTrainer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Kelas & latihan';

    protected static ?string $navigationLabel = 'Personal Trainer';

    protected static ?string $modelLabel = 'Personal Trainer';

    public static function form(Schema $schema): Schema
    {
        return PersonalTrainerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PersonalTrainersTable::configure($table);
    }

    public static function makeCreateAction(): CreateAction
    {
        return CreateAction::make()
            ->label('Tambah')
            ->icon(Heroicon::Plus)
            ->modal()
            ->modalHeading('Tambah personal trainer')
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalWidth(Width::FourExtraLarge)
            ->extraModalWindowAttributes(['class' => 'personal-trainer-form-modal'])
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
            'index' => ListPersonalTrainers::route('/'),
            'create' => CreatePersonalTrainer::route('/create'),
            'edit' => EditPersonalTrainer::route('/{record}/edit'),
        ];
    }
}
