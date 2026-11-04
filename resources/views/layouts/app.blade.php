<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GymPinang - Informasi Gym Lokal Tanjungpinang & Bintan')</title>
    <meta name="description" content="@yield('description', 'Temukan informasi gym, fasilitas, paket harga, lokasi, dan rating gym lokal di Tanjungpinang dan Bintan.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-gray-50 text-gray-800">

    <x-navbar />

    @if (session('success'))
        <div class="mx-auto w-full max-w-6xl px-4 pt-4">
            <div class="rounded border border-green-300 bg-green-50 px-4 py-2 text-sm text-green-800" role="status">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mx-auto w-full max-w-6xl px-4 pt-4">
            <div class="rounded border border-red-300 bg-red-50 px-4 py-2 text-sm text-red-800" role="alert">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @yield('hero')

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
        @yield('content')
    </main>

    <x-footer />

    @stack('scripts')
</body>
</html>