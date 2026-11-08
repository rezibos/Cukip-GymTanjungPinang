<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Arus pendapatan';

    protected ?string $description = 'Pembayaran lunas selama 14 hari terakhir, dalam rupiah.';

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '280px';

    protected ?array $options = [
        'plugins' => ['legend' => ['display' => false]],
        'scales' => [
            'y' => ['beginAtZero' => true, 'ticks' => ['maxTicksLimit' => 5]],
            'x' => ['grid' => ['display' => false], 'ticks' => ['maxTicksLimit' => 7]],
        ],
    ];

    /** @return array{datasets: list<array<string, mixed>>, labels: list<string>} */
    protected function getData(): array
    {
        $start = today()->subDays(13);
        $payments = Transaction::query()
            ->where('status', TransactionStatus::Paid)
            ->where('paid_at', '>=', $start)
            ->where('paid_at', '<', today()->addDay())
            ->get(['paid_at', 'amount']);
        $dailyRevenue = [];
        $labels = [];

        for ($offset = 0; $offset < 14; $offset++) {
            $date = $start->copy()->addDays($offset);
            $dailyRevenue[$date->toDateString()] = 0;
            $labels[] = $date->format('d/m');
        }

        foreach ($payments as $payment) {
            $dailyRevenue[$payment->paid_at->toDateString()] += $payment->amount;
        }

        return [
            'datasets' => [[
                'label' => 'Pendapatan',
                'data' => array_values($dailyRevenue),
                'borderColor' => '#171717',
                'backgroundColor' => 'rgba(23, 23, 23, 0.06)',
                'borderWidth' => 2,
                'pointRadius' => 2,
                'pointHoverRadius' => 5,
                'tension' => 0.35,
                'fill' => true,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
