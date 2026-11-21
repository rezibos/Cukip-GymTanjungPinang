<?php

namespace App\Filament\Resources\ClassSessions\Tables;

use App\Filament\Resources\ClassSessions\ClassSessionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClassSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->label('Tanggal')->date('l, d/m/Y')->sortable(),
                TextColumn::make('gymClass.name')->label('Kelas')->searchable(),
                TextColumn::make('capacity')->label('Kapasitas'),
                TextColumn::make('active_bookings_count')->label('Terisi')
                    ->counts(['bookings as active_bookings_count' => fn ($q) => $q->where('status', 'booked')])
                    ->formatStateUsing(fn (?int $state): int|string => $state > 0 ? $state : '-'),
            ])
            ->defaultSort('date')
            ->recordUrl(null)
            ->searchPlaceholder('Cari kelas...')
            ->filters([
                SelectFilter::make('gym_class_id')->label('Kelas')->relationship('gymClass', 'name'),
            ])
            ->selectable()
            ->trackDeselectedRecords(false)
            ->deselectAllRecordsWhenFiltered(false)
            ->toolbarActions([
                ClassSessionResource::makeCreateAction(),
                DeleteBulkAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->modal()
                    ->modalHeading('Ubah Sesi Kelas')
                    ->modalDescription('Perbarui jadwal sesi kelas di bawah ini.')
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelActionLabel('Batal')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalWidth(Width::FourExtraLarge)
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
                DeleteAction::make(),
            ])
            ->recordActionsColumnLabel('Action')
            ->recordActionsAlignment('center');
    }
}
