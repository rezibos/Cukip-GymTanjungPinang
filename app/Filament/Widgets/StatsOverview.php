<?php

namespace App\Filament\Widgets;

use App\Models\ClassBooking;
use App\Models\Membership;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $revenueThisMonth = Transaction::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        return [
            Stat::make('Member Aktif', Membership::where('status', 'active')
                ->whereDate('end_date', '>=', Carbon::today())->count()),
            Stat::make('Transaksi Menunggu', Transaction::where('status', 'pending')->count()),
            Stat::make('Pendapatan Bulan Ini', 'Rp' . number_format($revenueThisMonth, 0, ',', '.')),
            Stat::make('Booking Kelas Hari Ini', ClassBooking::where('status', 'booked')
                ->whereHas('session', fn ($q) => $q->whereDate('date', Carbon::today()))->count()),
        ];
    }
}