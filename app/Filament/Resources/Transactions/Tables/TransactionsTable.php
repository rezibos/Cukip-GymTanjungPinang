<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'user',
                'payment',
                'membership.plan',
                'trainerBooking.slot.trainer',
            ]))
            ->columns([
                TextColumn::make('invoice_number')->label('Invoice')->searchable(),
                TextColumn::make('user.name')->label('Member')->searchable(),
                TextColumn::make('purchase_name')
                    ->label('Pembelian')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->where(function (Builder $query) use ($search): void {
                        $query
                            ->whereHas('membership.plan', fn (Builder $planQuery): Builder => $planQuery->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('trainerBooking.slot.trainer', fn (Builder $trainerQuery): Builder => $trainerQuery->where('name', 'like', "%{$search}%"));
                    })),
                TextColumn::make('purchase_type')->label('Jenis')->badge(),
                TextColumn::make('quantity')->label('Qty')->alignCenter(),
                TextColumn::make('amount')->label('Jumlah')->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp'.number_format($state, 0, ',', '.')),
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
            ->selectable()
            ->trackDeselectedRecords(false)
            ->deselectAllRecordsWhenFiltered(false)
            ->searchPlaceholder('Cari invoice, member, atau pembelian...')
            ->emptyStateHeading('No Transaksi')
            ->emptyStateDescription(null)
            ->filters([
                Filter::make('period')
                    ->label('Periode transaksi')
                    ->schema([
                        Select::make('range')
                            ->label('Periode transaksi')
                            ->placeholder('Semua waktu')
                            ->options([
                                'week' => 'Minggu ini',
                                'month' => 'Bulan ini',
                                'year' => 'Tahun ini',
                                'custom' => 'Custom',
                            ])
                            ->columnSpanFull()
                            ->live(),
                        DatePicker::make('start_date')
                            ->label('Tanggal mulai')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Pilih tanggal mulai')
                            ->suffixIcon('heroicon-o-calendar-days')
                            ->default(fn (): string => now()->startOfMonth()->toDateString())
                            ->required(fn (Get $get): bool => $get('range') === 'custom')
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => $get('range') === 'custom'),
                        DatePicker::make('end_date')
                            ->label('Tanggal selesai')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Pilih tanggal selesai')
                            ->suffixIcon('heroicon-o-calendar-days')
                            ->default(fn (): string => now()->toDateString())
                            ->minDate(fn (Get $get): ?string => $get('start_date'))
                            ->required(fn (Get $get): bool => $get('range') === 'custom')
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => $get('range') === 'custom'),
                    ])
                    ->columns(2)
                    ->query(fn (Builder $query, array $data): Builder => match ($data['range'] ?? null) {
                        'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                        'month' => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
                        'year' => $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]),
                        'custom' => $query
                            ->where('created_at', '>=', Carbon::parse($data['start_date'])->startOfDay())
                            ->where('created_at', '<=', Carbon::parse($data['end_date'])->endOfDay()),
                        default => $query,
                    })
                    ->indicateUsing(function (array $data): array {
                        return match ($data['range'] ?? null) {
                            'week' => [Indicator::make('Minggu ini')->removeField('range')],
                            'month' => [Indicator::make('Bulan ini')->removeField('range')],
                            'year' => [Indicator::make('Tahun ini')->removeField('range')],
                            'custom' => [Indicator::make(
                                Carbon::parse($data['start_date'])->format('d/m/Y').' – '.Carbon::parse($data['end_date'])->format('d/m/Y'),
                            )->removeField('range')],
                            default => [],
                        };
                    }),
                SelectFilter::make('purchase_type')
                    ->label('Jenis Pembelian')
                    ->options([
                        'membership' => 'Membership',
                        'trainer' => 'Personal trainer',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'membership' => $query->whereNotNull('membership_id'),
                        'trainer' => $query->whereNotNull('trainer_booking_id'),
                        default => $query,
                    }),
                SelectFilter::make('status')->options([
                    'pending' => 'Menunggu Konfirmasi',
                    'paid' => 'Lunas',
                    'rejected' => 'Ditolak',
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->recordActions([
                Action::make('markPaid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Semua transaksi dalam checkout yang sama akan dilunasi dan pembeliannya diaktifkan.')
                    ->visible(fn (Transaction $record) => $record->status === TransactionStatus::Pending)
                    ->action(function (Transaction $record, Component $livewire): void {
                        DB::transaction(function () use ($record): void {
                            foreach ($record->checkoutGroup() as $transaction) {
                                $transaction->update(['status' => TransactionStatus::Paid, 'paid_at' => now()]);
                                $transaction->membership?->activate();

                                if ($transaction->trainerBooking) {
                                    $transaction->trainerBooking->update(['status' => 'confirmed']);
                                    $transaction->trainerBooking->slot->update(['status' => 'booked', 'held_until' => null]);
                                }

                                $transaction->payment?->update(['status' => PaymentStatus::Paid, 'paid_at' => now()]);
                            }
                        });

                        $livewire->dispatch(
                            'filament-sweetalert',
                            title: 'Transaksi berhasil dilunasi',
                            message: 'Pembelian member sudah aktif.',
                            type: 'success',
                        );
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
                    ->action(function (Transaction $record, array $data, Component $livewire): void {
                        DB::transaction(function () use ($record, $data): void {
                            foreach ($record->checkoutGroup() as $transaction) {
                                $transaction->update([
                                    'status' => TransactionStatus::Rejected,
                                    'admin_note' => $data['admin_note'],
                                ]);
                                $transaction->membership?->update(['status' => 'cancelled']);

                                if ($transaction->trainerBooking) {
                                    $transaction->trainerBooking->update(['status' => 'cancelled']);
                                    $transaction->trainerBooking->slot->update(['status' => 'available', 'held_until' => null]);
                                }

                                $transaction->payment?->update(['status' => PaymentStatus::Failed]);
                            }
                        });

                        $livewire->dispatch(
                            'filament-sweetalert',
                            title: 'Transaksi ditolak',
                            message: 'Status transaksi dan pembelian telah diperbarui.',
                            type: 'warning',
                        );
                    }),

                ViewAction::make()
                    ->label('Detail')
                    ->modal()
                    ->modalHeading('Detail Transaksi')
                    ->modalDescription('Periksa detail transaksi di bawah ini.')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->extraAttributes(['class' => 'transaction-view-grid'])
                            ->schema([
                                TextInput::make('invoice_number')
                                    ->label('Nomor invoice')
                                    ->extraAttributes(['class' => 'transaction-view-item']),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        TransactionStatus::Pending->value => 'Menunggu Konfirmasi',
                                        TransactionStatus::Paid->value => 'Lunas',
                                        TransactionStatus::Rejected->value => 'Ditolak',
                                    ])
                                    ->extraAttributes(['class' => 'transaction-view-item']),
                                TextInput::make('user_name')
                                    ->label('Member')
                                    ->extraAttributes(['class' => 'transaction-view-item']),
                                TextInput::make('transaction_date')
                                    ->label('Tanggal transaksi')
                                    ->extraAttributes(['class' => 'transaction-view-item']),
                                TextInput::make('purchase_type')
                                    ->label('Jenis pembelian')
                                    ->extraAttributes(['class' => 'transaction-view-item']),
                                TextInput::make('purchase_name')
                                    ->label('Pembelian')
                                    ->extraAttributes(['class' => 'transaction-view-item']),
                                TextInput::make('formatted_amount')
                                    ->label('Jumlah')
                                    ->extraAttributes(['class' => 'transaction-view-item']),
                                TextInput::make('quantity')
                                    ->label('Kuantitas')
                                    ->extraAttributes(['class' => 'transaction-view-item']),
                                Textarea::make('admin_note')
                                    ->label('Catatan admin')
                                    ->placeholder('Tidak ada catatan admin.')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'transaction-view-item transaction-view-note']),
                            ]),
                    ])
                    ->mutateRecordDataUsing(fn (array $data, Transaction $record): array => [
                        ...$data,
                        'user_name' => $record->user?->name ?? '-',
                        'transaction_date' => $record->created_at?->translatedFormat('d M Y, H:i') ?? '-',
                        'purchase_type' => $record->purchase_type,
                        'purchase_name' => $record->purchase_name,
                        'formatted_amount' => $record->formatted_amount,
                    ])
                    ->modalCancelAction(fn (Action $action): Action => $action->label('Tutup'))
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalWidth(Width::FourExtraLarge)
                    ->extraModalWindowAttributes(['class' => 'transaction-view-modal'])
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
                DeleteAction::make()->label('Hapus'),
            ])
            ->recordActionsColumnLabel('Action')
            ->recordActionsAlignment('center');
    }
}
