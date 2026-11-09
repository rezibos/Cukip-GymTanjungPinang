@props(['plan', 'featured' => false])

@php
    $priceDivisor = $plan->price >= 1000000 ? 1000000 : 1000;
    $compactPrice = rtrim(rtrim(number_format($plan->price / $priceDivisor, 2, ',', '.'), '0'), ',');
    $priceSuffix = $plan->price >= 1000000 ? 'JT' : 'K';
@endphp

<article {{ $attributes->class(['sculpted-plan', 'sculpted-plan-featured' => $featured]) }}>
    <div class="sculpted-plan-top">
        @if ($featured || $plan->duration_days === 1)
            <span class="sculpted-plan-badge">{{ $featured ? 'Recommended' : 'Trial' }}</span>
        @endif
        <h3 class="text-[1.65rem] font-semibold tracking-[-.045em]">{{ $plan->name }}</h3>
        <div class="mt-6 grid grid-cols-[1.1fr_1fr] items-start gap-5">
            <p class="text-[.85rem] leading-6 text-[#686862]">{{ $plan->description ?? 'Pilih masa akses yang sesuai dengan rutinitas dan tujuan latihanmu.' }}</p>
            <div class="pt-1 text-right">
                <p class="whitespace-nowrap text-[clamp(2.2rem,3.1vw,3rem)] font-semibold leading-none tracking-[-.065em]" aria-label="{{ $plan->formatted_price }}" title="{{ $plan->formatted_price }}">{{ $compactPrice }}<span class="ml-0.5 text-[.6em]">{{ $priceSuffix }}</span></p>
                <p class="mt-3 text-xs text-[#797972]">{{ $plan->duration_days >= 300 ? '/ Year' : ($plan->duration_days >= 28 ? '/ Month' : $plan->duration_days . ' Day') }}</p>
            </div>
        </div>
    </div>
    <div class="sculpted-plan-bottom">
        <p class="border-t border-[#171717]/15 pt-5 text-sm font-semibold tracking-[-.015em]">Facilities</p>
        <ul class="mt-5 flex flex-col gap-2.5">
            @foreach ($plan->features as $feature)
                <li class="flex items-start gap-2 text-[.8rem] leading-5 text-[#575751]"><span class="mt-0.5 text-[.65rem] text-[#88887f]" aria-hidden="true">✓</span>{{ $feature }}</li>
            @endforeach
        </ul>
        <p class="mt-auto pt-6 text-[.6rem] font-semibold uppercase tracking-[.14em] text-[#909087]">{{ $plan->duration_days }} days access</p>
    </div>
    <form method="POST" action="{{ route('cart.store', $plan) }}" class="sculpted-plan-arrow group" data-cart-add>
        @csrf
        <button type="submit" class="grid size-full cursor-pointer place-items-center rounded-[inherit] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717] disabled:cursor-wait disabled:opacity-65" aria-label="Masukkan {{ $plan->name }} ke keranjang">
            <svg class="col-start-1 row-start-1 size-10 transition duration-200 group-hover:translate-x-5 group-hover:opacity-0 group-focus-within:translate-x-5 group-focus-within:opacity-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg>
            <svg class="col-start-1 row-start-1 size-7 -translate-x-5 opacity-0 transition duration-200 group-hover:translate-x-0 group-hover:opacity-100 group-focus-within:translate-x-0 group-focus-within:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 4h2l2.1 10.1a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H6"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
        </button>
    </form>
</article>
