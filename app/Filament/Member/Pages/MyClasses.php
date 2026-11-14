<?php

namespace App\Filament\Member\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;

class MyClasses extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Kelas saya';

    protected static ?string $title = 'Kelas saya';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.member.pages.my-classes';

    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->isMember() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $bookings = Filament::auth()->user()->classBookings()
            ->with('session.gymClass')
            ->where('status', 'booked')
            ->whereHas('session', fn ($query) => $query->whereDate('date', '>=', today()))
            ->get()
            ->sortBy(fn ($booking) => $booking->session->date)
            ->values();

        return ['bookings' => $bookings];
    }
}
