<?php

namespace App\Filament\Resources\TrainerBookings\Schemas;

use App\Enums\PaymentStatus;
use App\Models\PersonalTrainer;
use App\Models\TrainerSlot;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class TrainerBookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('Member')
                ->relationship('user', 'name')
                ->searchable(['name', 'email'])
                ->preload()
                ->required(),
            Select::make('personal_trainer_id')
                ->label('Personal trainer')
                ->options(fn (): array => PersonalTrainer::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(fn (Set $set): mixed => $set('slot_ids', []))
                ->required(),
            Select::make('slot_ids')
                ->label('Jadwal sesi')
                ->multiple()
                ->options(fn (Get $get): array => self::availableSlotOptions($get('personal_trainer_id')))
                ->searchable()
                ->preload()
                ->disabled(fn (Get $get): bool => blank($get('personal_trainer_id')))
                ->required()
                ->minItems(1),
            Select::make('payment_status')
                ->label('Status pembayaran')
                ->searchable(false)
                ->options([
                    PaymentStatus::Paid->value => 'Lunas',
                    PaymentStatus::Pending->value => 'Menunggu pembayaran',
                ])
                ->default(PaymentStatus::Paid->value)
                ->required(),
            Select::make('payment_method')
                ->label('Metode Pembayaran')
                ->searchable(false)
                ->position('top')
                ->options([
                    'manual' => 'Manual',
                    'midtrans' => 'Otomatis (Midtrans)',
                ])
                ->default('manual')
                ->live()
                ->required(),
            Select::make('payment_channel')
                ->label('Pembayaran Manual')
                ->searchable(false)
                ->position('top')
                ->options([
                    'cash' => 'Tunai',
                    'qris' => 'QRIS',
                    'bank_transfer' => 'Transfer Bank',
                ])
                ->default('cash')
                ->required(fn (Get $get): bool => $get('payment_method') === 'manual')
                ->visible(fn (Get $get): bool => $get('payment_method') === 'manual'),
        ]);
    }

    /** @return array<int, string> */
    private static function availableSlotOptions(mixed $trainerId): array
    {
        if (blank($trainerId)) {
            return [];
        }

        return TrainerSlot::query()
            ->where('personal_trainer_id', $trainerId)
            ->where('starts_at', '>=', now())
            ->where(function ($query): void {
                $query
                    ->where('status', 'available')
                    ->orWhere(fn ($heldQuery) => $heldQuery
                        ->where('status', 'held')
                        ->where('held_until', '<', now()));
            })
            ->orderBy('starts_at')
            ->get()
            ->mapWithKeys(fn (TrainerSlot $slot): array => [
                $slot->id => $slot->starts_at->translatedFormat('D, d M Y · H:i').' - '.$slot->ends_at->format('H:i'),
            ])
            ->all();
    }
}
