@extends('layouts.app')

@section('title', 'Profil - GymPinang')

@section('content')
    <h1 class="text-xl font-bold">Profil Saya</h1>

    <div class="mt-4 rounded border bg-white p-4 text-sm">
        <p><span class="font-medium">Nama:</span> {{ $user->name }}</p>
        <p><span class="font-medium">Email:</span> {{ $user->email }}</p>
        <p><span class="font-medium">Role:</span> {{ $user->role }}</p>
    </div>

    <h2 class="mt-6 text-lg font-semibold">Review Saya</h2>

    @forelse ($reviews as $review)
        <div class="mt-3 rounded border bg-white p-4 text-sm">
            <p class="font-medium">{{ $review->gym->name }} - {{ $review->rating }}/5</p>
            <p class="mt-1">{{ $review->review }}</p>
        </div>
    @empty
        <p class="mt-2 text-sm text-gray-500">Anda belum menulis review.</p>
    @endforelse
@endsection