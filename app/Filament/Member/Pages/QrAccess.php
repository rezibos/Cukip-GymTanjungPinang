<?php

namespace App\Filament\Member\Pages;

use App\Support\MemberAccessCode;
use Filament\Facades\Filament;
use Filament\Pages\Page;

class QrAccess extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationLabel = 'QR akses';

    protected static ?string $title = 'QR akses pintu';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.member.pages.qr-access';

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

        return [
            'activeMembership' => $activeMembership,
            'latestMembership' => $latestMembership,
            'accessCode' => $accessCode,
            'qrCells' => $accessCode ? MemberAccessCode::cells($accessCode) : [],
        ];
    }
}
