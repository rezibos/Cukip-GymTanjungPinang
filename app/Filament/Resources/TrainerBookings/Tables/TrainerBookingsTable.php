<?php

namespace App\Filament\Resources\TrainerBookings\Tables;

use App\Enums\PaymentStatus;
use App\Filament\Resources\TrainerBookings\TrainerBookingResource;
use App\Models\TrainerBooking;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TrainerBookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'user',
                'slot.trainer',
                'payment',
                'transaction',
            ]))
            ->columns([
                TextColumn::make('user.name')->label('User')->searchable(),
                TextColumn::make('slot.trainer.name')->label('Personal trainer')->searchable(),
                TextColumn::make('payment_method')
                    ->label('Metode pembayaran')
                    ->getStateUsing(fn (TrainerBooking $record): string => self::paymentMethodLabel($record)),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (string $state): string => self::bookingStatusLabel($state))
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending_payment' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('slot.starts_at')->label('Mulai')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('slot.ends_at')->label('Berakhir')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->selectable()
            ->trackDeselectedRecords(false)
            ->deselectAllRecordsWhenFiltered(false)
            ->searchPlaceholder('Cari invoice, member, atau pelatih...')
            ->emptyStateHeading('No Booking Personal Trainer')
            ->emptyStateDescription(null)
            ->filters([
                SelectFilter::make('status')->label('Status booking')->options([
                    'pending_payment' => 'Menunggu pembayaran',
                    'confirmed' => 'Terkonfirmasi',
                    'cancelled' => 'Dibatalkan',
                ]),
                SelectFilter::make('payment_status')
                    ->label('Status pembayaran')
                    ->options([
                        PaymentStatus::Pending->value => 'Menunggu pembayaran',
                        PaymentStatus::Paid->value => 'Lunas',
                        PaymentStatus::Failed->value => 'Gagal',
                        PaymentStatus::Expired->value => 'Kedaluwarsa',
                        PaymentStatus::Refunded->value => 'Dikembalikan',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->whereHas('payment', fn (Builder $paymentQuery): Builder => $paymentQuery->where('status', $data['value']))
                        : $query),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail Booking Personal Trainer')
                    ->modalDescription('Periksa jadwal dan pembayaran booking personal trainer.')
                    ->schema([
                        Grid::make(['default' => 1, 'md' => 2])
                            ->schema([
                                TextInput::make('invoice')->label('Invoice'),
                                TextInput::make('member')->label('Member'),
                                TextInput::make('trainer')->label('Personal trainer'),
                                TextInput::make('schedule')->label('Jadwal'),
                                TextInput::make('formatted_amount')->label('Jumlah'),
                                TextInput::make('payment_method')->label('Metode pembayaran'),
                                TextInput::make('payment_status')->label('Status pembayaran'),
                                TextInput::make('booking_status')->label('Status booking'),
                            ]),
                    ])
                    ->mutateRecordDataUsing(fn (array $data, TrainerBooking $record): array => [
                        ...$data,
                        'invoice' => $record->transaction?->invoice_number ?? '-',
                        'member' => $record->user?->name ?? '-',
                        'trainer' => $record->slot?->trainer?->name ?? '-',
                        'schedule' => $record->slot?->starts_at?->translatedFormat('d M Y, H:i') ?? '-',
                        'formatted_amount' => 'Rp'.number_format($record->amount, 0, ',', '.'),
                        'payment_method' => self::paymentMethodLabel($record),
                        'payment_status' => $record->payment?->status?->label() ?? '-',
                        'booking_status' => self::bookingStatusLabel($record->status),
                    ])
                    ->modalCancelAction(fn (Action $action): Action => $action->label('Tutup'))
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalWidth(Width::FourExtraLarge)
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                TrainerBookingResource::makeCreateAction(),
                DeleteBulkAction::make(),
            ])
            ->recordActionsColumnLabel('Action')
            ->recordActionsAlignment('center');
    }

    private static function bookingStatusLabel(string $status): string
    {
        return match ($status) {
            'pending_payment' => 'Menunggu pembayaran',
            'confirmed' => 'Terkonfirmasi',
            'cancelled' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    private static function paymentMethodLabel(TrainerBooking $booking): string
    {
        return match ($booking->transaction?->payment_method) {
            'manual' => 'Manual · '.match ($booking->transaction?->payment_channel) {
                'cash' => 'Tunai',
                'qris' => 'QRIS',
                'bank_transfer' => 'Transfer Bank',
                default => '-',
            },
            'midtrans' => 'Otomatis · Midtrans',
            default => $booking->payment?->payment_type !== null ? 'Otomatis · Midtrans' : '-',
        };
    }
}
