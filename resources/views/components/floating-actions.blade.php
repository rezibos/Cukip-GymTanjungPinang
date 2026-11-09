@php
    $storedCart = session('membership_cart', []);
    $cartCount = is_array($storedCart)
        ? (array_is_list($storedCart) ? count($storedCart) : array_sum(array_map('intval', $storedCart)))
        : 0;
@endphp

<div {{ $attributes->merge(['class' => 'fixed bottom-5 right-5 z-40 flex flex-col items-center gap-2.5 sm:bottom-7 sm:right-7']) }} data-floating-actions>
    <a
        href="{{ route('cart.index') }}"
        class="group relative grid size-13 place-items-center rounded-full bg-[#171717] text-white shadow-[0_12px_32px_rgba(0,0,0,.22)] transition duration-200 hover:-translate-y-1 hover:bg-[#3f3f3f] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]"
        aria-label="Buka keranjang, {{ $cartCount }} paket"
        data-cart-link
    >
        <span class="pointer-events-none absolute bottom-full right-0 mb-2 hidden whitespace-nowrap rounded-lg bg-[#171717] px-3 py-1.5 text-[.65rem] font-medium text-white shadow-lg group-hover:block group-focus-visible:block" aria-hidden="true">Keranjang</span>
        <span class="absolute -right-1 -top-1 grid size-5 place-items-center rounded-full border-2 border-white bg-[#e8e8e3] text-[.6rem] font-bold text-[#171717] {{ $cartCount === 0 ? 'hidden' : '' }}" aria-hidden="true" data-cart-count>{{ $cartCount }}</span>
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 4h2l2.1 10.1a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H6"/>
            <circle cx="10" cy="20" r="1"/>
            <circle cx="18" cy="20" r="1"/>
        </svg>
    </a>

    <details class="group relative" data-floating-help>
        <summary
            class="relative grid size-13 cursor-pointer list-none place-items-center rounded-full border border-black/10 bg-white text-[#171717] shadow-[0_12px_32px_rgba(0,0,0,.16)] transition duration-200 hover:-translate-y-1 hover:bg-[#eeeeea] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717] [&::-webkit-details-marker]:hidden"
            aria-label="Buka bantuan cepat"
            aria-controls="floating-help-panel"
            aria-expanded="false"
        >
            <span class="pointer-events-none absolute bottom-full right-0 mb-2 hidden whitespace-nowrap rounded-lg bg-[#171717] px-3 py-1.5 text-[.65rem] font-medium text-white shadow-lg group-hover:block group-focus-visible:block group-open:hidden" aria-hidden="true">Bantuan</span>
            <svg class="size-5 transition group-open:rotate-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="9"/>
                <path d="M9.7 9a2.4 2.4 0 0 1 4.65.8c0 1.8-2.35 2.05-2.35 3.7"/>
                <path d="M12 17.2h.01"/>
            </svg>
        </summary>

        <div id="floating-help-panel" class="absolute bottom-full right-0 mb-3 w-[min(18rem,calc(100vw-2.5rem))] origin-bottom-right rounded-[1.35rem] border border-black/10 bg-white p-2.5 text-[#171717] shadow-[0_20px_50px_rgba(0,0,0,.18)]">
            <div class="px-3 pb-2 pt-2">
                <p class="text-sm font-semibold tracking-[-.025em]">Ada yang bisa dibantu?</p>
                <p class="mt-1 text-[.7rem] leading-5 text-[#737373]">Temukan informasi yang kamu perlukan.</p>
            </div>
            <nav class="flex flex-col" aria-label="Bantuan cepat">
                <a href="{{ route('home') }}#faq" class="flex items-center justify-between rounded-xl px-3 py-3 text-xs font-medium transition hover:bg-[#f1f1ed] focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#171717]">
                    Tanya jawab <span aria-hidden="true">↗</span>
                </a>
                <a href="{{ route('classes.index') }}" class="flex items-center justify-between rounded-xl px-3 py-3 text-xs font-medium transition hover:bg-[#f1f1ed] focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#171717]">
                    Jadwal kelas <span aria-hidden="true">↗</span>
                </a>
                <a href="{{ route('facilities.index') }}" class="flex items-center justify-between rounded-xl px-3 py-3 text-xs font-medium transition hover:bg-[#f1f1ed] focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#171717]">
                    Lihat fasilitas <span aria-hidden="true">↗</span>
                </a>
            </nav>
        </div>
    </details>
</div>
