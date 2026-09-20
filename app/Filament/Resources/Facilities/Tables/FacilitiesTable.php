<?php

namespace App\Filament\Resources\Facilities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FacilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gym.name')->label('Gym')->searchable()->sortable(),
                TextColumn::make('name')->label('Fasilitas')->searchable(),
                TextColumn::make('description')->label('Keterangan')->placeholder('-')->toggleable(),
            ])
            ->defaultSort('gym_id')
            ->filters([
                SelectFilter::make('gym')->label('Gym')->relationship('gym', 'name')->searchable()->preload(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}