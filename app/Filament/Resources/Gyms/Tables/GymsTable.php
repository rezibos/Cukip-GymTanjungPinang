<?php

namespace App\Filament\Resources\Gyms\Tables;

use App\Enums\Region;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GymsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Foto')->disk('public')->square(),

                TextColumn::make('name')->label('Nama')->searchable()->sortable(),

                TextColumn::make('category.name')->label('Kategori')->sortable(),

                TextColumn::make('region')
                    ->label('Wilayah')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof Region ? $state->value : $state)
                    ->sortable(),

                TextColumn::make('approved_reviews_avg_rating')
                    ->label('Rating')
                    ->avg('approvedReviews', 'rating')
                    ->numeric(1)
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('region')->label('Wilayah')->options(Region::options()),
                SelectFilter::make('category')->label('Kategori')->relationship('category', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}