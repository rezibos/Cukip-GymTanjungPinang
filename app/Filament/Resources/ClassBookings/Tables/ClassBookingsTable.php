<?php

namespace App\Filament\Resources\ClassBookings\Tables;

use App\Enums\BookingStatus;
use App\Models\ClassBooking;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClassBookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User')->searchable(),
                TextColumn::make('session.gymClass.name')->label('Kelas'),
                TextColumn::make('session.date')->label('Tanggal')->date('d/m/Y')->sortable(),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (BookingStatus $state) => $state->label())
                    ->color(fn (BookingStatus $state) => $state === BookingStatus::Booked ? 'success' : 'gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->filters([
                SelectFilter::make('status')->options(['booked' => 'Terdaftar', 'cancelled' => 'Dibatalkan']),
            ])
            ->recordActions([
                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (ClassBooking $record) => $record->status->value === 'booked')
                    ->action(fn (ClassBooking $record) => $record->update(['status' => 'cancelled'])),
                DeleteAction::make(),
            ])
            ->recordActionsColumnLabel('Action')
            ->recordActionsAlignment('center');
    }
}
