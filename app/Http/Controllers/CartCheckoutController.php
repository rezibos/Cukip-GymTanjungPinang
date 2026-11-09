<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use App\Support\MembershipCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CartCheckoutController extends Controller
{
    public function create(Request $request, MembershipCart $cart): View|RedirectResponse
    {
        $items = $cart->items($request);

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang masih kosong. Pilih paket terlebih dahulu.');
        }

        $user = $request->user();

        if ($user->isMember() || $user->hasPendingMembership()) {
            return redirect()->route('cart.index')
                ->with('error', 'Anda tidak bisa membuat pesanan baru saat masih memiliki membership aktif atau pembayaran tertunda.');
        }

        return view('cart.checkout', [
            'items' => $items,
            'totalQuantity' => $items->sum('quantity'),
            'total' => $items->sum('subtotal'),
            'user' => $user,
        ]);
    }

    public function store(Request $request, MembershipCart $cart, PaymentService $paymentService): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'regex:/^[0-9]{8,15}$/'],
        ], [
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Nomor WhatsApp hanya angka, contoh: 6281234567890.',
        ]);

        $items = $cart->items($request);

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang masih kosong. Pilih paket terlebih dahulu.');
        }

        $user = $request->user();

        if ($user->isMember() || $user->hasPendingMembership()) {
            return redirect()->route('cart.index')
                ->with('error', 'Anda tidak bisa membuat pesanan baru saat ini.');
        }

        if (blank($user->phone)) {
            $user->update(['phone' => $validated['phone']]);
        }

        [$firstTransaction, $payment] = DB::transaction(function () use ($items, $user): array {
            $checkoutBatch = (string) Str::uuid();
            $payment = $user->payments()->create([
                'checkout_batch' => $checkoutBatch,
                'order_id' => 'GYMPINANG-'.$checkoutBatch,
                'gross_amount' => $items->sum('subtotal'),
                'status' => 'pending',
            ]);
            $transactions = collect();

            foreach ($items as $item) {
                $membership = $user->memberships()->create([
                    'membership_plan_id' => $item['plan']->id,
                    'quantity' => $item['quantity'],
                    'status' => 'pending_payment',
                ]);

                $transactions->push($membership->transactions()->create([
                    'user_id' => $user->id,
                    'payment_id' => $payment->id,
                    'quantity' => $item['quantity'],
                    'checkout_batch' => $checkoutBatch,
                    'amount' => $item['subtotal'],
                    'status' => 'pending',
                ]));
            }

            return [$transactions->firstOrFail(), $payment];
        });

        $cart->clear($request);

        try {
            $payment = $paymentService->start($payment);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('membership.invoice', $firstTransaction)
                ->with('error', 'Pesanan tersimpan, tetapi Midtrans belum dapat dihubungi. Silakan coba bayar lagi dari invoice.');
        }

        return redirect()->away($payment->redirect_url);
    }
}
