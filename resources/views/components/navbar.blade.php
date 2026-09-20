@php
    $links = [
        ['label' => 'Beranda',  'href' => route('home'),          'active' => request()->routeIs('home')],
        ['label' => 'Gym',      'href' => route('gyms.index'),    'active' => request()->routeIs('gyms.*')],
        ['label' => 'Map',      'href' => route('map'),           'active' => request()->routeIs('map')],
        ['label' => 'Kategori', 'href' => route('home') . '#kategori', 'active' => request()->routeIs('categories.*')],
    ];
@endphp

<header class="sticky top-0 z-40 border-b bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        <a href="{{ route('home') }}" class="text-lg font-bold tracking-wide text-green-800">GYMPINANG</a>

        {{-- Menu desktop (>= 768px) --}}
        <nav class="hidden items-center gap-6 text-sm md:flex" aria-label="Menu utama">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"
                   class="{{ $link['active'] ? 'font-semibold text-green-800' : 'text-gray-600 hover:text-green-800' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach

            @auth
                @if (auth()->user()->isAdmin())
                    <a href="/admin" class="text-gray-600 hover:text-green-800">Admin</a>
                @endif
                <a href="{{ route('profile') }}"
                   class="{{ request()->routeIs('profile') ? 'font-semibold text-green-800' : 'text-gray-600 hover:text-green-800' }}">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-red-700">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="rounded bg-green-800 px-4 py-2 font-medium text-white hover:bg-green-900">Login</a>
            @endauth
        </nav>

        {{-- Tombol ☰ (mobile) --}}
        <button id="menu-button" type="button"
                class="-mr-2 rounded p-2 text-gray-700 hover:bg-gray-100 md:hidden"
                aria-controls="mobile-menu" aria-expanded="false" aria-label="Buka menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    {{-- Menu mobile --}}
    <nav id="mobile-menu" class="hidden max-h-[calc(100svh-4rem)] overflow-y-auto border-t bg-white md:hidden" aria-label="Menu mobile">
        <div class="mx-auto flex max-w-6xl flex-col px-4 py-2 text-sm">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"
                   class="border-b border-gray-100 py-3 {{ $link['active'] ? 'font-semibold text-green-800' : 'text-gray-700' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach

            @auth
                @if (auth()->user()->isAdmin())
                    <a href="/admin" class="border-b border-gray-100 py-3 text-gray-700">Admin</a>
                @endif
                <a href="{{ route('profile') }}" class="border-b border-gray-100 py-3 text-gray-700">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 text-left text-red-700">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="my-3 rounded bg-green-800 px-4 py-3 text-center font-medium text-white">Login</a>
            @endauth
        </div>
    </nav>
</header>