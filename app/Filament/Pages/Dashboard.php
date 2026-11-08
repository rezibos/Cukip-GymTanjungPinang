<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LatestTransactions;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TodayClasses;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Ringkasan';

    protected static ?string $navigationLabel = 'Ringkasan';

    protected string $view = 'filament.pages.dashboard';

    public function getHeader(): ?View
    {
        return view('filament.pages.dashboard-header');
    }

    public function getWidgets(): array
    {
        return [StatsOverview::class, RevenueChart::class, TodayClasses::class, LatestTransactions::class];
    }
}
