<?php

namespace App\Filament\Resources\TrainerSlots\Tables;

use App\Filament\Resources\TrainerSlots\TrainerSlotResource;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                SelectFilter::make('personal_trainer_id')
                    ->label('Pelatih')
                    ->relationship('trainer', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'held' => 'Ditahan pembayaran',
                        'booked' => 'Sudah dibooking',
                    ]),
            ])
            ->selectable()
            ->trackDeselectedRecords(false)
            ->deselectAllRecordsWhenFiltered(false)
            ->searchPlaceholder('Cari pelatih...')
            ->recordActions([
                EditAction::make()->label('Ubah'),
            ])
            ->toolbarActions([
                TrainerSlotResource::makeCreateAction(),
            ])
            ->recordActionsColumnLabel('Action')
            ->recordActionsAlignment('center');
    }
}
