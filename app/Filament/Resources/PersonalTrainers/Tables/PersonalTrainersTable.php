<?php

namespace App\Filament\Resources\PersonalTrainers\Tables;

use App\Filament\Resources\PersonalTrainers\PersonalTrainerResource;
use App\Models\PersonalTrainer;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PersonalTrainersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('specialization')->label('Spesialisasi')->searchable(),
                TextColumn::make('session_price')->label('Harga sesi')->money('IDR')->sortable(),
                TextColumn::make('session_minutes')->label('Durasi')->formatStateUsing(fn (int $state): string => ($state / 60).' jam'),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->filters([
                //
            ])
            ->selectable()
            ->trackDeselectedRecords(false)
            ->deselectAllRecordsWhenFiltered(false)
            ->searchPlaceholder('Cari nama atau spesialisasi...')
            ->recordUrl(null)
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->mutateRecordDataUsing(fn (array $data, PersonalTrainer $record): array => [
                        ...$data,
                        'certifications' => $record->certificationItems(),
                    ])
                    ->modal()
                    ->modalHeading('Ubah Personal Trainer')
                    ->modalDescription('Perbarui profil dan detail layanan pelatih di bawah ini.')
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelActionLabel('Batal')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalWidth(Width::FourExtraLarge)
                    ->extraModalWindowAttributes(['class' => 'personal-trainer-form-modal'])
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
                DeleteAction::make()->label('Hapus'),
            ])
            ->toolbarActions([
                PersonalTrainerResource::makeCreateAction(),
                DeleteBulkAction::make(),
            ])
            ->recordActionsColumnLabel('Action')
            ->recordActionsAlignment('center');
    }
}
