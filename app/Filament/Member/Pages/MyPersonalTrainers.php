<?php

namespace App\Filament\Member\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;

class MyPersonalTrainers extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Personal Trainer';

    protected static ?string $title = 'Personal Trainer';

    protected static ?string $slug = 'personal-trainer';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.member.pages.my-personal-trainers';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user?->trainerBookings()
            ->where('status', 'confirmed')
            ->exists() ?? false;
    }

    public function getHeading(): null
    {
        return null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $bookings = Filament::auth()->user()->trainerBookings()
            ->with('slot.trainer')
            ->where('status', 'confirmed')
            ->whereHas('slot', fn ($query) => $query->where('starts_at', '>=', now()))
            ->get()
            ->sortBy(fn ($booking) => $booking->slot->starts_at)
            ->values();

        return ['bookings' => $bookings];
    }
}
