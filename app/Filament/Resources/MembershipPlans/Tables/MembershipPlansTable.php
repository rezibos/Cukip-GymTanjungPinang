<?php

namespace App\Filament\Resources\MembershipPlans\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MembershipPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Paket')->searchable(),
                TextColumn::make('duration_days')->label('Durasi')->suffix(' hari')->sortable(),
                TextColumn::make('price')->label('Harga')->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp'.number_format($state, 0, ',', '.')),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('memberships_count')->label('Total Dipakai')->counts('memberships'),
            ])
            ->defaultSort('price')
            ->recordUrl(null)
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->modal()
                    ->modalHeading('Ubah Paket Membership')
                    ->modalDescription('Perbarui data paket membership di bawah ini.')
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
