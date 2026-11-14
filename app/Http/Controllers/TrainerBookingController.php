<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrainerBookingRequest;
use App\Models\TrainerSlot;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class TrainerBookingController extends Controller
{
    public function store(StoreTrainerBookingRequest $request, PaymentService $paymentService): RedirectResponse
    {
        $user = $request->user();
        $slotIds = $request->validated('slot_ids');

        $payment = DB::transaction(function () use ($slotIds, $user) {
            $slots = TrainerSlot::query()
                ->with('trainer')
                ->whereKey($slotIds)
                ->lockForUpdate()
                ->get();

            if ($slots->count() !== count($slotIds)
                || $slots->contains(fn (TrainerSlot $slot): bool => ! $slot->trainer->is_active
                    || $slot->starts_at->isPast()
                    || ($slot->status !== 'available' && ! ($slot->status === 'held' && $slot->held_until?->isPast())))) {
                throw ValidationException::withMessages(['slot_ids' => 'Salah satu sesi sudah tidak tersedia. Silakan pilih jadwal lain.']);
            }

            $checkoutBatch = (string) Str::uuid();
            $payment = $user->payments()->create([
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'checkout_batch' => $checkoutBatch,
                'order_id' => 'TRAINER-'.$checkoutBatch,
                'gross_amount' => $slots->sum(fn (TrainerSlot $slot): int => $slot->trainer->session_price),
                'status' => 'pending',
            ]);

            foreach ($slots as $slot) {
                $slot->update(['status' => 'held', 'held_until' => now()->addMinutes(30)]);
                $payment->trainerBookings()->create([
                    'user_id' => $user->id,
                    'trainer_slot_id' => $slot->id,
                    'amount' => $slot->trainer->session_price,
                    'status' => 'pending_payment',
                ]);
            }

            return $payment;
        });

        try {
            $payment = $paymentService->start($payment);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('classes.index')->with('error', 'Booking tersimpan, tetapi Midtrans belum dapat dimuat. Coba kembali dari riwayat pembayaran.');
        }

        return redirect()->away($payment->redirect_url);
    }
}
