@php
    $footerName = rtrim($footerProfile?->name ?: 'CukipGym', '.');
    $gymPosition = mb_strripos($footerName, 'gym');
    $footerNamePrimary = $gymPosition === false ? $footerName : mb_substr($footerName, 0, $gymPosition);
    $footerNameAccent = $gymPosition === false ? '' : mb_substr($footerName, $gymPosition);
    $footerDescription = $footerProfile?->content('footer_description') ?: 'Tempat latihan, kelas kebugaran, dan komunitas untuk membantu kamu bergerak lebih konsisten.';
    $footerLocation = $footerProfile?->content('footer_location') ?: 'Gym lokal Tanjungpinang';
    $footerSponsors = $footerProfile?->footerSponsors() ?? (new \App\Models\GymProfile)->footerSponsors();
@endphp

<footer class="bg-[#0a0a0a] px-5 py-14 text-white sm:px-8 lg:px-12 lg:py-20" data-navbar-tone="dark">
    <div class="mx-auto max-w-[90rem]">
        <div class="grid gap-12 border-b border-white/10 pb-12 lg:grid-cols-[1fr_auto] lg:items-end">
            <div>
                <p class="text-[clamp(3.8rem,9vw,9rem)] font-bold leading-[.8] tracking-[-0.075em]">
                    {{ $footerNamePrimary }}<span class="text-[#737373]">{{ $footerNameAccent }}.</span>
                </p>
                <p class="mt-7 max-w-md text-sm leading-7 text-white/50">{{ $footerDescription }}</p>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-3 text-sm text-white/65">
                <a href="{{ route('facilities.index') }}" class="transition hover:text-[#d4d4d4]">Fasilitas</a>
                <a href="{{ route('membership.index') }}" class="transition hover:text-[#d4d4d4]">Membership</a>
                <a href="{{ route('classes.index') }}" class="transition hover:text-[#d4d4d4]">Kelas</a>
                <a href="{{ route('payments.history') }}" class="transition hover:text-[#d4d4d4]">Pesanan Saya</a>
                <a href="{{ route('login') }}" class="transition hover:text-[#d4d4d4]">Masuk</a>
            </div>
        </div>

        @if ($footerSponsors !== [])
            <div class="py-10" role="region" aria-labelledby="sponsors-title">
                <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2">
                    <h3 id="sponsors-title" class="text-[.65rem] font-semibold uppercase tracking-[.22em] text-white/55">Sponsor & partner</h3>
                </div>
                <ul class="mt-7 grid grid-cols-2 items-center gap-x-7 gap-y-8 text-white/55 sm:grid-cols-4 sm:gap-8" aria-label="Sponsor dan partner CukipGym">
                    @foreach ($footerSponsors as $sponsor)
                        <li class="relative flex min-h-14 items-center justify-center text-center">
                            @if (filled($sponsor['logo'] ?? null))
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($sponsor['logo']) }}"
                                    alt="{{ $sponsor['name'] }}"
                                    class="max-h-12 max-w-[10rem] object-contain grayscale opacity-70 transition hover:grayscale-0 hover:opacity-100"
                                    loading="lazy"
                                    onerror="this.hidden=true;this.nextElementSibling.hidden=false;"
                                >
                                <span hidden class="text-xl font-semibold tracking-[-.04em] sm:text-2xl">{{ $sponsor['name'] }}</span>
                            @else
                                <span>
                                    <span class="block text-xl font-semibold tracking-[-.04em] sm:text-2xl">{{ $sponsor['name'] }}</span>
                                    @if (filled($sponsor['tagline'] ?? null))
                                        <span class="mt-1 block text-[.55rem] font-medium uppercase tracking-[.28em]">{{ $sponsor['tagline'] }}</span>
                                    @endif
                                </span>
                            @endif

                            @if (filled($sponsor['url'] ?? null))
                                <a href="{{ $sponsor['url'] }}" class="absolute inset-0 rounded-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white" target="_blank" rel="noopener noreferrer">
                                    <span class="sr-only">Kunjungi {{ $sponsor['name'] }}</span>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col gap-3 pt-8 text-xs text-white/38 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ $footerName }}. Semua hak dilindungi.</p>
            <p>{{ $footerLocation }}</p>
        </div>
    </div>
</footer>
