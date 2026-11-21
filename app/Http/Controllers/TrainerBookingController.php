<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrainerBookingRequest;
use App\Models\Payment;
use App\Models\TrainerSlot;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class TrainerBookingController extends Controller
{
    public function store(StoreTrainerBookingRequest $request, PaymentService $paymentService): JsonResponse|RedirectResponse
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
                $booking = $payment->trainerBookings()->create([
                    'user_id' => $user->id,
                    'trainer_slot_id' => $slot->id,
                    'amount' => $slot->trainer->session_price,
                    'status' => 'pending_payment',
                ]);
                $booking->transaction()->create([
                    'user_id' => $user->id,
                    'payment_id' => $payment->id,
                    'quantity' => 1,
                    'checkout_batch' => $checkoutBatch,
                    'amount' => $slot->trainer->session_price,
                    'status' => 'pending',
                ]);
            }

            return $payment;
        });

        try {
            $payment = $paymentService->start($payment);
        } catch (Throwable $exception) {
            report($exception);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Booking tersimpan, tetapi Midtrans belum dapat dimuat. Coba kembali dari riwayat pembayaran.',
                    'invoice_url' => route('payments.pending', ['order_id' => $payment->order_id]),
                ], 503);
            }

            return redirect()->route('classes.index')->with('error', 'Booking tersimpan, tetapi Midtrans belum dapat dimuat. Coba kembali dari riwayat pembayaran.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'snap_token' => $payment->snap_token,
                'order_id' => $payment->order_id,
                'redirect_url' => $payment->redirect_url,
                'invoice_url' => route('payments.pending', ['order_id' => $payment->order_id]),
                'cancel_url' => route('trainer-bookings.cancel', $payment),
            ]);
        }

        return redirect()->away($payment->redirect_url);
    }

    public function destroy(Request $request, Payment $payment, PaymentService $paymentService): JsonResponse
    {
        if ((int) $payment->user_id !== (int) $request->user()->id
            || ! $payment->trainerBookings()->exists()) {
            abort(404);
        }

        try {
            $paymentService->cancelPendingTrainerBooking($payment);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Booking belum dapat dibatalkan. Silakan coba lagi.',
            ], 503);
        }

        return response()->json([
            'message' => 'Booking berhasil dibatalkan. Slot sudah tersedia kembali.',
        ]);
    }
}
