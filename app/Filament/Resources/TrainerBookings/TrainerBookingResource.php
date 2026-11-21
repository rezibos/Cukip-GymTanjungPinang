<?php

namespace App\Filament\Resources\TrainerBookings;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Filament\Resources\TrainerBookings\Pages\ListTrainerBookings;
use App\Filament\Resources\TrainerBookings\Schemas\TrainerBookingForm;
use App\Filament\Resources\TrainerBookings\Tables\TrainerBookingsTable;
use App\Models\TrainerBooking;
use App\Models\TrainerSlot;
use App\Models\User;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TrainerBookingResource extends Resource
{
    protected static ?string $model = TrainerBooking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Booking Personal Trainer';

    protected static ?string $modelLabel = 'Booking Personal Trainer';

    protected static ?string $pluralModelLabel = 'Booking Personal Trainer';

    protected static string|\UnitEnum|null $navigationGroup = 'Kelas & latihan';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return TrainerBookingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainerBookingsTable::configure($table);
    }

    public static function makeCreateAction(): CreateAction
    {
        return CreateAction::make()
            ->label('Tambah')
            ->icon(Heroicon::Plus)
            ->modal()
            ->modalHeading('Tambah Booking Personal Trainer')
            ->modalDescription('Catat pembelian sesi personal trainer yang dilakukan langsung di tempat.')
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalWidth(Width::FourExtraLarge)
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->using(fn (array $data): TrainerBooking => self::createManualBooking($data))
            ->createAnother(false);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainerBookings::route('/'),
        ];
    }

    /** @param array{user_id: int|string, personal_trainer_id: int|string, slot_ids: array<int, int|string>, payment_status: string, payment_method: string, payment_channel?: string|null} $data */
    private static function createManualBooking(array $data): TrainerBooking
    {
        return DB::transaction(function () use ($data): TrainerBooking {
            $user = User::query()->findOrFail($data['user_id']);
            $slotIds = collect($data['slot_ids'])->map(fn (mixed $id): int => (int) $id)->unique()->values();
            $slots = TrainerSlot::query()
                ->with('trainer')
                ->whereKey($slotIds)
                ->where('personal_trainer_id', $data['personal_trainer_id'])
                ->lockForUpdate()
                ->get();

            if ($slots->count() !== $slotIds->count() || $slots->contains(fn (TrainerSlot $slot): bool => $slot->starts_at->isPast()
                || ($slot->status !== 'available' && ! ($slot->status === 'held' && $slot->held_until?->isPast())))) {
                throw ValidationException::withMessages([
                    'mountedActions.0.data.slot_ids' => 'Salah satu jadwal sudah tidak tersedia. Pilih jadwal lain.',
                ]);
            }

            $isPaid = $data['payment_status'] === PaymentStatus::Paid->value;
            $checkoutBatch = (string) Str::uuid();
            $payment = $user->payments()->create([
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'checkout_batch' => $checkoutBatch,
                'order_id' => 'TRAINER-MANUAL-'.$checkoutBatch,
                'gross_amount' => $slots->sum(fn (TrainerSlot $slot): int => $slot->trainer->session_price),
                'status' => $isPaid ? PaymentStatus::Paid : PaymentStatus::Pending,
                'payment_type' => $data['payment_method'] === 'manual'
                    ? $data['payment_channel']
                    : 'midtrans',
                'paid_at' => $isPaid ? now() : null,
            ]);
            $bookings = collect();

            foreach ($slots as $slot) {
                $slot->update([
                    'status' => $isPaid ? 'booked' : 'held',
                    'held_until' => $isPaid ? null : now()->addMinutes(30),
                ]);
                $booking = $payment->trainerBookings()->create([
                    'user_id' => $user->id,
                    'trainer_slot_id' => $slot->id,
                    'amount' => $slot->trainer->session_price,
                    'status' => $isPaid ? 'confirmed' : 'pending_payment',
                ]);
                $booking->transaction()->create([
                    'user_id' => $user->id,
                    'payment_id' => $payment->id,
                    'quantity' => 1,
                    'checkout_batch' => $checkoutBatch,
                    'amount' => $slot->trainer->session_price,
                    'payment_method' => $data['payment_method'],
                    'payment_channel' => $data['payment_method'] === 'manual'
                        ? $data['payment_channel']
                        : null,
                    'status' => $isPaid ? TransactionStatus::Paid : TransactionStatus::Pending,
                    'paid_at' => $isPaid ? now() : null,
                    'admin_note' => 'Dibuat manual oleh admin.',
                ]);
                $bookings->push($booking);
            }

            return $bookings->firstOrFail();
        });
    }
}
