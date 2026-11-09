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

    @section('navbar')
        <x-navbar />
    @show

    <x-notifications />

    @section('floating-actions')
        <x-floating-actions />
    @show

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
