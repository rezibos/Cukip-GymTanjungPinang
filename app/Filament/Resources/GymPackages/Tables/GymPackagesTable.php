<?php

namespace App\Filament\Resources\GymPackages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GymPackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gym.name')->label('Gym')->searchable()->sortable(),
                TextColumn::make('name')->label('Paket')->searchable(),
                TextColumn::make('duration')->label('Durasi'),
                TextColumn::make('price')->label('Harga')->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.')),
            ])
            ->defaultSort('gym_id')
            ->filters([
                SelectFilter::make('gym')->label('Gym')->relationship('gym', 'name')->searchable()->preload(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}