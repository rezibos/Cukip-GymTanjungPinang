<?php

namespace App\Filament\Resources\MembershipPlans\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.')),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('memberships_count')->label('Total Dipakai')->counts('memberships'),
            ])
            ->defaultSort('price')
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}