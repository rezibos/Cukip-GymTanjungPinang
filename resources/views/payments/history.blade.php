@extends('layouts.app')

@section('title', 'Riwayat Pesanan - CukipGym')
@section('description', 'Lihat dan lanjutkan pembayaran membership CukipGym yang tersimpan.')
@section('main-class', 'w-full flex-1 bg-[#f5f5f5]')

@section('content')
@php
    $paymentGroups = collect([
        [
            'key' => 'pending',
            'title' => 'Menunggu pembayaran',
            'description' => 'Pesanan yang masih dapat dilanjutkan.',
            'statuses' => ['pending'],
            'dotClass' => 'bg-[#9a6c26]',
        ],
        [
            'key' => 'paid',
            'title' => 'Berhasil',
            'description' => 'Pembayaran yang sudah terkonfirmasi.',
            'statuses' => ['paid'],
            'dotClass' => 'bg-[#35705b]',
        ],
        [
            'key' => 'failed',
            'title' => 'Gagal / tidak selesai',
            'description' => 'Pembayaran gagal, kedaluwarsa, atau dikembalikan.',
            'statuses' => ['failed', 'expired', 'refunded'],
            'dotClass' => 'bg-[#9b4b4b]',
        ],
    ])->map(function (array $group) use ($payments): array {
        $group['payments'] = $payments
            ->filter(fn ($payment): bool => in_array($payment->status->value, $group['statuses'], true))
            ->values();

        return $group;
    });
@endphp

<section class="px-5 pb-20 pt-32 text-[#171717] sm:px-8 lg:px-12 lg:pb-28 lg:pt-40" aria-labelledby="payment-history-title" data-navbar-tone="light">
    <div class="mx-auto max-w-5xl">
        <header class="border-b border-black/10 pb-8">
            <p class="section-kicker">Pembayaran membership</p>
            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 id="payment-history-title" class="text-4xl font-semibold leading-none tracking-[-.04em] sm:text-5xl">Riwayat pesanan.</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-6 text-[#666666]">Buka status yang ingin dilihat. Pesanan yang belum selesai dapat dilanjutkan tanpa membuat pesanan baru.</p>
                </div>
                @if ($payments->isNotEmpty())
                    <p class="shrink-0 text-xs font-medium text-[#666666]">{{ $payments->count() }} total pesanan</p>
                @endif
            </div>
        </header>

        @if ($payments->isNotEmpty())
            <div class="mt-8 flex flex-col gap-3" aria-label="Pesanan berdasarkan status">
                @foreach ($paymentGroups as $group)
                    <details
                        data-payment-group="{{ $group['key'] }}"
                        class="group overflow-hidden rounded-2xl border border-black/10 bg-white"
                        @if ($group['key'] === 'pending' && $group['payments']->isNotEmpty()) open @endif
                    >
                        <summary class="flex min-h-20 cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 outline-none transition-colors duration-200 hover:bg-black/[.025] focus-visible:bg-black/[.04] sm:px-6 [&::-webkit-details-marker]:hidden">
                            <span class="flex min-w-0 items-center gap-3 sm:gap-4">
                                <span class="size-2.5 shrink-0 rounded-full {{ $group['dotClass'] }}" aria-hidden="true"></span>
                                <span class="min-w-0">
                                    <span class="flex flex-wrap items-center gap-2">
                                        <span class="text-base font-semibold tracking-[-.02em]">{{ $group['title'] }}</span>
                                        <span class="rounded-full bg-[#f1f1f1] px-2.5 py-1 text-[.65rem] font-semibold text-[#525252]">{{ $group['payments']->count() }}</span>
                                    </span>
                                    <span class="mt-1 hidden text-xs text-[#737373] sm:block">{{ $group['description'] }}</span>
                                </span>
                            </span>
                            <span class="flex shrink-0 items-center gap-3">
                                @if ($group['payments']->isNotEmpty())
                                    <span class="hidden text-right sm:block">
                                        <span class="block text-[.62rem] text-[#858585]">Total nilai</span>
                                        <span class="mt-0.5 block text-sm font-semibold">Rp{{ number_format($group['payments']->sum('gross_amount'), 0, ',', '.') }}</span>
                                    </span>
                                @endif
                                <span class="grid size-10 place-items-center rounded-full border border-black/10 text-[#525252] transition-transform duration-200 group-open:rotate-180" aria-hidden="true">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </span>
                            </span>
                        </summary>

                        <div class="border-t border-black/10">
                            @forelse ($group['payments'] as $payment)
                                @php
                                    $checkoutItems = collect($payment->checkout_items ?? []);
                                    $firstCheckoutItem = $checkoutItems->first();
                                    $firstTransaction = $payment->transactions->first();
                                    $firstTrainerBooking = $payment->trainerBookings->first();
                                    $itemName = $firstCheckoutItem['name'] ?? $firstTransaction?->membership?->plan?->name ?? ($firstTrainerBooking ? 'Sesi privat '.$firstTrainerBooking->slot->trainer->name : 'Membership CukipGym');
                                    $itemCount = $checkoutItems->sum('quantity') ?: ($payment->transactions->sum('quantity') ?: $payment->trainerBookings->count());
                                    $itemUnit = $firstTrainerBooking ? 'sesi' : 'periode';
                                @endphp
                                <article class="grid gap-4 border-b border-black/[.07] px-5 py-5 last:border-b-0 sm:grid-cols-[minmax(0,1fr)_auto_auto] sm:items-center sm:gap-6 sm:px-6">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2.5">
                                            <h2 class="truncate text-sm font-semibold sm:text-base">{{ $itemName }}</h2>
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f3f3f1] px-2.5 py-1 text-[.6rem] font-semibold text-[#3f3f3f] ring-1 ring-inset ring-black/10">
                                                <span class="size-1.5 rounded-full {{ $group['dotClass'] }}" aria-hidden="true"></span>
                                                {{ $payment->status->label() }}
                                            </span>
                                        </div>
                                        <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-[.65rem] text-[#737373]">
                                            <span>{{ $itemCount }} {{ $itemUnit }}</span>
                                            <span aria-hidden="true">·</span>
                                            <time datetime="{{ $payment->created_at->toIso8601String() }}">{{ $payment->created_at->translatedFormat('d M Y, H:i') }}</time>
                                            <span class="hidden sm:inline" aria-hidden="true">·</span>
                                            <span class="max-w-full truncate font-mono text-[.6rem] text-[#8a8a8a] sm:max-w-60" title="{{ $payment->order_id }}">{{ $payment->order_id }}</span>
                                        </div>
                                    </div>
                                    <p class="text-base font-semibold tabular-nums sm:text-right">Rp{{ number_format($payment->gross_amount, 0, ',', '.') }}</p>
                                    <a href="{{ route('payments.pending', ['order_id' => $payment->order_id]) }}" class="inline-flex min-h-11 w-fit items-center justify-center rounded-full px-5 text-xs font-semibold transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black {{ $group['key'] === 'pending' ? 'bg-[#171717] text-white hover:bg-[#353535]' : 'border border-black/15 bg-white text-[#171717] hover:bg-[#f2f2f2]' }}">
                                        {{ $group['key'] === 'pending' ? 'Lanjutkan pembayaran' : 'Lihat detail' }}
                                    </a>
                                </article>
                            @empty
                                <div class="px-5 py-6 sm:px-6">
                                    <p class="text-sm font-medium">Belum ada pesanan {{ strtolower($group['title']) }}.</p>
                                    <p class="mt-1 text-xs leading-5 text-[#737373]">Grup ini akan terisi otomatis saat status pembayaran berubah.</p>
                                </div>
                            @endforelse
                        </div>
                    </details>
                @endforeach
            </div>
        @else
            <div class="mt-8 rounded-2xl border border-black/10 bg-white p-8 sm:p-10">
                <h2 class="text-2xl font-semibold tracking-[-.03em]">Belum ada pesanan di browser ini.</h2>
                <p class="mt-3 max-w-lg text-sm leading-7 text-[#666666]">Setelah kamu membuat checkout, pesanan akan muncul di sini meskipun popup Midtrans ditutup.</p>
                <a href="{{ route('membership.index') }}#pilihan-paket" class="mt-7 inline-flex min-h-11 items-center rounded-full bg-[#171717] px-6 text-xs font-semibold text-white transition-colors hover:bg-[#353535]">Pilih membership</a>
            </div>
        @endif
    </div>
</section>
@endsection
