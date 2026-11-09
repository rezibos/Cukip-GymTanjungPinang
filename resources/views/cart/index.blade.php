@extends('layouts.app')

@section('title', 'Keranjang Membership - CukipGym')
@section('description', 'Periksa paket membership CukipGym yang sudah kamu pilih sebelum melanjutkan checkout.')
@section('main-class', 'w-full flex-1 bg-[#f5f5f5]')

@section('content')
<section class="px-5 pb-20 pt-32 text-[#171717] sm:px-8 lg:px-12 lg:pb-28 lg:pt-40" aria-labelledby="cart-title" data-navbar-tone="light">
    <div class="mx-auto max-w-[76rem]">
        <div class="grid gap-7 border-b border-black/10 pb-9 lg:grid-cols-[1fr_.7fr] lg:items-end">
            <div>
                <p class="section-kicker">Keranjang membership</p>
                <h1 id="cart-title" class="mt-5 text-[clamp(3rem,6vw,5.5rem)] font-semibold leading-[.95] tracking-[-.065em]">Pilihanmu.<br><span class="text-[#858585]">Siap dilanjutkan.</span></h1>
            </div>
            <p class="max-w-md text-sm leading-7 text-[#737373] lg:justify-self-end">Atur jumlah setiap paket, periksa totalnya, lalu selesaikan semuanya dalam satu checkout.</p>
        </div>

        @if ($items->isEmpty())
            <div class="mt-10 rounded-[2rem] border border-black/10 bg-white p-8 sm:p-12">
                <span class="grid size-12 place-items-center rounded-full bg-[#f0f0ec]" aria-hidden="true">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.1 10.1a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H6"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                </span>
                <h2 class="mt-6 text-2xl font-semibold tracking-[-.04em]">Keranjangmu masih kosong.</h2>
                <p class="mt-3 max-w-lg text-sm leading-7 text-[#737373]">Pilih paket yang sesuai, lalu tekan tombol panah pada kartu untuk memasukkannya ke keranjang.</p>
                <a href="{{ route('membership.index') }}#pilihan-paket" class="mt-7 inline-flex items-center gap-6 rounded-full bg-[#171717] px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-[#404040]">Lihat paket <span aria-hidden="true">↗</span></a>
            </div>
        @else
            <div class="mt-10 grid gap-8 lg:grid-cols-[1fr_22rem] lg:items-start">
                <div class="flex flex-col gap-4">
                    @foreach ($items as $item)
                        @php($plan = $item['plan'])
                        <article class="grid gap-6 rounded-[1.6rem] border border-black/10 bg-white p-6 sm:grid-cols-[1fr_auto] sm:items-center sm:p-7" data-cart-item data-plan-id="{{ $plan->id }}" data-unit-price="{{ $plan->price }}">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-xl font-semibold tracking-[-.035em]">{{ $plan->name }}</h2>
                                    <span class="rounded-full bg-[#f0f0ec] px-3 py-1 text-[.65rem] font-medium text-[#737373]">{{ $plan->duration_days }} hari</span>
                                </div>
                                <p class="mt-3 max-w-xl text-xs leading-6 text-[#737373]">{{ $plan->description ?? 'Paket membership CukipGym yang sudah kamu pilih.' }}</p>
                                <div class="mt-5 flex flex-wrap items-end gap-x-5 gap-y-2">
                                    <p class="text-2xl font-semibold tracking-[-.045em]" data-item-subtotal>Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                    <p class="pb-1 text-[.65rem] text-[#858585]">{{ $plan->formatted_price }} per periode</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 sm:flex-col sm:items-stretch">
                                <form method="POST" action="{{ route('cart.update', $plan) }}" class="flex min-h-11 items-center justify-between rounded-full border border-black/10 bg-[#f5f5f2] p-1" data-cart-quantity-form>
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="operation" value="decrease" class="grid size-9 cursor-pointer place-items-center rounded-full bg-white text-lg shadow-sm transition hover:bg-[#171717] hover:text-white disabled:cursor-not-allowed disabled:opacity-35" aria-label="Kurangi jumlah {{ $plan->name }}" data-cart-decrease @disabled($item['quantity'] <= 1)>−</button>
                                    <span class="min-w-12 text-center text-sm font-semibold" data-item-quantity>{{ $item['quantity'] }}</span>
                                    <button type="submit" name="operation" value="increase" class="grid size-9 cursor-pointer place-items-center rounded-full bg-[#171717] text-lg text-white transition hover:bg-[#404040] disabled:cursor-not-allowed disabled:opacity-35" aria-label="Tambah jumlah {{ $plan->name }}" data-cart-increase>+</button>
                                </form>
                                <form method="POST" action="{{ route('cart.destroy', $plan) }}" data-cart-remove>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="min-h-11 w-full cursor-pointer rounded-full border border-black/10 px-5 text-xs font-medium text-[#737373] transition hover:border-black/25 hover:text-[#171717]">Hapus</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="border-t border-black/15 pt-6 lg:sticky lg:top-28 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0" aria-label="Ringkasan keranjang">
                    <div class="flex items-center justify-between gap-5">
                        <p class="text-[.65rem] font-semibold uppercase tracking-[.2em] text-[#737373]">Ringkasan</p>
                        <p class="text-xs text-[#737373]"><span class="font-semibold text-[#171717]" data-cart-total-quantity>{{ $totalQuantity }}</span> periode</p>
                    </div>

                    <div class="mt-10">
                        <p class="text-xs text-[#858585]">Total pembayaran</p>
                        <p class="mt-2 whitespace-nowrap text-[clamp(2.4rem,3.5vw,3.4rem)] font-semibold leading-none tracking-[-.065em]" data-cart-total>Rp{{ number_format($total, 0, ',', '.') }}</p>
                    </div>

                    <div class="mt-9 flex flex-col gap-4 border-y border-black/10 py-6 text-xs leading-5 text-[#737373]">
                        <p class="flex items-start gap-3">
                            <span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full border border-black/15 text-[.6rem] text-[#171717]" aria-hidden="true">✓</span>
                            Satu checkout untuk seluruh pilihan.
                        </p>
                        <p class="flex items-start gap-3">
                            <span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full border border-black/15 text-[.6rem] text-[#171717]" aria-hidden="true">✓</span>
                            Bayar langsung tanpa perlu login.
                        </p>
                    </div>

                    <button type="button" id="btn-bayar" class="group mt-7 flex w-full min-h-14 cursor-pointer items-center justify-between rounded-full bg-[#171717] pl-6 pr-2 text-sm font-semibold text-white transition hover:bg-[#3b3b3b] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]">
                        Bayar
                        <span class="grid size-10 place-items-center rounded-full bg-white text-[#171717] transition group-hover:translate-x-0.5" aria-hidden="true">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-5-5 5 5-5 5"/></svg>
                        </span>
                    </button>
                    <p class="mt-4 text-center text-[.62rem] text-[#9a9a9a]">Isi data diri, lalu lanjut ke pembayaran Midtrans.</p>
                </aside>
            </div>
        @endif
    </div>
</section>

{{-- ======================== MODAL CHECKOUT ======================== --}}
@if ($items->isNotEmpty())
<div id="checkout-modal" class="fixed inset-0 z-[999] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="relative w-full max-w-md rounded-[1.7rem] bg-white p-7 shadow-2xl sm:p-9">
        {{-- Close --}}
        <button type="button" id="modal-close" class="absolute right-5 top-5 grid size-9 cursor-pointer place-items-center rounded-full border border-black/10 text-[#737373] transition hover:bg-[#f5f5f2] hover:text-[#171717]" aria-label="Tutup">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <p class="text-[.65rem] font-semibold uppercase tracking-[.18em] text-[#858585]">Checkout</p>
        <h2 id="modal-title" class="mt-3 text-2xl font-semibold tracking-[-.04em]">Data Diri</h2>
        <p class="mt-2 text-xs leading-6 text-[#737373]">Lengkapi data di bawah untuk membuat pesanan dan melanjutkan pembayaran.</p>

        <form id="checkout-form" class="mt-7 flex flex-col gap-5" novalidate>
            @csrf
            <div>
                <label for="checkout-name" class="text-xs font-medium text-[#404040]">Nama Lengkap</label>
                <input id="checkout-name" name="name" type="text" required autocomplete="name" placeholder="Nama lengkap kamu" class="mt-2 w-full rounded-xl border border-black/10 bg-[#f9f9f7] px-4 py-3 text-sm outline-none placeholder:text-[#b5b5b5] focus:border-[#171717] focus:ring-1 focus:ring-[#171717]">
                <p class="mt-1.5 hidden text-xs text-red-500" data-error="name"></p>
            </div>
            <div>
                <label for="checkout-email" class="text-xs font-medium text-[#404040]">Email</label>
                <input id="checkout-email" name="email" type="email" required autocomplete="email" placeholder="email@contoh.com" class="mt-2 w-full rounded-xl border border-black/10 bg-[#f9f9f7] px-4 py-3 text-sm outline-none placeholder:text-[#b5b5b5] focus:border-[#171717] focus:ring-1 focus:ring-[#171717]">
                <p class="mt-1.5 hidden text-xs text-red-500" data-error="email"></p>
            </div>
            <div>
                <label for="checkout-phone" class="text-xs font-medium text-[#404040]">Nomor WhatsApp</label>
                <input id="checkout-phone" name="phone" type="text" inputmode="numeric" required autocomplete="tel" placeholder="6281234567890" class="mt-2 w-full rounded-xl border border-black/10 bg-[#f9f9f7] px-4 py-3 text-sm outline-none placeholder:text-[#b5b5b5] focus:border-[#171717] focus:ring-1 focus:ring-[#171717]">
                <p class="mt-1.5 hidden text-xs text-red-500" data-error="phone"></p>
            </div>

            <p class="text-[.65rem] leading-5 text-[#9a9a9a]">Akun akan otomatis dibuat setelah pembayaran berhasil. Kredensial login dikirim via email.</p>

            <div id="checkout-error" class="hidden rounded-xl bg-red-50 px-4 py-3 text-xs leading-5 text-red-600"></div>

            <button type="submit" id="btn-submit" class="flex min-h-12 w-full cursor-pointer items-center justify-center gap-3 rounded-full bg-[#171717] text-sm font-semibold text-white transition hover:bg-[#3b3b3b] disabled:cursor-not-allowed disabled:opacity-50">
                <span id="btn-text">Lanjut Bayar</span>
                <svg id="btn-spinner" class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="opacity-75"/></svg>
            </button>
        </form>
    </div>
</div>
@endif
@endsection

@if ($items->isNotEmpty())
@push('scripts')
<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('checkout-modal');
    const form = document.getElementById('checkout-form');
    const btnBayar = document.getElementById('btn-bayar');
    const btnClose = document.getElementById('modal-close');
    const btnSubmit = document.getElementById('btn-submit');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');
    const errorBox = document.getElementById('checkout-error');

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        form.querySelector('input')?.focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function setLoading(loading) {
        btnSubmit.disabled = loading;
        btnText.textContent = loading ? 'Memproses...' : 'Lanjut Bayar';
        btnSpinner.classList.toggle('hidden', !loading);
    }

    function clearErrors() {
        form.querySelectorAll('[data-error]').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        errorBox.classList.add('hidden');
        errorBox.textContent = '';
    }

    function showFieldErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const el = form.querySelector(`[data-error="${field}"]`);
            if (el) {
                el.textContent = messages[0];
                el.classList.remove('hidden');
            }
        }
    }

    btnBayar.addEventListener('click', openModal);
    btnClose.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        setLoading(true);

        const formData = new FormData(form);

        try {
            const response = await fetch('{{ route("cart.guest-checkout") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    email: formData.get('email'),
                    phone: formData.get('phone'),
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    showFieldErrors(data.errors);
                } else if (data.message) {
                    errorBox.textContent = data.message;
                    errorBox.classList.remove('hidden');
                }
                if (data.redirect) {
                    setTimeout(() => window.location.href = data.redirect, 2000);
                }
                setLoading(false);
                return;
            }

            closeModal();

            window.snap.pay(data.snap_token, {
                onSuccess: function () {
                    window.location.href = '{{ route("payments.finish") }}';
                },
                onPending: function () {
                    window.location.href = '{{ route("payments.pending") }}';
                },
                onError: function () {
                    window.location.href = '{{ route("payments.error") }}';
                },
                onClose: function () {
                    window.location.href = '{{ route("payments.pending") }}';
                },
            });
        } catch (err) {
            errorBox.textContent = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
            errorBox.classList.remove('hidden');
            setLoading(false);
        }
    });
});
</script>
@endpush
@endif
