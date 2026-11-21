<?php

namespace App\Filament\Resources\TrainerSlots\Tables;

use App\Filament\Resources\TrainerSlots\TrainerSlotResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
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
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Tersedia',
                        'held' => 'Menunggu pembayaran',
                        'booked' => 'Sudah dibooking',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'held' => 'warning',
                        default => 'gray',
                    }),
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
            ->emptyStateDescription(null)
            ->recordUrl(null)
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->modal()
                    ->modalHeading('Ubah Jadwal Personal Trainer')
                    ->modalDescription('Perbarui jadwal personal trainer di bawah ini.')
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelActionLabel('Batal')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalWidth(Width::FourExtraLarge)
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                TrainerSlotResource::makeCreateAction(),
                DeleteBulkAction::make(),
            ])
            ->recordActionsColumnLabel('Action')
            ->recordActionsAlignment('center');
    }
}
