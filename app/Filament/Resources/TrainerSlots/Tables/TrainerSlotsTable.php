<?php

namespace App\Filament\Resources\TrainerSlots\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainerSlotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trainer.name')->label('Pelatih')->searchable()->sortable(),
                TextColumn::make('starts_at')->label('Mulai')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('ends_at')->label('Selesai')->dateTime('H:i'),
                TextColumn::make('status')->label('Status')->badge(),
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
