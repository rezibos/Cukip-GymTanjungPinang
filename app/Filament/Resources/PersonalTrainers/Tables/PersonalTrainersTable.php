<?php

namespace App\Filament\Resources\PersonalTrainers\Tables;

use App\Filament\Resources\PersonalTrainers\PersonalTrainerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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
            ->recordActions([
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->toolbarActions([
                PersonalTrainerResource::makeCreateAction(),
            ])
            ->recordActionsColumnLabel('Action')
            ->recordActionsAlignment('center');
    }
}
