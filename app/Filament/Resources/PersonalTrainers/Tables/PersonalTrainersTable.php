<?php

namespace App\Filament\Resources\PersonalTrainers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
