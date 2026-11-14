<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use App\Support\MembershipCart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class GuestCheckoutController extends Controller
{
    public function store(Request $request, MembershipCart $cart, PaymentService $paymentService): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'regex:/^[0-9]{8,15}$/'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Nomor WhatsApp hanya angka, contoh: 6281234567890.',
        ]);

        $items = $cart->items($request);

        if ($items->isEmpty()) {
            return response()->json(['message' => 'Keranjang masih kosong.'], 422);
        }

        $user = $request->user();
        $createsAccount = false;

        if (! $user) {
            $user = User::query()->where('email', $validated['email'])->first();

            if (! $user) {
                $createsAccount = true;
            }
        }

        if ($user && blank($user->phone)) {
            $user->update(['phone' => $validated['phone']]);
        }

        if ($user && ($user->isMember() || $user->hasPendingMembership())) {
            return response()->json([
                'message' => 'Email ini sudah memiliki membership aktif atau pembayaran tertunda.',
            ], 422);
        }

        $checkoutBatch = (string) Str::uuid();
        $payment = Payment::query()->create([
            'user_id' => $user?->id,
            'customer_name' => $user?->name ?? $validated['name'],
            'customer_email' => $user?->email ?? $validated['email'],
            'customer_phone' => $user?->phone ?? $validated['phone'],
            'checkout_items' => $items->map(fn (array $item): array => [
                'membership_plan_id' => $item['plan']->id,
                'name' => $item['plan']->name,
                'price' => $item['plan']->price,
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
            ])->values()->all(),
            'checkout_batch' => $checkoutBatch,
            'order_id' => 'GYMPINANG-'.$checkoutBatch,
            'gross_amount' => $items->sum('subtotal'),
            'status' => 'pending',
            'creates_account' => $createsAccount,
        ]);

        $cart->clear($request);
        $guestPaymentIds = collect($request->session()->get('guest_payment_ids', []))
            ->push($payment->id)
            ->unique()
            ->take(-10)
            ->values()
            ->all();
        $request->session()->put('guest_payment_ids', $guestPaymentIds);

        try {
            $payment = $paymentService->start($payment);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Pesanan tersimpan, tetapi Midtrans belum dapat dihubungi. Silakan coba lagi.',
                'redirect' => route('payments.pending', ['order_id' => $payment->order_id]),
            ], 503);
        }

        return response()->json([
            'snap_token' => $payment->snap_token,
            'order_id' => $payment->order_id,
            'invoice_url' => route('payments.pending', ['order_id' => $payment->order_id]),
        ]);
    }
}
