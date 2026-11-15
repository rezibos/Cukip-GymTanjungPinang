<?php

namespace App\Filament\Resources\Facilities\Tables;

use App\Enums\FacilityCategory;
use App\Filament\Resources\Facilities\FacilityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FacilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')->label('Foto')->disk('public')->visibility('public')->square(),
                TextColumn::make('name')->label('Fasilitas')->searchable(),
                TextColumn::make('category')->label('Kategori')->formatStateUsing(fn (FacilityCategory $state): string => $state->label())->badge(),
                TextColumn::make('description')->label('Keterangan')->placeholder('-'),
            ])
            ->recordUrl(null)
            ->searchPlaceholder('Cari fasilitas...')
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(FacilityCategory::options()),
            ])
            ->selectable()
            ->trackDeselectedRecords(false)
            ->deselectAllRecordsWhenFiltered(false)
            ->toolbarActions([
                FacilityResource::makeCreateAction(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->modal()
                    ->modalHeading('Ubah Fasilitas')
                    ->modalDescription('Perbarui data fasilitas di bawah ini.')
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
