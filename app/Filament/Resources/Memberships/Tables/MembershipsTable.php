<?php

namespace App\Filament\Resources\Memberships\Tables;

use App\Enums\MembershipStatus;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MembershipsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User')->searchable(),
                TextColumn::make('plan.name')->label('Paket'),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (MembershipStatus $state) => match ($state) {
                        MembershipStatus::Active => 'success',
                        MembershipStatus::Expired, MembershipStatus::Cancelled => 'danger',
                        MembershipStatus::PendingPayment => 'warning',
                    })
                    ->formatStateUsing(fn (MembershipStatus $state) => $state->label()),
                TextColumn::make('start_date')->label('Mulai')->date('d/m/Y')->placeholder('-'),
                TextColumn::make('end_date')->label('Berakhir')->date('d/m/Y')->placeholder('-')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'pending_payment' => 'Menunggu Pembayaran',
                    'active' => 'Aktif',
                    'expired' => 'Kedaluwarsa',
                    'cancelled' => 'Dibatalkan',
                ]),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}