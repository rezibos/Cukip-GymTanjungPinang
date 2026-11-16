<?php

namespace App\Filament\Member\Pages;

use App\Support\MemberAccessCode;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Ringkasan';

    protected static ?string $navigationLabel = 'Ringkasan';

    protected string $view = 'filament.member.pages.dashboard';

    public function getHeading(): null
    {
        return null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();
        $activeMembership = $user->activeMembership()?->load('plan');
        $latestMembership = $activeMembership ?? $user->memberships()
            ->with('plan')
            ->latest('end_date')
            ->latest('id')
            ->first();
        $accessCode = $activeMembership
            ? MemberAccessCode::make($user, $activeMembership)
            : null;
        $upcomingBookingsQuery = $user->classBookings()
            ->with('session.gymClass')
            ->where('status', 'booked')
            ->whereHas('session', fn ($query) => $query->whereDate('date', '>=', today()));
        $upcomingBookings = (clone $upcomingBookingsQuery)
            ->get();
        $upcomingBookings = $upcomingBookings
            ->sortBy(fn ($booking) => $booking->session->date)
            ->take(6)
            ->values();
        $upcomingTrainerBookingsQuery = $user->trainerBookings()
            ->with('slot.trainer')
            ->where('status', 'confirmed')
            ->whereHas('slot', fn ($query) => $query->where('starts_at', '>=', now()));
        $upcomingTrainerBookings = (clone $upcomingTrainerBookingsQuery)
            ->get()
            ->sortBy(fn ($booking) => $booking->slot->starts_at)
            ->take(6)
            ->values();

        return [
            'user' => $user,
            'activeMembership' => $activeMembership,
            'latestMembership' => $latestMembership,
            'accessCode' => $accessCode,
            'qrCells' => $accessCode ? MemberAccessCode::cells($accessCode) : [],
            'upcomingBookings' => $upcomingBookings,
            'upcomingTrainerBookings' => $upcomingTrainerBookings,
            'upcomingBookingsCount' => (clone $upcomingBookingsQuery)->count(),
            'upcomingTrainerBookingsCount' => (clone $upcomingTrainerBookingsQuery)->count(),
            'monthlyBookingsCount' => $user->classBookings()
                ->where('status', 'booked')
                ->whereHas('session', fn ($query) => $query
                    ->whereDate('date', '>=', now()->startOfMonth())
                    ->whereDate('date', '<=', now()->endOfMonth()))
                ->count(),
            'remainingDays' => $activeMembership
                ? max(0, (int) today()->diffInDays($activeMembership->end_date, false))
                : 0,
        ];
    }
}
