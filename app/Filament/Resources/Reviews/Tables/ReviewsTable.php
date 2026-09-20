<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Models\Review;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User')->searchable(),
                TextColumn::make('gym.name')->label('Gym')->searchable()->sortable(),
                TextColumn::make('rating')->label('Rating')->sortable()
                    ->formatStateUsing(fn ($state) => $state . ' / 5'),
                TextColumn::make('review')->label('Komentar')->limit(50)->tooltip(fn ($record) => $record->review),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state) => $state === Review::STATUS_APPROVED ? 'success' : 'danger'),
                TextColumn::make('created_at')->label('Tanggal')->dateTime('d/m/Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    Review::STATUS_APPROVED => 'approved',
                    Review::STATUS_REJECTED => 'rejected',
                ]),
                SelectFilter::make('gym')->label('Gym')->relationship('gym', 'name')->searchable()->preload(),
            ])
            ->recordActions([
                Action::make('reject')
                    ->label('Sembunyikan')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Review $record) => $record->status === Review::STATUS_APPROVED)
                    ->action(fn (Review $record) => $record->update(['status' => Review::STATUS_REJECTED])),

                Action::make('approve')
                    ->label('Tampilkan')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->visible(fn (Review $record) => $record->status === Review::STATUS_REJECTED)
                    ->action(fn (Review $record) => $record->update(['status' => Review::STATUS_APPROVED])),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}