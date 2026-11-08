<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ClassSessions\ClassSessionResource;
use App\Models\ClassSession;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TodayClasses extends TableWidget
{
    protected static ?string $heading = 'Jadwal hari ini';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ClassSession::query()
                ->with('gymClass')
                ->withCount('activeBookings')
                ->whereDate('date', today())
                ->orderBy('id')
                ->limit(6))
            ->columns([
                TextColumn::make('gymClass.name')->label('Kelas')
                    ->description(fn (ClassSession $record): string => $record->gymClass->time_range),
                TextColumn::make('gymClass.instructor')->label('Instruktur')->placeholder('Tim CukipGym'),
                TextColumn::make('active_bookings_count')->label('Peserta')
                    ->formatStateUsing(fn (int $state, ClassSession $record): string => $state.' / '.$record->capacity),
            ])
            ->headerActions([
                Action::make('schedule')->label('Semua sesi')->url(ClassSessionResource::getUrl('index'))->color('gray')->link(),
            ])
            ->recordUrl(fn (ClassSession $record): string => ClassSessionResource::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('Belum ada sesi hari ini')
            ->emptyStateDescription('Sesi yang dijadwalkan hari ini akan muncul di sini.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->paginated(false);
    }
}
