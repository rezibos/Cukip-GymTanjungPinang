@extends('layouts.app')

@section('title', 'Daftar Gym Tanjungpinang & Bintan - GymPinang')
@section('description', 'Cari gym lokal di Tanjungpinang dan Bintan berdasarkan nama, wilayah, dan kategori.')

@section('content')
@php
    $base = request()->except('page');
    $url = fn (array $override) => route('gyms.index', array_filter(array_merge($base, $override)));
@endphp

<h1 class="text-2xl font-bold">Semua Gym</h1>

<div class="mt-4 space-y-4 rounded-lg border border-gray-200 bg-white p-4">

    {{-- Search + kecamatan --}}
    <form method="GET" action="{{ route('gyms.index') }}" class="flex flex-col gap-2 sm:flex-row">
        @if ($region) <input type="hidden" name="region" value="{{ $region->value }}"> @endif
        @if ($categorySlug) <input type="hidden" name="category" value="{{ $categorySlug }}"> @endif
        @if ($nearby)
            <input type="hidden" name="lat" value="{{ $lat }}">
            <input type="hidden" name="lng" value="{{ $lng }}">
        @endif

        <label for="search" class="sr-only">Cari nama gym</label>
        <input id="search" name="search" type="search" value="{{ $search }}" placeholder="Cari gym..."
               class="min-w-0 flex-1 rounded border border-gray-300 px-3 py-2">

        @if ($districts)
            <label for="district" class="sr-only">Kecamatan</label>
            <select id="district" name="district" onchange="this.form.submit()"
                    class="rounded border border-gray-300 bg-white px-3 py-2.5 sm:max-w-52">
                <option value="">Semua kecamatan</option>
                @foreach ($districts as $d)
                    <option value="{{ $d }}" @selected($district === $d)>{{ $d }}</option>
                @endforeach
            </select>
        @endif

        <button type="submit" class="rounded bg-green-800 px-5 py-2 font-medium text-white hover:bg-green-900">
            Cari
        </button>
    </form>

    {{-- Filter wilayah --}}
    <div class="flex flex-wrap items-center gap-2">
        <span class="w-full text-sm font-medium sm:w-auto">Wilayah:</span>
        <x-pill :href="$url(['region' => null, 'district' => null])" :active="! $region">Semua</x-pill>
        @foreach (\App\Enums\Region::cases() as $r)
            <x-pill :href="$url(['region' => $r->value, 'district' => null])" :active="$region === $r">
                {{ $r->value }}
            </x-pill>
        @endforeach
    </div>

    {{-- Filter kategori --}}
    <div class="flex flex-wrap items-center gap-2">
        <span class="w-full text-sm font-medium sm:w-auto">Kategori:</span>
        <x-pill :href="$url(['category' => null])" :active="$categorySlug === ''">Semua</x-pill>
        @foreach ($categories as $c)
            <x-pill :href="$url(['category' => $c->slug])" :active="$categorySlug === $c->slug">
                {{ $c->name }}
            </x-pill>
        @endforeach
    </div>

    {{-- Gym terdekat --}}
    <div class="flex flex-wrap items-center gap-2">
        <span class="w-full text-sm font-medium sm:w-auto">Urutan:</span>
        @if ($nearby)
            <x-pill :href="$url(['lat' => null, 'lng' => null])">Urut nama</x-pill>
            <span class="rounded border border-green-800 bg-green-800 px-3 py-2 text-sm text-white">Terdekat dari lokasi saya</span>
        @else
            <button type="button" id="nearby-button"
                    class="rounded border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 hover:border-green-800">
                Gym terdekat dari lokasi saya
            </button>
        @endif
        <p id="nearby-error" class="hidden w-full text-sm text-red-600" role="alert"></p>
    </div>
</div>

<p class="mt-6 text-sm text-gray-600">{{ $gyms->total() }} gym ditemukan</p>

@if ($gyms->count())
    <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($gyms as $gym)
            <x-gym-card :gym="$gym" />
        @endforeach
    </div>

    <div class="mt-6">{{ $gyms->links() }}</div>
@else
    <div class="mt-3 rounded-lg border border-dashed border-gray-300 bg-white p-8 text-center">
        <p class="font-medium">Gym tidak ditemukan.</p>
        <p class="mt-1 text-sm text-gray-600">Coba kata kunci lain atau hapus filter.</p>
        <a href="{{ route('gyms.index') }}" class="mt-3 inline-block text-sm text-green-800 underline">Reset filter</a>
    </div>
@endif
@endsection

@push('scripts')
<script>
    // Gym terdekat: minta lokasi browser, lalu muat ulang halaman dengan ?lat=...&lng=...
    const nearbyButton = document.getElementById('nearby-button');
    const nearbyError = document.getElementById('nearby-error');

    if (nearbyButton) {
        nearbyButton.addEventListener('click', () => {
            const showError = (text) => {
                nearbyError.textContent = text;
                nearbyError.classList.remove('hidden');
                nearbyButton.disabled = false;
                nearbyButton.textContent = 'Gym terdekat dari lokasi saya';
            };

            if (!navigator.geolocation) {
                showError('Browser Anda tidak mendukung fitur lokasi.');
                return;
            }

            nearbyButton.disabled = true;
            nearbyButton.textContent = 'Mencari lokasi...';
            nearbyError.classList.add('hidden');

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const url = new URL(window.location.href);
                    url.searchParams.set('lat', pos.coords.latitude.toFixed(6));
                    url.searchParams.set('lng', pos.coords.longitude.toFixed(6));
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                },
                () => showError('Lokasi tidak dapat diakses. Izinkan akses lokasi di browser, lalu coba lagi.'),
                { timeout: 10000 }
            );
        });
    }
</script>
@endpush