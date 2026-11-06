<footer class="bg-[#0a0a0a] px-5 py-14 text-white sm:px-8 lg:px-12 lg:py-20" data-navbar-tone="dark">
    <div class="mx-auto max-w-[90rem]">
        <div class="grid gap-12 border-b border-white/10 pb-12 lg:grid-cols-[1fr_auto] lg:items-end">
            <div>
                <p class="text-[clamp(3.8rem,9vw,9rem)] font-bold leading-[.8] tracking-[-0.075em]">Cukip<span class="text-[#737373]">Gym.</span></p>
                <p class="mt-7 max-w-md text-sm leading-7 text-white/50">Tempat latihan, kelas kebugaran, dan komunitas untuk membantu kamu bergerak lebih konsisten.</p>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-3 text-sm text-white/65">
                <a href="{{ route('facilities.index') }}" class="transition hover:text-[#d4d4d4]">Fasilitas</a>
                <a href="{{ route('membership.index') }}" class="transition hover:text-[#d4d4d4]">Membership</a>
                <a href="{{ route('classes.index') }}" class="transition hover:text-[#d4d4d4]">Kelas</a>
                <a href="{{ route('register') }}" class="transition hover:text-[#d4d4d4]">Daftar</a>
            </div>
        </div>
        <div class="py-10" role="region" aria-labelledby="sponsors-title">
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2">
                <h3 id="sponsors-title" class="text-[.65rem] font-semibold uppercase tracking-[.22em] text-white/55">Sponsor & partner</h3>
                <span class="rounded-full border border-white/15 px-2.5 py-1 text-[.55rem] text-white/35">Identitas contoh</span>
            </div>
            <ul class="mt-7 grid grid-cols-2 items-center gap-x-7 gap-y-8 text-white/55 sm:grid-cols-4 sm:gap-8" aria-label="Contoh sponsor">
                <li class="flex items-center justify-center gap-2.5">
                    <svg class="size-8" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M5 25 16 6l11 19H5ZM11 19h10"/></svg>
                    <span class="text-xl font-bold tracking-[-.07em] sm:text-2xl">FORMA<span class="text-white/30">.</span></span>
                </li>
                <li class="text-center">
                    <span class="block text-lg font-bold uppercase leading-none tracking-[.08em] sm:text-xl">Pinang</span>
                    <span class="mt-1 block text-[.55rem] font-medium uppercase tracking-[.4em]">Active club</span>
                </li>
                <li class="flex items-center justify-center gap-2.5">
                    <svg class="size-8" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M5 16h22M5 10v12M10 6v20M22 6v20M27 10v12"/></svg>
                    <span class="text-xl font-semibold tracking-[-.06em] sm:text-2xl">rep<span class="font-light">lab</span></span>
                </li>
                <li class="text-center">
                    <span class="block text-xl font-semibold uppercase leading-none tracking-[.16em] sm:text-2xl">Nusa</span>
                    <span class="mt-1 block text-[.55rem] uppercase tracking-[.35em]">Hydrate</span>
                </li>
            </ul>
        </div>
        <div class="flex flex-col gap-3 pt-8 text-xs text-white/38 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} CukipGym. Semua hak dilindungi.</p>
            <p>Gym lokal Tanjungpinang</p>
        </div>
    </div>
</footer>
