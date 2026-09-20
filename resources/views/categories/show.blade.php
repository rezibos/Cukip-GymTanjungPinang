@extends('layouts.app')

@section('title', 'Kategori ' . $category->name . ' - GymPinang')
@section('description', 'Daftar gym kategori ' . $category->name . ' di Tanjungpinang dan Bintan.')

@section('content')
    <p class="text-sm"><a href="{{ route('home') }}#kategori" class="text-green-800 hover:underline">&larr; Semua kategori</a></p>

    <h1 class="mt-2 text-2xl font-bold">{{ $category->name }}</h1>
    @if ($category->description)
        <p class="mt-1 text-gray-600">{{ $category->description }}</p>
    @endif

    @if ($gyms->count())
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($gyms as $gym)
                <x-gym-card :gym="$gym" />
            @endforeach
        </div>
        <div class="mt-6">{{ $gyms->links() }}</div>
    @else
        <p class="mt-6 text-gray-600">Belum ada gym di kategori ini.</p>
    @endif
@endsection