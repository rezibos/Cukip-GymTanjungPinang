<?php

namespace App\Filament\Widgets;

use App\Enums\Region;
use App\Models\Gym;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestGyms extends TableWidget
{
    protected static ?string $heading = 'Gym Terbaru';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Gym::query()->with('category')->latest('id')->limit(5))
            ->columns([
                TextColumn::make('name')->label('Nama'),
                TextColumn::make('category.name')->label('Kategori'),
                TextColumn::make('region')->label('Wilayah')
                    ->formatStateUsing(fn ($state) => $state instanceof Region ? $state->value : $state),
                TextColumn::make('created_at')->label('Dibuat')->dateTime('d/m/Y'),
            ])
            ->paginated(false);
    }
}