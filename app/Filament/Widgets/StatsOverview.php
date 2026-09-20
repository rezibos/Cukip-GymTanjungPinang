<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Gym;
use App\Models\GymPackage;
use App\Models\Review;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Gym', Gym::count()),
            Stat::make('Total Kategori', Category::count()),
            Stat::make('Total Paket', GymPackage::count()),
            Stat::make('Total Review', Review::count()),
            Stat::make('Total User', User::count()),
        ];
    }
}