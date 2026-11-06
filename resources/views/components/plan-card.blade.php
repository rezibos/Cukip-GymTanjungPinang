@props(['plan', 'featured' => false])

<article {{ $attributes->merge(['class' => 'reveal group flex min-h-[25rem] flex-col rounded-[1.75rem] border p-6 transition duration-300 hover:-translate-y-2 ' . ($featured ? 'border-[#171717] bg-[#171717] text-white shadow-[0_24px_70px_rgba(23,28,44,.22)]' : 'border-[#171717]/10 bg-[#ffffff] text-[#171717]')]) }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.14em] {{ $featured ? 'text-[#d4d4d4]' : 'text-[#737373]' }}">{{ $plan->duration_days }} hari</p>
            <h3 class="mt-3 break-words text-2xl font-semibold tracking-[-0.04em]">{{ $plan->name }}</h3>
        </div>
        @if ($featured)
            <span class="rounded-full bg-[#737373] px-3 py-1 text-[.65rem] font-semibold uppercase tracking-wider text-white">Pilihan</span>
        @endif
    </div>

    <p class="mt-10 text-[clamp(2rem,3vw,3.4rem)] font-bold leading-none tracking-[-0.06em]">{{ $plan->formatted_price }}</p>
    @if ($plan->description)
        <p class="mt-5 text-sm leading-7 {{ $featured ? 'text-white/58' : 'text-[#525252]' }}">{{ $plan->description }}</p>
    @endif

    @if ($plan->features)
        <ul class="my-6 flex flex-col gap-2 text-xs opacity-70">
            @foreach ($plan->features as $feature)
                <li>✓ {{ $feature }}</li>
            @endforeach
        </ul>
    @endif

    <a href="{{ auth()->check() ? route('membership.checkout', $plan) : route('login') }}"
       class="mt-auto inline-flex items-center justify-between rounded-full px-5 py-3.5 text-sm font-semibold transition {{ $featured ? 'bg-[#737373] text-white hover:bg-[#525252]' : 'bg-[#171717] text-white hover:bg-[#737373]' }}">
        Pilih paket <span class="text-lg transition group-hover:translate-x-1" aria-hidden="true">→</span>
    </a>
</article>
