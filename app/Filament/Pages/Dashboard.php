<?php

namespace App\Filament\Pages;

use App\Enums\MembershipStatus;
use App\Enums\TransactionStatus;
use App\Models\ClassBooking;
use App\Models\ClassSession;
use App\Models\Membership;
use App\Models\PersonalTrainer;
use App\Models\Transaction;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Ringkasan';

    protected static ?string $navigationLabel = 'Ringkasan';

    protected string $view = 'filament.pages.dashboard';

    public int $summaryMonth;

    public int $summaryYear;

    public int $incomeYear;

    public string $dashboardPeriod = 'month';

    public ?string $dashboardStartDate = null;

    public ?string $dashboardEndDate = null;

    public function mount(): void
    {
        $this->summaryMonth = now()->month;
        $this->summaryYear = now()->year;
        $this->incomeYear = now()->year;
        $this->dashboardStartDate = now()->startOfMonth()->toDateString();
        $this->dashboardEndDate = now()->toDateString();
    }

    public function getHeader(): ?View
    {
        return view('filament.pages.dashboard-header');
    }

    public function getWidgets(): array
    {
        return [];
    }

    public function previousSummaryMonth(): void
    {
        $month = $this->selectedMonth()->subMonth();
        $this->summaryMonth = $month->month;
        $this->summaryYear = $month->year;
    }

    public function nextSummaryMonth(): void
    {
        $month = $this->selectedMonth()->addMonth();
        $this->summaryMonth = $month->month;
        $this->summaryYear = $month->year;
    }

    public function previousIncomeYear(): void
    {
        $this->incomeYear--;
    }

    public function nextIncomeYear(): void
    {
        $this->incomeYear++;
    }

    /**
     * @return array{
     *     stats: array{activeMembers: int, activeTrainers: int, monthlyIncome: int, monthlyBookings: int},
     *     membershipSummary: array{expired: array{count: int, percentage: float}, active: array{count: int, percentage: float}, new: array{count: int, percentage: float}},
     *     membershipPlans: array{total: int, gradient: string, items: list<array{label: string, count: int, color: string, percentage: float, offset: float}>},
     *     instructors: array{total: int, gradient: string, items: list<array{label: string, count: int, color: string, percentage: float, offset: float}>},
     *     income: array{values: list<int>, pointCoordinates: list<array{x: float, y: float, value: int}>, linePath: string, areaPath: string, maximumLabel: string, midpointLabel: string},
     *     todayClasses: Collection<int, ClassSession>,
     *     monthLabel: string,
     *     periodLabel: string
     * }
     */
    public function dashboardData(): array
    {
        $today = Carbon::today();
        [$periodStart, $periodEnd] = $this->dashboardPeriodRange();
        $selectedMonthStart = $this->selectedMonth();
        $selectedMonthEnd = $selectedMonthStart->copy()->endOfMonth();

        $activeMembers = Membership::query()
            ->where('status', MembershipStatus::Active)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        $monthlyIncome = (int) Transaction::query()
            ->where('status', TransactionStatus::Paid)
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->sum('amount');

        $monthlyBookings = ClassBooking::query()
            ->where('status', 'booked')
            ->whereHas('session', fn (Builder $query): Builder => $query
                ->whereBetween('date', [$periodStart, $periodEnd]))
            ->count();

        $expiredMemberships = Membership::query()
            ->whereBetween('end_date', [$selectedMonthStart, $selectedMonthEnd])
            ->whereDate('end_date', '<', $today)
            ->count();
        $activeMemberships = Membership::query()
            ->where('status', MembershipStatus::Active)
            ->whereDate('start_date', '<=', $selectedMonthEnd)
            ->whereDate('end_date', '>=', $selectedMonthStart)
            ->count();
        $newMemberships = Membership::query()
            ->whereBetween('created_at', [$selectedMonthStart, $selectedMonthEnd])
            ->count();
        $membershipBase = max(1, Membership::query()
            ->where('created_at', '<=', $selectedMonthEnd)
            ->count());

        $planCounts = Membership::query()
            ->with('plan:id,name')
            ->whereBetween('created_at', [$selectedMonthStart, $selectedMonthEnd])
            ->get()
            ->countBy(fn (Membership $membership): string => $membership->plan?->name ?? 'Tanpa paket')
            ->sortDesc();

        $instructorCounts = ClassSession::query()
            ->with('gymClass:id,instructor')
            ->whereBetween('date', [$selectedMonthStart, $selectedMonthEnd])
            ->get()
            ->countBy(fn (ClassSession $session): string => $session->gymClass?->instructor ?: 'Tim CukipGym')
            ->sortDesc();

        return [
            'stats' => [
                'activeMembers' => $activeMembers,
                'activeTrainers' => PersonalTrainer::query()->where('is_active', true)->count(),
                'monthlyIncome' => $monthlyIncome,
                'monthlyBookings' => $monthlyBookings,
            ],
            'membershipSummary' => [
                'expired' => ['count' => $expiredMemberships, 'percentage' => round(($expiredMemberships / $membershipBase) * 100, 2)],
                'active' => ['count' => $activeMemberships, 'percentage' => round(($activeMemberships / $membershipBase) * 100, 2)],
                'new' => ['count' => $newMemberships, 'percentage' => round(($newMemberships / $membershipBase) * 100, 2)],
            ],
            'membershipPlans' => $this->distribution($planCounts),
            'instructors' => $this->distribution($instructorCounts),
            'income' => $this->incomeChart(),
            'todayClasses' => ClassSession::query()
                ->with('gymClass')
                ->withCount('activeBookings')
                ->whereDate('date', $today)
                ->orderBy('id')
                ->limit(3)
                ->get(),
            'monthLabel' => $selectedMonthStart->locale('id')->translatedFormat('F Y'),
            'periodLabel' => $this->dashboardPeriodLabel($periodStart, $periodEnd),
        ];
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function dashboardPeriodRange(): array
    {
        $today = Carbon::today();

        return match ($this->dashboardPeriod) {
            'week' => [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()],
            'year' => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
            'custom' => [
                filled($this->dashboardStartDate) ? Carbon::parse($this->dashboardStartDate)->startOfDay() : $today->copy()->startOfMonth(),
                filled($this->dashboardEndDate) ? Carbon::parse($this->dashboardEndDate)->endOfDay() : $today->copy()->endOfDay(),
            ],
            default => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
        };
    }

    private function dashboardPeriodLabel(Carbon $start, Carbon $end): string
    {
        return match ($this->dashboardPeriod) {
            'week' => 'Minggu ini',
            'year' => 'Tahun '.$end->year,
            'custom' => $start->format('d/m/Y').' – '.$end->format('d/m/Y'),
            default => $start->locale('id')->translatedFormat('F Y'),
        };
    }

    private function selectedMonth(): Carbon
    {
        return Carbon::create($this->summaryYear, $this->summaryMonth, 1)->startOfMonth();
    }

    /**
     * @param  Collection<string, int>  $counts
     * @return array{total: int, gradient: string, items: list<array{label: string, count: int, color: string, percentage: float, offset: float}>}
     */
    private function distribution(Collection $counts): array
    {
        $colors = ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444', '#a855f7'];
        $counts = $counts->take(5);
        $total = (int) $counts->sum();

        if ($total === 0) {
            return ['total' => 0, 'gradient' => '#e5e5e5 0 100%', 'items' => []];
        }

        $offset = 0.0;
        $segments = [];
        $items = [];

        foreach ($counts as $label => $count) {
            $color = $colors[count($items)];
            $percentage = round(((int) $count / $total) * 100, 2);
            $nextOffset = $offset + $percentage;
            $segments[] = $color.' '.$offset.'% '.$nextOffset.'%';
            $items[] = [
                'label' => (string) $label,
                'count' => (int) $count,
                'color' => $color,
                'percentage' => $percentage,
                'offset' => round($offset, 2),
            ];
            $offset = $nextOffset;
        }

        return ['total' => $total, 'gradient' => implode(', ', $segments), 'items' => $items];
    }

    /** @return array{values: list<int>, pointCoordinates: list<array{x: float, y: float, value: int}>, linePath: string, areaPath: string, maximumLabel: string, midpointLabel: string} */
    private function incomeChart(): array
    {
        $yearStart = Carbon::create($this->incomeYear, 1, 1)->startOfYear();
        $yearEnd = $yearStart->copy()->endOfYear();
        $values = array_fill(0, 12, 0);

        Transaction::query()
            ->where('status', TransactionStatus::Paid)
            ->whereBetween('paid_at', [$yearStart, $yearEnd])
            ->get(['paid_at', 'amount'])
            ->each(function (Transaction $transaction) use (&$values): void {
                $monthIndex = $transaction->paid_at->month - 1;
                $values[$monthIndex] += $transaction->amount;
            });

        $maximumValue = max($values);
        $chartMaximum = max(1, $maximumValue);
        $chartLeft = 30;
        $chartRight = 690;
        $chartWidth = $chartRight - $chartLeft;
        $pointCoordinates = [];

        foreach ($values as $index => $value) {
            $x = $chartLeft + (($index / 11) * $chartWidth);
            $y = 190 - (($value / $chartMaximum) * 160);
            $pointCoordinates[] = [
                'x' => round($x, 2),
                'y' => round($y, 2),
                'value' => $value,
            ];
        }

        $linePath = 'M '.$pointCoordinates[0]['x'].' '.$pointCoordinates[0]['y'];

        for ($index = 1; $index < count($pointCoordinates); $index++) {
            $previous = $pointCoordinates[$index - 1];
            $current = $pointCoordinates[$index];
            $middleX = round(($previous['x'] + $current['x']) / 2, 2);
            $linePath .= ' C '.$middleX.' '.$previous['y'].', '.$middleX.' '.$current['y'].', '.$current['x'].' '.$current['y'];
        }

        return [
            'values' => $values,
            'pointCoordinates' => $pointCoordinates,
            'linePath' => $linePath,
            'areaPath' => $linePath." L {$chartRight} 190 L {$chartLeft} 190 Z",
            'maximumLabel' => $this->compactRupiah($maximumValue),
            'midpointLabel' => $this->compactRupiah((int) round($maximumValue / 2)),
        ];
    }

    private function compactRupiah(int $amount): string
    {
        if ($amount >= 1_000_000) {
            return 'Rp'.round($amount / 1_000_000, 1).' jt';
        }

        if ($amount >= 1_000) {
            return 'Rp'.round($amount / 1_000, 1).' rb';
        }

        return 'Rp'.number_format($amount, 0, ',', '.');
    }
}
