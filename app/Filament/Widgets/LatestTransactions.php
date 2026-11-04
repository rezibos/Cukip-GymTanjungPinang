<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestTransactions extends TableWidget
{
    protected static ?string $heading = 'Transaksi Terbaru';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Transaction::query()->with(['user', 'membership.plan'])->latest()->limit(5))
            ->columns([
                TextColumn::make('invoice_number')->label('Invoice'),
                TextColumn::make('user.name')->label('User'),
                TextColumn::make('membership.plan.name')->label('Paket'),
                TextColumn::make('amount')->label('Jumlah')
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.')),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn ($state) => match ($state->value) {
                        'paid' => 'success', 'rejected' => 'danger', default => 'warning',
                    }),
            ])
            ->paginated(false);
    }
}