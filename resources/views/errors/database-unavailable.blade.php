<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Database Tidak Terhubung - GymPinang</title>
    @vite('resources/css/app.css')
</head>
<body class="flex min-h-screen flex-col bg-[#f5f5f5] font-sans text-[#171717] antialiased">
    <main class="flex min-h-[70vh] flex-1 items-center justify-center px-5 py-20 sm:px-8">
        <div class="mx-auto max-w-xl text-center">
            <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-[#171717] text-white" aria-hidden="true">
                <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <ellipse cx="12" cy="5" rx="7" ry="3" />
                    <path d="M5 5v6c0 1.7 3.1 3 7 3s7-1.3 7-3V5M5 11v6c0 1.7 3.1 3 7 3 1.1 0 2.1-.1 3-.3" />
                    <path d="m18 16 4 4m0-4-4 4" />
                </svg>
            </div>

            <p class="mt-7 text-xs font-semibold uppercase tracking-[.2em] text-[#737373]">Error 503</p>
            <h1 class="mt-3 text-3xl font-bold tracking-[-.04em] sm:text-5xl">Database tidak terhubung</h1>
            <p class="mx-auto mt-5 max-w-md text-sm leading-7 text-[#666] sm:text-base">
                Website belum dapat mengambil data. Silakan tunggu sebentar, lalu coba muat ulang halaman.
            </p>

            <button
                class="mt-8 inline-flex min-h-12 items-center justify-center gap-2 rounded-lg bg-[#171717] px-6 text-sm font-semibold text-white transition hover:bg-[#303030] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]"
                type="button"
                onclick="window.location.reload()"
            >
                <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 11a8.1 8.1 0 1 0 2 5.3M20 4v7h-7" />
                </svg>
                Coba lagi
            </button>
        </div>
    </main>

    <x-footer />
</body>
</html>
