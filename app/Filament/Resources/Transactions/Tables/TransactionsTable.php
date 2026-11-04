<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')->label('Invoice')->searchable(),
                TextColumn::make('user.name')->label('User')->searchable(),
                TextColumn::make('membership.plan.name')->label('Paket'),
                TextColumn::make('amount')->label('Jumlah')->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.')),
                IconColumn::make('payment_proof')->label('Bukti')->boolean()
                    ->getStateUsing(fn (Transaction $record) => (bool) $record->payment_proof),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (TransactionStatus $state) => match ($state) {
                        TransactionStatus::Paid => 'success',
                        TransactionStatus::Rejected => 'danger',
                        TransactionStatus::Pending => 'warning',
                    })
                    ->formatStateUsing(fn (TransactionStatus $state) => $state->label()),
                TextColumn::make('created_at')->label('Tanggal')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Menunggu Konfirmasi',
                    'paid' => 'Lunas',
                    'rejected' => 'Ditolak',
                ]),
            ])
            ->recordActions([
                Action::make('markPaid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Membership user akan otomatis diaktifkan setelah ini.')
                    ->visible(fn (Transaction $record) => $record->status === TransactionStatus::Pending)
                    ->action(function (Transaction $record) {
                        $record->update(['status' => TransactionStatus::Paid, 'paid_at' => now()]);
                        $record->membership->activate();

                        Notification::make()
                            ->title('Transaksi ditandai lunas, membership user telah aktif')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('admin_note')->label('Alasan penolakan')->required(),
                    ])
                    ->visible(fn (Transaction $record) => $record->status === TransactionStatus::Pending)
                    ->action(function (Transaction $record, array $data) {
                        $record->update([
                            'status' => TransactionStatus::Rejected,
                            'admin_note' => $data['admin_note'],
                        ]);
                        $record->membership->update(['status' => 'cancelled']);

                        Notification::make()->title('Transaksi ditolak')->warning()->send();
                    }),

                EditAction::make()->label('Detail'),
                DeleteAction::make(),
            ]);
    }
}