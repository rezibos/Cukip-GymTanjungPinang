@extends('layouts.app')

@section('title', 'Checkout Keranjang - CukipGym')
@section('description', 'Selesaikan seluruh pilihan paket membership CukipGym dalam satu checkout.')
@section('main-class', 'w-full flex-1 bg-[#f5f5f5]')

@section('content')
<section class="px-5 pb-20 pt-32 text-[#171717] sm:px-8 lg:px-12 lg:pb-28 lg:pt-40" aria-labelledby="checkout-title" data-navbar-tone="light">
    <div class="mx-auto max-w-[68rem]">
        <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-xs font-medium text-[#737373] transition hover:text-[#171717]"><span aria-hidden="true">←</span> Kembali ke keranjang</a>
        <div class="mt-7 grid gap-8 lg:grid-cols-[1fr_23rem] lg:items-start">
            <div>
                <p class="section-kicker">Checkout semua</p>
                <h1 id="checkout-title" class="mt-5 text-[clamp(2.8rem,5vw,4.8rem)] font-semibold leading-[.95] tracking-[-.06em]">Satu langkah.<br><span class="text-[#858585]">Semua pilihanmu.</span></h1>

                <div class="mt-9 flex flex-col gap-3">
                    @foreach ($items as $item)
                        <article class="flex items-center justify-between gap-6 rounded-[1.4rem] border border-black/10 bg-white p-5 sm:p-6">
                            <div>
                                <h2 class="font-semibold tracking-[-.025em]">{{ $item['plan']->name }}</h2>
                                <p class="mt-1 text-xs text-[#737373]">{{ $item['plan']->duration_days }} hari × {{ $item['quantity'] }} periode</p>
                            </div>
                            <p class="shrink-0 font-semibold">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                        </article>
                    @endforeach
                </div>
            </div>

            <aside class="rounded-[1.7rem] bg-[#171717] p-7 text-white lg:sticky lg:top-28">
                <p class="text-[.65rem] font-medium uppercase tracking-[.18em] text-white/45">Konfirmasi pesanan</p>
                <div class="mt-6 flex items-center justify-between gap-5 border-b border-white/15 pb-5 text-sm">
                    <span class="text-white/60">Jumlah periode</span>
                    <span class="font-semibold">{{ $totalQuantity }}</span>
                </div>
                <div class="mt-5 flex items-end justify-between gap-5">
                    <span class="text-sm text-white/60">Total bayar</span>
                    <span class="text-xl font-semibold tracking-[-.04em]">Rp{{ number_format($total, 0, ',', '.') }}</span>
                </div>

                <form method="POST" action="{{ route('cart.checkout.store') }}" class="mt-7">
                    @csrf
                    <label for="cart-phone" class="text-xs font-medium text-white/75">Nomor WhatsApp</label>
                    <input id="cart-phone" name="phone" type="text" inputmode="numeric" autocomplete="tel" value="{{ old('phone', $user->phone) }}" required placeholder="6281234567890" class="mt-2 w-full rounded-xl border border-white/15 bg-white/[.06] px-4 py-3 text-sm text-white outline-none placeholder:text-white/30 focus:border-white/50">
                    <p class="mt-2 text-[.65rem] leading-5 text-white/40">Digunakan untuk konfirmasi pembayaran.</p>
                    @error('phone')<p class="mt-2 text-xs text-white/80">{{ $message }}</p>@enderror

                    <button type="submit" class="mt-6 flex min-h-12 w-full cursor-pointer items-center justify-between rounded-full bg-white px-5 text-sm font-semibold text-[#171717] transition hover:bg-[#d4d4d4]">
                        Buat semua pesanan <span aria-hidden="true">↗</span>
                    </button>
                </form>
            </aside>
        </div>
    </div>
</section>
@endsection
