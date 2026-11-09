<?php

namespace App\Http\Controllers;

use App\Mail\AccountCreatedMail;
use App\Models\User;
use App\Services\PaymentService;
use App\Support\MembershipCart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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

        $plainPassword = null;
        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            if ($user->isMember() || $user->hasPendingMembership()) {
                return response()->json([
                    'message' => 'Email ini sudah memiliki membership aktif atau pembayaran tertunda.',
                ], 422);
            }

            if (blank($user->phone)) {
                $user->update(['phone' => $validated['phone']]);
            }
        } else {
            $plainPassword = Str::random(8);
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => $plainPassword,
                'role' => 'user',
            ]);
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

        // Login the user so webhook/return pages work correctly
        Auth::login($user);

        try {
            $payment = $paymentService->start($payment);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Pesanan tersimpan, tetapi Midtrans belum dapat dihubungi. Silakan coba lagi.',
                'redirect' => route('membership.invoice', $firstTransaction),
            ], 503);
        }

        // Send account credentials email for newly created users
        if ($plainPassword !== null) {
            Mail::to($user)->send(new AccountCreatedMail($user, $plainPassword));
        }

        return response()->json([
            'snap_token' => $payment->snap_token,
        ]);
    }
}
