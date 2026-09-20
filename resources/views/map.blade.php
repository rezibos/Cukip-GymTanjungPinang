@extends('layouts.app')

@section('title', 'Peta Gym Tanjungpinang & Bintan - GymPinang')
@section('description', 'Lihat lokasi gym lokal di Tanjungpinang dan Bintan pada peta.')

@section('content')
    <h1 class="text-2xl font-bold">Peta Gym</h1>
    <p class="mt-1 text-sm text-gray-600">Klik atau ketuk penanda untuk melihat ringkasan gym, lalu buka detailnya.</p>

    <div class="mt-4">
        <x-gym-map :markers="$markers" :full="true" height="h-[70svh] min-h-80" />
    </div>

    <p class="mt-3 text-sm text-gray-500">
        {{ $markers->count() }} gym ditampilkan di peta.
        @if ($withoutCoordinates > 0)
            {{ $withoutCoordinates }} gym belum memiliki koordinat.
        @endif
    </p>
@endsection