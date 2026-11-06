<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GymPinang - Informasi Gym Lokal Tanjungpinang & Bintan')</title>
    <meta name="description" content="@yield('description', 'Temukan informasi gym, fasilitas, paket harga, lokasi, dan rating gym lokal di Tanjungpinang dan Bintan.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-[#f5f5f5] text-[#171717] antialiased">

    <x-navbar />

    @if (session('success'))
        <div class="mx-auto w-full max-w-6xl px-4 pt-4">
            <div class="rounded border border-neutral-300 bg-neutral-50 px-4 py-2 text-sm text-neutral-900" role="status">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mx-auto w-full max-w-6xl px-4 pt-4">
            <div class="rounded border border-neutral-400 bg-neutral-100 px-4 py-2 text-sm text-neutral-900" role="alert">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @yield('hero')

    <main class="@yield('main-class', 'mx-auto w-full max-w-6xl flex-1 px-4 pb-8 pt-28')">
        @yield('content')
    </main>

    @section('footer')
        <x-footer />
    @show

    @stack('scripts')
</body>
</html>
