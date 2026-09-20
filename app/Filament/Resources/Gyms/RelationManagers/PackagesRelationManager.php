<?php

namespace App\Filament\Resources\Gyms\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'packages';

    protected static ?string $title = 'Paket Gym';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama paket')->required()->maxLength(100)
                ->placeholder('Paket Bulanan'),
            TextInput::make('duration')->label('Durasi')->required()->maxLength(50)
                ->placeholder('1 bulan'),
            TextInput::make('price')->label('Harga')->required()
                ->numeric()->integer()->minValue(0)->maxValue(100000000)
                ->prefix('Rp')->helperText('Angka saja, tanpa titik. Contoh: 250000'),
            TextInput::make('description')->label('Keterangan')->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Nama paket'),
                TextColumn::make('duration')->label('Durasi'),
                TextColumn::make('price')->label('Harga')->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.')),
            ])
            ->defaultSort('price')
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}