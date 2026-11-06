<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionProofRequest;
use App\Models\GymProfile;
use App\Models\MembershipPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MembershipController extends Controller
{
    public function index()
    {
        $plans = MembershipPlan::where('is_active', true)->orderBy('price')->get();

        return view('membership.index', compact('plans'));
    }

    public function checkoutForm(MembershipPlan $plan)
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

    public function checkoutStore(Request $request, MembershipPlan $plan)
    {
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

        $membership = $user->memberships()->create([
            'membership_plan_id' => $plan->id,
            'status' => 'pending_payment',
        ]);

        $transaction = $membership->transactions()->create([
            'user_id' => $user->id,
            'amount' => $plan->price,
            'status' => 'pending',
        ]);

        return redirect()->route('membership.invoice', $transaction)
            ->with('success', 'Pesanan dibuat. Silakan lakukan pembayaran dan konfirmasi via WhatsApp.');
    }

    public function invoice(Transaction $transaction)
    {
        // Hanya pemilik transaksi yang boleh melihat
        abort_unless($transaction->user_id === auth()->id(), 403);

        $transaction->load('membership.plan');
        $profile = GymProfile::current();

        $waMessage = "Halo, saya ingin konfirmasi pembayaran membership CukipGym.\n"
            ."Nomor Invoice: {$transaction->invoice_number}\n"
            ."Paket: {$transaction->membership->plan->name}\n"
            ."Total: {$transaction->formatted_amount}\n"
            .'Terima kasih.';

        $waUrl = $profile->whatsapp_url
            ? $profile->whatsapp_url.'?text='.rawurlencode($waMessage)
            : null;

        return view('membership.invoice', compact('transaction', 'waUrl'));
    }

    public function uploadProof(StoreTransactionProofRequest $request, Transaction $transaction)
    {
        abort_unless($transaction->user_id === auth()->id(), 403);

        if ($transaction->status->value !== 'pending') {
            return back()->with('error', 'Transaksi ini sudah diproses, bukti tidak bisa diubah.');
        }

        // Hapus file lama kalau ada, supaya storage tidak menumpuk
        if ($transaction->payment_proof) {
            Storage::disk('public')->delete($transaction->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');
        $transaction->update(['payment_proof' => $path]);

        return redirect()->route('membership.invoice', $transaction)
            ->with('success', 'Bukti transfer berhasil diunggah. Menunggu verifikasi admin.');
    }
}
