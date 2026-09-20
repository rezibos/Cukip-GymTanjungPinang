<?php

namespace App\Filament\Widgets;

use App\Models\Review;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestReviews extends TableWidget
{
    protected static ?string $heading = 'Review Terbaru';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Review::query()->with(['user', 'gym'])->latest('id')->limit(5))
            ->columns([
                TextColumn::make('user.name')->label('User'),
                TextColumn::make('gym.name')->label('Gym'),
                TextColumn::make('rating')->label('Rating')->formatStateUsing(fn ($state) => $state . ' / 5'),
                TextColumn::make('review')->label('Komentar')->limit(60),
                TextColumn::make('created_at')->label('Tanggal')->dateTime('d/m/Y'),
            ])
            ->paginated(false);
    }
}