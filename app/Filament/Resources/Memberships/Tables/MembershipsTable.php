<?php

namespace App\Filament\Resources\Memberships\Tables;

use App\Enums\MembershipStatus;
use App\Filament\Resources\Memberships\MembershipResource;
use App\Models\Membership;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MembershipsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'latestTransaction.payment',
            ]))
            ->columns([
                TextColumn::make('user.name')->label('User')->searchable(),
                TextColumn::make('plan.name')->label('Paket'),
                TextColumn::make('payment_method')
                    ->label('Metode pembayaran')
                    ->getStateUsing(fn (Membership $record): string => self::paymentMethodLabel($record)),
                TextColumn::make('status')->label('Status')->badge()
                    ->getStateUsing(fn (Membership $record): MembershipStatus => $record->effectiveStatus())
                    ->color(fn (MembershipStatus $state) => match ($state) {
                        MembershipStatus::Active => 'success',
                        MembershipStatus::Expired, MembershipStatus::Cancelled => 'danger',
                        MembershipStatus::PendingPayment => 'warning',
                        MembershipStatus::Upcoming => 'info',
                    })
                    ->formatStateUsing(fn (MembershipStatus $state) => $state->label()),
                TextColumn::make('start_date')->label('Mulai')->date('d/m/Y')->placeholder('-'),
                TextColumn::make('end_date')->label('Berakhir')->date('d/m/Y')->placeholder('-')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->selectable()
            ->trackDeselectedRecords(false)
            ->deselectAllRecordsWhenFiltered(false)
            ->searchPlaceholder('Cari nama...')
            ->emptyStateHeading('No Membership')
            ->emptyStateDescription(null)
            ->filters([
                SelectFilter::make('status')->options([
                    'pending_payment' => 'Menunggu Pembayaran',
                    'upcoming' => 'Belum Mulai',
                    'active' => 'Aktif',
                    'expired' => 'Kedaluwarsa',
                    'cancelled' => 'Dibatalkan',
                ]),
            ])
            ->toolbarActions([
                MembershipResource::makeCreateAction(),
                DeleteBulkAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->modal()
                    ->modalHeading('Ubah Membership')
                    ->modalDescription('Perbarui detail membership di bawah ini.')
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelActionLabel('Batal')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalWidth(Width::FourExtraLarge)
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
                DeleteAction::make(),
            ])
            ->recordActionsColumnLabel('Action')
            ->recordActionsAlignment('center');
    }

    private static function paymentMethodLabel(Membership $membership): string
    {
        $transaction = $membership->latestTransaction;

        return match ($transaction?->payment_method) {
            'manual' => 'Manual · '.match ($transaction->payment_channel) {
                'cash' => 'Tunai',
                'qris' => 'QRIS',
                'bank_transfer' => 'Transfer Bank',
                default => '-',
            },
            'midtrans' => 'Otomatis · Midtrans',
            default => $transaction?->payment?->payment_type !== null ? 'Otomatis · Midtrans' : '-',
        };
    }
}
