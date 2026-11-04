<?php

namespace App\Filament\Resources\Memberships\Schemas;

use App\Enums\MembershipStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class MembershipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')->label('User')->relationship('user', 'name')->required()->searchable()->preload(),
            Select::make('membership_plan_id')->label('Paket')->relationship('plan', 'name')->required()->searchable()->preload(),
            Select::make('status')->label('Status')
                ->options([
                    MembershipStatus::PendingPayment->value => 'Menunggu Pembayaran',
                    MembershipStatus::Active->value => 'Aktif',
                    MembershipStatus::Expired->value => 'Kedaluwarsa',
                    MembershipStatus::Cancelled->value => 'Dibatalkan',
                ])
                ->required(),
            DatePicker::make('start_date')->label('Tanggal Mulai'),
            DatePicker::make('end_date')->label('Tanggal Berakhir')
                ->helperText('Ubah tanggal ini untuk memperpanjang membership secara manual.'),
        ]);
    }
}