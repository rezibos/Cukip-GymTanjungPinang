<?php

namespace App\Filament\Member\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;

class Transactions extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $title = 'Riwayat transaksi';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.member.pages.transactions';

    public function getHeading(): null
    {
        return null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $search = trim(request()->string('search')->toString());
        $status = request()->string('status')->toString();
        $type = request()->string('type')->toString();
        $requestedPerPage = request()->integer('per_page', 10);
        $perPage = in_array($requestedPerPage, [5, 10, 25], true) ? $requestedPerPage : 10;

        $allowedStatuses = ['pending', 'paid', 'failed', 'expired', 'refunded'];
        $status = in_array($status, $allowedStatuses, true) ? $status : '';
        $type = in_array($type, ['membership', 'trainer'], true) ? $type : '';

        $payments = Filament::auth()->user()->payments()
            ->with(['transactions.membership.plan', 'trainerBookings.slot.trainer'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $searchQuery) use ($search): void {
                    $searchQuery
                        ->where('order_id', 'like', "%{$search}%")
                        ->orWhere('checkout_items', 'like', "%{$search}%")
                        ->orWhereHas('transactions.membership.plan', fn (Builder $planQuery) => $planQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('trainerBookings.slot.trainer', fn (Builder $trainerQuery) => $trainerQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
            ->when($type === 'trainer', fn (Builder $query) => $query->whereHas('trainerBookings'))
            ->when($type === 'membership', fn (Builder $query) => $query->whereDoesntHave('trainerBookings'))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return [
            'payments' => $payments,
            'search' => $search,
            'statusFilter' => $status,
            'typeFilter' => $type,
            'perPage' => $perPage,
            'activeFilterCount' => (int) ($status !== '') + (int) ($type !== ''),
        ];
    }
}
