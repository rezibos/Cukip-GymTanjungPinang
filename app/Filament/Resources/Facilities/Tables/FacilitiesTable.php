<?php

namespace App\Filament\Resources\Facilities\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FacilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Fasilitas')->searchable(),
                TextColumn::make('description')->label('Keterangan')->placeholder('-'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}