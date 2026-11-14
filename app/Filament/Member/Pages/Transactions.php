<?php

namespace App\Filament\Member\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;

class Transactions extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $title = 'Riwayat transaksi';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.member.pages.transactions';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'transactions' => Filament::auth()->user()->transactions()
                ->with(['membership.plan', 'payment'])
                ->latest()
                ->get(),
        ];
    }
}
