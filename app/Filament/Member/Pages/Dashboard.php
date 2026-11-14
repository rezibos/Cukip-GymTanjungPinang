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
        $upcomingBookings = $user->classBookings()
            ->with('session.gymClass')
            ->where('status', 'booked')
            ->whereHas('session', fn ($query) => $query->whereDate('date', '>=', today()))
            ->latest('id')
            ->limit(3)
            ->get();
        $recentTransactions = $user->transactions()
            ->with('membership.plan')
            ->latest()
            ->limit(4)
            ->get();

        return [
            'user' => $user,
            'activeMembership' => $activeMembership,
            'latestMembership' => $latestMembership,
            'accessCode' => $accessCode,
            'qrCells' => $accessCode ? MemberAccessCode::cells($accessCode) : [],
            'upcomingBookings' => $upcomingBookings,
            'recentTransactions' => $recentTransactions,
            'upcomingBookingsCount' => $user->classBookings()
                ->where('status', 'booked')
                ->whereHas('session', fn ($query) => $query->whereDate('date', '>=', today()))
                ->count(),
            'transactionsCount' => $user->transactions()->count(),
            'remainingDays' => $activeMembership
                ? max(0, (int) today()->diffInDays($activeMembership->end_date, false))
                : 0,
        ];
    }
}
