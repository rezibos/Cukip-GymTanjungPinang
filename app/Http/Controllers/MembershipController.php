<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionProofRequest;
use App\Models\GymProfile;
use App\Models\MembershipPlan;
use App\Models\Transaction;
use App\Services\PaymentService;
use App\Support\MembershipCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class MembershipController extends Controller
{
    public function index(): View
    {
        $plans = MembershipPlan::where('is_active', true)->orderBy('duration_days')->get();

        return view('membership.index', compact('plans'));
    }

    public function checkoutForm(MembershipPlan $plan): View|RedirectResponse
    {
        abort_unless($plan->is_active, 404);

        $user = auth()->user();

        if ($user->isMember()) {
            return redirect()->route('membership.index')
                ->with('error', 'Anda masih memiliki membership aktif. Tidak bisa checkout paket baru.');
        }

        if ($user->hasPendingMembership()) {
            return redirect()->route('membership.index')
                ->with('error', 'Anda memiliki transaksi yang masih menunggu pembayaran. Selesaikan dulu sebelum checkout paket baru.');
        }

        return view('membership.checkout', compact('plan', 'user'));
    }

    public function checkoutStore(
        Request $request,
        MembershipPlan $plan,
        MembershipCart $cart,
        PaymentService $paymentService,
    ): RedirectResponse {
        abort_unless($plan->is_active, 404);

        $user = auth()->user();

        // Validasi ulang di server, jangan percaya pengecekan di halaman form saja
        if ($user->isMember() || $user->hasPendingMembership()) {
            return redirect()->route('membership.index')
                ->with('error', 'Anda tidak bisa checkout paket baru saat ini.');
        }

        $validated = $request->validate([
            'phone' => ['required', 'regex:/^[0-9]{8,15}$/'],
        ], [
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Nomor WhatsApp hanya angka, contoh: 6281234567890.',
        ]);

        // Simpan nomor telepon ke profil user jika belum ada
        if (blank($user->phone)) {
            $user->update(['phone' => $validated['phone']]);
        }

        [$transaction, $payment] = DB::transaction(function () use ($plan, $user): array {
            $checkoutBatch = (string) Str::uuid();
            $payment = $user->payments()->create([
                'checkout_batch' => $checkoutBatch,
                'order_id' => 'GYMPINANG-'.$checkoutBatch,
                'gross_amount' => $plan->price,
                'status' => 'pending',
            ]);
            $membership = $user->memberships()->create([
                'membership_plan_id' => $plan->id,
                'quantity' => 1,
                'status' => 'pending_payment',
            ]);
            $transaction = $membership->transactions()->create([
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'quantity' => 1,
                'checkout_batch' => $checkoutBatch,
                'amount' => $plan->price,
                'status' => 'pending',
            ]);

            return [$transaction, $payment];
        });

        $cart->remove($request, $plan);

        try {
            $payment = $paymentService->start($payment);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('membership.invoice', $transaction)
                ->with('error', 'Pesanan tersimpan, tetapi Midtrans belum dapat dihubungi. Silakan coba bayar lagi dari invoice.');
        }

        return redirect()->away($payment->redirect_url);
    }

    public function invoice(Transaction $transaction): View
    {
        // Hanya pemilik transaksi yang boleh melihat
        abort_unless($transaction->user_id === auth()->id(), 403);

        $transactions = $transaction->checkoutGroup();
        $transactions->load('membership.plan');
        $transaction->load('payment');
        abort_unless($transactions->every(fn (Transaction $item): bool => $item->user_id === auth()->id()), 403);

        $total = $transactions->sum('amount');
        $profile = GymProfile::current();
        $packageLines = $transactions->map(
            fn (Transaction $item): string => "- {$item->membership->plan->name} x{$item->quantity}: {$item->formatted_amount}",
        )->implode("\n");

        $waMessage = "Halo, saya ingin konfirmasi pembayaran membership CukipGym.\n"
            ."Nomor Invoice: {$transaction->invoice_number}\n"
            ."Paket:\n{$packageLines}\n"
            .'Total: Rp'.number_format($total, 0, ',', '.')."\n"
            .'Terima kasih.';

        $waUrl = $profile->whatsapp_url
            ? $profile->whatsapp_url.'?text='.rawurlencode($waMessage)
            : null;

        return view('membership.invoice', compact('transaction', 'transactions', 'total', 'waUrl'));
    }

    public function uploadProof(StoreTransactionProofRequest $request, Transaction $transaction): RedirectResponse
    {
        abort_unless($transaction->user_id === auth()->id(), 403);

        $transactions = $transaction->checkoutGroup();
        abort_unless($transactions->every(fn (Transaction $item): bool => $item->user_id === auth()->id()), 403);

        if ($transactions->contains(fn (Transaction $item): bool => $item->status->value !== 'pending')) {
            return back()->with('error', 'Transaksi ini sudah diproses, bukti tidak bisa diubah.');
        }

        foreach ($transactions->pluck('payment_proof')->filter()->unique() as $oldProof) {
            Storage::disk('public')->delete($oldProof);
        }

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');
        Transaction::query()->whereKey($transactions->modelKeys())->update(['payment_proof' => $path]);

        return redirect()->route('membership.invoice', $transaction)
            ->with('success', 'Bukti transfer berhasil diunggah. Menunggu verifikasi admin.');
    }
}
