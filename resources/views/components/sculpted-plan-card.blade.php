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
    <a href="{{ auth()->check() ? route('membership.checkout', $plan) : route('login') }}" class="sculpted-plan-arrow group focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]" aria-label="Pilih {{ $plan->name }}">
        <svg class="size-10 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg>
    </a>
</article>
