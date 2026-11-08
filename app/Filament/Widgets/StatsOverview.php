<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ClassBookings\ClassBookingResource;
use App\Filament\Resources\Memberships\MembershipResource;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\ClassBooking;
use App\Models\Membership;
use App\Models\Transaction;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $revenueThisMonth = Transaction::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        return [
            Stat::make('Member Aktif', Membership::where('status', 'active')
                ->whereDate('end_date', '>=', Carbon::today())->count())
                ->description('Membership yang masih berlaku')
                ->icon(Heroicon::OutlinedUsers)
                ->url(MembershipResource::getUrl('index'))
                ->extraAttributes(['class' => 'admin-stat-featured']),
            Stat::make('Perlu Konfirmasi', Transaction::where('status', 'pending')->count())
                ->description('Pembayaran menunggu pemeriksaan')
                ->icon(Heroicon::OutlinedCreditCard)
                ->url(TransactionResource::getUrl('index')),
            Stat::make('Pendapatan Bulan Ini', 'Rp'.number_format($revenueThisMonth, 0, ',', '.'))
                ->description(now()->translatedFormat('F Y').' · pembayaran lunas')
                ->icon(Heroicon::OutlinedBanknotes)
                ->url(TransactionResource::getUrl('index')),
            Stat::make('Booking Kelas Hari Ini', ClassBooking::where('status', 'booked')
                ->whereHas('session', fn (Builder $query): Builder => $query->whereDate('date', Carbon::today()))->count())
                ->description('Peserta terdaftar untuk hari ini')
                ->icon(Heroicon::OutlinedCalendarDays)
                ->url(ClassBookingResource::getUrl('index')),
        ];
    }
}
