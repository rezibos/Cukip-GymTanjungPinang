<?php

namespace App\Filament\Resources\ClassSessions\Tables;

use App\Models\ClassSession;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClassSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->label('Tanggal')->date('l, d/m/Y')->sortable(),
                TextColumn::make('gymClass.name')->label('Kelas')->searchable(),
                TextColumn::make('capacity')->label('Kapasitas'),
                TextColumn::make('active_bookings_count')->label('Terisi')
                    ->counts(['bookings' => fn ($q) => $q->where('status', 'booked')]),
            ])
            ->defaultSort('date')
            ->filters([
                SelectFilter::make('gym_class_id')->label('Kelas')->relationship('gymClass', 'name'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}