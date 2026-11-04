@extends('layouts.app')

@section('title', 'Fasilitas - CukipGym')

@section('content')
    <h1 class="text-2xl font-bold">Fasilitas {{ $profile->name }}</h1>
    <p class="mt-1 text-sm text-gray-600">Jam operasional: {{ $profile->opening_hours }}</p>

    <ul class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
        @foreach ($facilities as $facility)
            <li class="rounded-lg border border-gray-200 bg-white p-4 text-sm">
                <p class="font-medium">{{ $facility->name }}</p>
                @if ($facility->description)
                    <p class="mt-1 text-gray-500">{{ $facility->description }}</p>
                @endif
            </li>
        @endforeach
    </ul>
@endsection