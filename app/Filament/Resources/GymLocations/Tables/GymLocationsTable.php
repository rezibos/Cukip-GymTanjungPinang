<?php

namespace App\Filament\Resources\GymLocations\Tables;

use App\Filament\Resources\GymLocations\GymLocationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class GymLocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Foto')
                    ->square(),
                TextColumn::make('name')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('opening_hours')
                    ->label('Jam operasional')
                    ->wrap(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->recordUrl(null)
            ->searchPlaceholder('Cari lokasi gym...')
            ->emptyStateDescription(null)
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->placeholder('Semua'),
            ])
            ->selectable()
            ->trackDeselectedRecords(false)
            ->deselectAllRecordsWhenFiltered(false)
            ->toolbarActions([
                GymLocationResource::makeCreateAction(),
                DeleteBulkAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->modal()
                    ->modalHeading('Ubah Lokasi Gym')
                    ->modalDescription('Perbarui data lokasi gym di bawah ini.')
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
