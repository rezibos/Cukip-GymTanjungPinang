@extends('layouts.app')

@section('title', 'Riwayat Pesanan - CukipGym')
@section('description', 'Lihat dan lanjutkan pembayaran membership CukipGym yang tersimpan.')
@section('main-class', 'w-full flex-1 bg-[#f5f5f5]')

@section('content')
<section class="px-5 pb-20 pt-32 text-[#171717] sm:px-8 lg:px-12 lg:pb-28 lg:pt-40" aria-labelledby="payment-history-title" data-navbar-tone="light">
    <div class="mx-auto max-w-4xl">
        <div class="border-b border-black/10 pb-9">
            <p class="section-kicker">Pembayaran membership</p>
            <h1 id="payment-history-title" class="mt-5 text-[clamp(3rem,6vw,5.2rem)] font-semibold leading-[.95] tracking-[-.065em]">Riwayat pesanan.</h1>
            <p class="mt-5 max-w-xl text-sm leading-7 text-[#737373]">Pesanan guest tersimpan pada browser ini. Pembayaran yang belum selesai dapat dibuka kembali tanpa membuat pesanan baru.</p>
        </div>

        <div class="mt-9 flex flex-col gap-4">
            @forelse ($payments as $payment)
                @php
                    $checkoutItems = collect($payment->checkout_items ?? []);
                    $firstCheckoutItem = $checkoutItems->first();
                    $firstTransaction = $payment->transactions->first();
                    $itemName = $firstCheckoutItem['name'] ?? $firstTransaction?->membership?->plan?->name ?? 'Membership CukipGym';
                    $itemCount = $checkoutItems->sum('quantity') ?: $payment->transactions->sum('quantity');
                @endphp
                <article class="grid gap-6 rounded-[1.5rem] border border-black/10 bg-white p-6 sm:grid-cols-[1fr_auto] sm:items-center sm:p-7">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-lg font-semibold tracking-[-.03em]">{{ $itemName }}</h2>
                            <span class="rounded-full bg-[#f0f0ec] px-3 py-1 text-[.62rem] font-semibold">{{ $payment->status->label() }}</span>
                        </div>
                        <p class="mt-3 break-all text-[.65rem] text-[#858585]">{{ $payment->order_id }}</p>
                        <p class="mt-1 text-[.65rem] text-[#a3a3a3]">{{ $itemCount }} periode · {{ $payment->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 sm:flex-col sm:items-end">
                        <p class="text-lg font-semibold">Rp{{ number_format($payment->gross_amount, 0, ',', '.') }}</p>
                        <a href="{{ route('payments.pending', ['order_id' => $payment->order_id]) }}" class="inline-flex min-h-10 items-center rounded-full bg-[#171717] px-5 text-[.65rem] font-semibold text-white transition hover:bg-[#404040]">
                            {{ $payment->status->value === 'pending' ? 'Lanjutkan pembayaran' : 'Lihat pesanan' }}
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-[1.7rem] border border-black/10 bg-white p-8 sm:p-12">
                    <h2 class="text-2xl font-semibold tracking-[-.04em]">Belum ada pesanan di browser ini.</h2>
                    <p class="mt-3 max-w-lg text-sm leading-7 text-[#737373]">Setelah kamu membuat checkout, pesanan akan muncul di sini meskipun popup Midtrans ditutup.</p>
                    <a href="{{ route('membership.index') }}#pilihan-paket" class="mt-7 inline-flex min-h-11 items-center rounded-full bg-[#171717] px-6 text-xs font-semibold text-white">Pilih membership</a>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
