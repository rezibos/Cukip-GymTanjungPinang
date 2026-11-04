@extends('layouts.app')

@section('title', 'CukipGym - Gym Lokal Tanjungpinang')
@section('description', $profile->description)

@section('hero')
<section class="bg-green-800 text-white">
    <div class="mx-auto max-w-6xl px-4 py-12 sm:py-16">
        <h1 class="max-w-2xl text-3xl font-bold leading-tight sm:text-4xl">{{ $profile->name }}</h1>
        <p class="mt-4 max-w-xl text-green-100">{{ $profile->description }}</p>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('membership.index') }}"
            class="rounded bg-white px-5 py-3 text-center font-medium text-green-900 hover:bg-green-50">
                Lihat Paket Membership
            </a>
            <a href="{{ route('classes.index') }}"
            class="rounded border border-white px-5 py-3 text-center font-medium text-white hover:bg-green-900">
                Jadwal Kelas
            </a>
        </div>
    </div>
</section>
@endsection

@section('content')
<div class="space-y-12">

    {{-- Fasilitas --}}
    <section aria-labelledby="judul-fasilitas">
        <div class="mb-4 flex items-end justify-between gap-4">
            <h2 id="judul-fasilitas" class="text-xl font-semibold">Fasilitas</h2>
            <a href="{{ route('facilities.index') }}" class="text-sm text-green-800 hover:underline">Lihat semua</a>
        </div>
        <ul class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach ($facilities as $facility)
                <li class="rounded-lg border border-gray-200 bg-white p-3 text-center text-sm font-medium">
                    {{ $facility->name }}
                </li>
            @endforeach
        </ul>
    </section>

    {{-- Paket membership --}}
    <section aria-labelledby="judul-paket">
        <div class="mb-4 flex items-end justify-between gap-4">
            <h2 id="judul-paket" class="text-xl font-semibold">Paket Membership</h2>
            <a href="{{ route('membership.index') }}" class="text-sm text-green-800 hover:underline">Lihat semua</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($plans as $plan)
                <x-plan-card :plan="$plan" />
            @endforeach
        </div>
    </section>

    {{-- Testimoni --}}
    @if ($reviews->count())
        <section aria-labelledby="judul-testimoni">
            <h2 id="judul-testimoni" class="mb-4 text-xl font-semibold">Kata Member Kami</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($reviews as $review)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 text-sm">
                        <x-rating-stars :rating="$review->rating" />
                        <p class="mt-2 break-words text-gray-700">{{ $review->review }}</p>
                        <p class="mt-2 font-medium text-gray-900">{{ $review->user->name }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Lokasi --}}
    <section aria-labelledby="judul-lokasi">
        <h2 id="judul-lokasi" class="mb-4 text-xl font-semibold">Lokasi</h2>
        <p class="mb-3 text-sm text-gray-700">{{ $profile->address }}</p>
        <x-gym-map :markers="$marker" />
        <div class="mt-3 flex flex-col gap-2 sm:flex-row">
            @if ($profile->whatsapp_url)
                <a href="{{ $profile->whatsapp_url }}" target="_blank" rel="noopener noreferrer"
                   class="rounded bg-green-800 px-4 py-2.5 text-center font-medium text-white hover:bg-green-900">
                    Hubungi via WhatsApp
                </a>
            @endif
            @if ($profile->maps_url)
                <a href="{{ $profile->maps_url }}" target="_blank" rel="noopener noreferrer"
                   class="rounded border border-green-800 px-4 py-2.5 text-center font-medium text-green-800 hover:bg-green-50">
                    Buka di Google Maps
                </a>
            @endif
        </div>
    </section>

</div>
@endsection