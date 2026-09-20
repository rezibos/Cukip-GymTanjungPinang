@extends('layouts.app')

@section('title', 'GymPinang - Temukan Gym Lokal di Tanjungpinang & Bintan')

@section('hero')
<section class="bg-green-800 text-white">
    <div class="mx-auto max-w-6xl px-4 py-12 sm:py-16">
        <h1 class="max-w-2xl text-3xl font-bold leading-tight sm:text-4xl">
            Temukan Gym Lokal di Tanjungpinang &amp; Bintan
        </h1>
        <p class="mt-4 max-w-xl text-green-100">
            Temukan informasi gym, fasilitas, paket harga, lokasi, dan rating gym lokal di Tanjungpinang dan Bintan.
        </p>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('gyms.index') }}"
               class="rounded bg-white px-5 py-3 text-center font-medium text-green-900 hover:bg-green-50">
                Jelajahi Gym
            </a>
            <a href="{{ route('map') }}"
               class="rounded border border-white px-5 py-3 text-center font-medium text-white hover:bg-green-900">
                Lihat Map
            </a>
        </div>
    </div>
</section>
@endsection

@section('content')
<div class="space-y-12">

    {{-- Kategori --}}
    <section id="kategori" aria-labelledby="judul-kategori">
        <h2 id="judul-kategori" class="mb-4 text-xl font-semibold">Kategori Gym</h2>
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('categories.show', $category) }}"
                   class="rounded-lg border border-gray-200 bg-white p-4 hover:border-green-800">
                    <p class="font-medium text-gray-900">{{ $category->name }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $category->gyms_count }} gym</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Gym populer --}}
    <section aria-labelledby="judul-populer">
        <div class="mb-4 flex items-end justify-between gap-4">
            <h2 id="judul-populer" class="text-xl font-semibold">Gym Populer</h2>
            <a href="{{ route('gyms.index') }}" class="text-sm text-green-800 hover:underline">Lihat semua</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($popularGyms as $gym)
                <x-gym-card :gym="$gym" />
            @endforeach
        </div>
    </section>

    {{-- Gym terbaru --}}
    <section aria-labelledby="judul-terbaru">
        <h2 id="judul-terbaru" class="mb-4 text-xl font-semibold">Gym Terbaru</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($latestGyms as $gym)
                <x-gym-card :gym="$gym" />
            @endforeach
        </div>
    </section>

    {{-- Preview peta --}}
    <section aria-labelledby="judul-peta">
        <div class="mb-4 flex items-end justify-between gap-4">
            <h2 id="judul-peta" class="text-xl font-semibold">Peta Gym</h2>
            <a href="{{ route('map') }}" class="text-sm text-green-800 hover:underline">Buka peta penuh</a>
        </div>
        <x-gym-map :markers="$markers" />
    </section>

    {{-- Tentang --}}
    <section class="rounded-lg border border-gray-200 bg-white p-5" aria-labelledby="judul-tentang">
        <h2 id="judul-tentang" class="text-xl font-semibold">Tentang GymPinang</h2>
        <p class="mt-2 max-w-3xl text-sm leading-relaxed text-gray-600">
            Informasi gym lokal di Tanjungpinang dan Bintan selama ini tersebar di media sosial dan dari mulut
            ke mulut. GymPinang mengumpulkan lokasi, harga paket, fasilitas, jam operasional, dan rating dalam
            satu tempat, sehingga lebih mudah membandingkan dan memilih gym yang sesuai kebutuhan Anda.
        </p>
    </section>

</div>
@endsection