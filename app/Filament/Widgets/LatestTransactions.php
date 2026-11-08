<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionStatus;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestTransactions extends TableWidget
{
    protected static ?string $heading = 'Transaksi Terbaru';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Transaction::query()->with(['user', 'membership.plan'])->latest()->limit(5))
            ->columns([
                TextColumn::make('invoice_number')->label('Invoice'),
                TextColumn::make('user.name')->label('Member'),
                TextColumn::make('membership.plan.name')->label('Paket'),
                TextColumn::make('amount')->label('Jumlah')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.')),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (TransactionStatus $state): string => $state->label())
                    ->color(fn (TransactionStatus $state): string => $state === TransactionStatus::Paid ? 'primary' : 'gray'),
            ])
            ->headerActions([
                Action::make('transactions')->label('Semua transaksi')->url(TransactionResource::getUrl('index'))->color('gray')->link(),
            ])
            ->recordUrl(fn (Transaction $record): string => TransactionResource::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('Belum ada transaksi')
            ->emptyStateDescription('Transaksi membership akan ditampilkan setelah member membuat pesanan.')
            ->emptyStateIcon('heroicon-o-credit-card')
            ->paginated(false);
    }
}
