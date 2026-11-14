@php
    $links = [
        ['label' => 'Beranda', 'href' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Fasilitas', 'href' => route('facilities.index'), 'active' => request()->routeIs('facilities.*')],
        ['label' => 'Membership', 'href' => route('membership.index'), 'active' => request()->routeIs('membership.*')],
        ['label' => 'Kelas', 'href' => route('classes.index'), 'active' => request()->routeIs('classes.*')],
        ['label' => 'Pesanan Saya', 'href' => route('payments.history'), 'active' => request()->routeIs('payments.*')],
    ];
@endphp

<header class="fixed inset-x-0 top-0 z-50 px-5 py-6 sm:px-8 lg:px-12" data-navbar>
    <div class="mx-auto flex max-w-[90rem] items-center justify-between">
        <a href="{{ route('home') }}" class="navbar-brand text-xl font-bold tracking-[-0.05em] sm:text-2xl" aria-label="CukipGym beranda" data-navbar-brand>
            CukipGym
        </a>

        <button id="menu-button" type="button" class="rounded-full border border-[#a3a3a3] bg-white px-6 py-3 text-[.65rem] font-semibold uppercase tracking-[0.16em] text-[#171717] shadow-[0_8px_24px_rgba(0,0,0,.18)] transition hover:-translate-y-0.5 hover:bg-[#d4d4d4] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white" aria-controls="mobile-menu" aria-expanded="false">
            Menu
        </button>
    </div>

    <nav id="mobile-menu" class="absolute right-5 top-[5.25rem] hidden w-[calc(100%-2.5rem)] max-w-xs overflow-hidden rounded-[1.5rem] border border-white/10 bg-[#171717]/95 p-3 text-sm text-white shadow-2xl backdrop-blur-xl sm:right-8 lg:right-12" aria-label="Menu utama">
        <div class="flex flex-col">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}" class="flex items-center justify-between rounded-xl px-4 py-3.5 transition hover:bg-white/10 {{ $link['active'] ? 'bg-white/10 text-[#d4d4d4]' : 'text-white/75' }}">
                    {{ $link['label'] }}
                    <span aria-hidden="true">↗</span>
                </a>
            @endforeach

            @auth
                @if (auth()->user()->isAdmin())
                    <a href="/admin" class="rounded-xl px-4 py-3.5 text-white/75 transition hover:bg-white/10">Admin</a>
                @else
                    <a href="{{ route('filament.member.pages.dashboard') }}" class="rounded-xl px-4 py-3.5 text-white/75 transition hover:bg-white/10">Dashboard member</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-xl px-4 py-3.5 text-left text-[#d4d4d4] transition hover:bg-white/10">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mt-2 rounded-full bg-white px-5 py-3.5 text-center font-semibold text-[#171717] transition hover:bg-[#737373] hover:text-white">Masuk</a>
            @endauth
        </div>
    </nav>
</header>
