<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\Review;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
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
                TextColumn::make('name')->label('Nama tampilan')->searchable(),
                TextColumn::make('rating')->label('Rating')->formatStateUsing(fn ($state) => $state.' / 5'),
                TextColumn::make('review')->label('Komentar')->limit(50)->tooltip(fn ($record) => $record->review),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state) => $state === 'approved' ? 'success' : 'danger'),
                TextColumn::make('created_at')->label('Tanggal')->dateTime('d/m/Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->searchPlaceholder('Cari member atau review...')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'approved' => 'Tampil',
                        'rejected' => 'Disembunyikan',
                    ]),
                SelectFilter::make('name')
                    ->label('Nama tampilan')
                    ->options(fn (): array => Review::query()
                        ->whereNotNull('name')
                        ->distinct()
                        ->orderBy('name')
                        ->pluck('name', 'name')
                        ->all())
                    ->searchable(),
                SelectFilter::make('rating')
                    ->label('Rating')
                    ->options([
                        '5' => '5 bintang',
                        '4' => '4 bintang',
                        '3' => '3 bintang',
                        '2' => '2 bintang',
                        '1' => '1 bintang',
                    ]),
            ])
            ->selectable()
            ->trackDeselectedRecords(false)
            ->deselectAllRecordsWhenFiltered(false)
            ->toolbarActions([
                ReviewResource::makeCreateAction(),
            ])
            ->recordActions([
                Action::make('reject')->label('Sembunyikan')->icon('heroicon-o-eye-slash')->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->action(fn ($record) => $record->update(['status' => 'rejected'])),
                Action::make('approve')->label('Tampilkan')->icon('heroicon-o-eye')->color('success')
                    ->visible(fn ($record) => $record->status === 'rejected')
                    ->action(fn ($record) => $record->update(['status' => 'approved'])),
                EditAction::make()
                    ->label('Ubah')
                    ->modal()
                    ->modalHeading('Ubah Review')
                    ->modalDescription('Perbarui status review di bawah ini.')
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
}
