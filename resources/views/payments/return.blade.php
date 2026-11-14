@extends('layouts.app')

@section('title', 'Status Pembayaran - CukipGym')
@section('main-class', 'w-full flex-1 bg-[#f5f5f5]')

@php
    $isPending = $payment?->status->value === 'pending';
    $isPaid = $payment?->status->value === 'paid';
    $checkoutItems = collect($payment?->checkout_items ?? []);
    $transactionItems = $payment
        ? $payment->transactions->map(fn ($item) => [
            'name' => $item->membership->plan->name,
            'quantity' => $item->quantity,
            'subtotal' => $item->amount,
        ])
        : collect();
    $orderItems = $checkoutItems->isNotEmpty() ? $checkoutItems : $transactionItems;
@endphp

@section('content')
<section class="px-5 pb-20 pt-32 text-[#171717] sm:px-8 lg:px-12 lg:pb-28 lg:pt-40" data-navbar-tone="light">
    <div class="mx-auto max-w-4xl">
        <div class="rounded-[1.7rem] border border-black/10 bg-white p-8 sm:p-12">
            <p class="section-kicker">Pembayaran Midtrans</p>

            @if (! $payment)
                <h1 class="mt-5 text-4xl font-semibold tracking-[-.055em]">Pesanan tidak ditemukan.</h1>
                <p class="mt-5 text-sm leading-7 text-[#737373]">Pesanan ini tidak tersimpan pada browser atau akun yang sedang digunakan.</p>
            @elseif ($isPaid)
                <h1 class="mt-5 text-4xl font-semibold tracking-[-.055em]">Pembayaran berhasil.</h1>
                <p class="mt-5 text-sm leading-7 text-[#737373]">Membership sudah diproses. Detail pesanan tetap dapat kamu lihat dari riwayat pesanan.</p>
                @if ($payment->payment_confirmation_sent_at && $payment->customer_email)
                    <p class="mt-3 text-xs leading-6 text-[#27845a]">Konfirmasi pembayaran sudah dikirim ke {{ $payment->customer_email }}.</p>
                @endif
            @elseif ($isPending && $result === 'finish')
                <h1 class="mt-5 text-4xl font-semibold tracking-[-.055em]">Pembayaran sedang diverifikasi.</h1>
                <p class="mt-5 text-sm leading-7 text-[#737373]">Pesananmu tetap tersimpan. Muat ulang halaman ini beberapa saat lagi untuk memeriksa konfirmasi Midtrans.</p>
            @elseif ($isPending)
                <h1 class="mt-5 text-4xl font-semibold tracking-[-.055em]">Pesananmu tetap tersimpan.</h1>
                <p class="mt-5 text-sm leading-7 text-[#737373]">Kamu menutup popup Midtrans sebelum pembayaran selesai. Lanjutkan kapan saja selama transaksi masih berlaku.</p>
            @else
                <h1 class="mt-5 text-4xl font-semibold tracking-[-.055em]">Pembayaran {{ strtolower($payment->status->label()) }}.</h1>
                <p class="mt-5 text-sm leading-7 text-[#737373]">Pesanan ini masih tercatat di riwayat. Jika masih ingin membeli paketnya, buat checkout baru.</p>
            @endif

            @if ($payment)
                <div class="mt-8 border-y border-black/10 py-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-[.62rem] font-semibold uppercase tracking-[.18em] text-[#858585]">Nomor pesanan</p>
                            <p class="mt-2 break-all text-xs font-semibold">{{ $payment->order_id }}</p>
                        </div>
                        <span class="rounded-full bg-[#f0f0ec] px-3 py-1.5 text-[.65rem] font-semibold">{{ $payment->status->label() }}</span>
                    </div>

                    <div class="mt-6 flex flex-col gap-4">
                        @foreach ($orderItems as $item)
                            <div class="flex items-start justify-between gap-5">
                                <div>
                                    <p class="text-sm font-semibold">{{ $item['name'] }}</p>
                                    <p class="mt-1 text-xs text-[#858585]">{{ $item['quantity'] }} periode</p>
                                </div>
                                <p class="shrink-0 text-sm font-semibold">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex items-center justify-between border-t border-black/10 pt-5">
                        <p class="text-xs text-[#737373]">Total pembayaran</p>
                        <p class="text-lg font-semibold">Rp{{ number_format($payment->gross_amount, 0, ',', '.') }}</p>
                    </div>

                    @if ($payment->customer_email)
                        <p class="mt-5 text-xs leading-6 text-[#858585]">Pesanan atas nama <span class="font-semibold text-[#404040]">{{ $payment->customer_name }}</span> · {{ $payment->customer_email }}</p>
                    @endif
                </div>
            @endif

            <p id="payment-close-message" class="mt-6 hidden rounded-xl bg-[#fff4df] px-4 py-3 text-xs leading-6 text-[#7a5311]">Popup ditutup. Tenang, pesananmu masih tersimpan dan tombol pembayaran tetap bisa digunakan.</p>

            <div class="mt-8 grid gap-3 md:grid-cols-3">
                @if ($isPending && filled($payment->snap_token))
                    <button type="button" id="resume-payment" class="inline-flex min-h-11 cursor-pointer items-center justify-center whitespace-nowrap rounded-full bg-[#171717] px-6 text-xs font-semibold text-white transition hover:bg-[#404040]">
                        Lanjutkan pembayaran
                    </button>
                @elseif ($transaction)
                    <a href="{{ route('membership.invoice', $transaction) }}" class="inline-flex min-h-11 items-center justify-center whitespace-nowrap rounded-full bg-[#171717] px-6 text-xs font-semibold text-white">Lihat invoice</a>
                @endif
                <a href="{{ route('payments.history') }}" class="inline-flex min-h-11 items-center justify-center whitespace-nowrap rounded-full border border-black/10 px-6 text-xs font-semibold">Riwayat pesanan</a>
                <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center justify-center whitespace-nowrap rounded-full border border-black/10 px-6 text-xs font-semibold">Kembali ke beranda</a>
            </div>
        </div>
    </div>
</section>
@endsection

@if ($isPending && filled($payment->snap_token))
@push('scripts')
<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const resumeButton = document.getElementById('resume-payment');
    const closeMessage = document.getElementById('payment-close-message');
    const snapToken = @json($payment->snap_token);
    const orderId = @json($payment->order_id);
    const redirectUrl = @json($payment->redirect_url);

    function paymentResultUrl(routeUrl) {
        const url = new URL(routeUrl, window.location.origin);
        url.searchParams.set('order_id', orderId);

        return url.toString();
    }

    resumeButton.addEventListener('click', function () {
        closeMessage.classList.add('hidden');

        if (!window.snap || typeof window.snap.pay !== 'function') {
            if (redirectUrl) {
                window.location.href = redirectUrl;
                return;
            }

            closeMessage.textContent = 'Popup Midtrans belum dapat dimuat. Periksa koneksi internet lalu coba kembali.';
            closeMessage.classList.remove('hidden');
            return;
        }

        window.snap.pay(snapToken, {
            onSuccess: function () {
                window.location.href = paymentResultUrl(@json(route('payments.finish')));
            },
            onPending: function () {
                window.location.href = paymentResultUrl(@json(route('payments.pending')));
            },
            onError: function () {
                window.location.href = paymentResultUrl(@json(route('payments.error')));
            },
            onClose: function () {
                closeMessage.classList.remove('hidden');
                closeMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            },
        });
    });
});
</script>
@endpush
@endif
