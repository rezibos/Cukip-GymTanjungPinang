<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')->label('Invoice')->searchable(),
                TextColumn::make('user.name')->label('Member')->searchable(),
                TextColumn::make('membership.plan.name')->label('Paket'),
                TextColumn::make('quantity')->label('Qty')->alignCenter(),
                TextColumn::make('amount')->label('Jumlah')->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp'.number_format($state, 0, ',', '.')),
                TextColumn::make('payment_proof')->label('Bukti transfer')->badge()
                    ->getStateUsing(fn (Transaction $record): string => $record->payment_proof ? 'Diunggah' : 'Belum ada')
                    ->color('gray'),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (TransactionStatus $state) => match ($state) {
                        TransactionStatus::Paid => 'primary',
                        TransactionStatus::Rejected => 'danger',
                        TransactionStatus::Pending => 'gray',
                    })
                    ->formatStateUsing(fn (TransactionStatus $state) => $state->label()),
                TextColumn::make('created_at')->label('Tanggal')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->searchPlaceholder('Cari invoice atau member...')
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
                    ->modalDescription('Semua transaksi dalam checkout yang sama akan dilunasi dan membership-nya diaktifkan.')
                    ->visible(fn (Transaction $record) => $record->status === TransactionStatus::Pending)
                    ->action(function (Transaction $record) {
                        DB::transaction(function () use ($record): void {
                            foreach ($record->checkoutGroup() as $transaction) {
                                $transaction->update(['status' => TransactionStatus::Paid, 'paid_at' => now()]);
                                $transaction->membership->activate();
                            }
                        });

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
                        DB::transaction(function () use ($record, $data): void {
                            foreach ($record->checkoutGroup() as $transaction) {
                                $transaction->update([
                                    'status' => TransactionStatus::Rejected,
                                    'admin_note' => $data['admin_note'],
                                ]);
                                $transaction->membership->update(['status' => 'cancelled']);
                            }
                        });

                        Notification::make()->title('Transaksi ditolak')->warning()->send();
                    }),

                EditAction::make()
                    ->label('Detail')
                    ->modal()
                    ->modalHeading('Detail Transaksi')
                    ->modalDescription('Periksa dan perbarui transaksi di bawah ini.')
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelActionLabel('Batal')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalWidth(Width::FourExtraLarge)
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
                DeleteAction::make()->label('Hapus'),
            ])
            ->recordActionsColumnLabel('Action')
            ->recordActionsAlignment('center');
    }
}
