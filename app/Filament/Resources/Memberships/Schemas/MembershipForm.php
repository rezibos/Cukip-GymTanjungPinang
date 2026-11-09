<?php

namespace App\Filament\Resources\Memberships\Schemas;

use App\Enums\MembershipStatus;
use App\Models\MembershipPlan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class MembershipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')->label('User')->relationship('user', 'name')->required()->searchable()->preload(),
            Select::make('membership_plan_id')->label('Paket')->relationship('plan', 'name')->required()->searchable()->preload()
                ->live()
                ->afterStateUpdated(function (Set $set, mixed $state): void {
                    if (blank($state)) {
                        $set('start_date', null);
                        $set('end_date', null);

                        return;
                    }

                    $startDate = Carbon::today();

                    $set('start_date', $startDate->toDateString());
                    $set('end_date', self::calculateEndDate($state, $startDate));
                }),
            Select::make('status')->label('Status')
                ->options([
                    MembershipStatus::PendingPayment->value => 'Menunggu Pembayaran',
                    MembershipStatus::Upcoming->value => 'Belum Mulai',
                    MembershipStatus::Active->value => 'Aktif',
                    MembershipStatus::Expired->value => 'Kedaluwarsa',
                    MembershipStatus::Cancelled->value => 'Dibatalkan',
                ])
                ->required(),
            DatePicker::make('start_date')->label('Tanggal Mulai')
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set, mixed $state): void {
                    if (blank($state)) {
                        $set('end_date', null);

                        return;
                    }

                    $calculatedEndDate = self::calculateEndDate($get('membership_plan_id'), $state);

                    if (filled($calculatedEndDate)) {
                        $set('end_date', $calculatedEndDate);
                    }
                }),
            DatePicker::make('end_date')->label('Tanggal Berakhir')
                ->after('start_date')
                ->minDate(fn (Get $get): ?Carbon => filled($get('start_date'))
                    ? Carbon::parse($get('start_date'))->addDay()
                    : null)
                ->validationMessages([
                    'after' => 'Tanggal Berakhir harus setelah Tanggal Mulai.',
                    'after_or_equal' => 'Tanggal Berakhir harus setelah Tanggal Mulai.',
                ]),
        ]);
    }

    private static function calculateEndDate(mixed $membershipPlanId, Carbon|string $startDate): ?string
    {
        if (blank($membershipPlanId)) {
            return null;
        }

        $durationDays = MembershipPlan::query()->whereKey($membershipPlanId)->value('duration_days');

        if (blank($durationDays)) {
            return null;
        }

        return Carbon::parse($startDate)->addDays((int) $durationDays)->toDateString();
    }
}
