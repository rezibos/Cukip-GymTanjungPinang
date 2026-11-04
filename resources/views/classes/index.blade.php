@extends('layouts.app')

@section('title', 'Jadwal Kelas - CukipGym')
@section('description', 'Jadwal kelas kebugaran CukipGym untuk 7 hari ke depan.')

@section('content')
<h1 class="text-2xl font-bold">Jadwal Kelas</h1>
<p class="mt-1 text-sm text-gray-600">
    {{ $start->translatedFormat('d F Y') }} - {{ $end->translatedFormat('d F Y') }}
</p>

@if (! auth()->check())
    <div class="mt-4 rounded border border-dashed border-gray-300 bg-white p-4 text-sm">
        <a href="{{ route('login') }}" class="text-green-800 underline">Login</a> untuk booking kelas.
        Booking hanya untuk member aktif.
    </div>
@elseif (! auth()->user()->isMember())
    <div class="mt-4 rounded border border-dashed border-gray-300 bg-white p-4 text-sm">
        Booking kelas hanya untuk member aktif.
        <a href="{{ route('membership.index') }}" class="text-green-800 underline">Lihat paket membership</a>.
    </div>
@endif

@forelse ($sessions as $date => $daySessions)
    <section class="mt-6" aria-labelledby="tanggal-{{ $date }}">
        <h2 id="tanggal-{{ $date }}" class="text-lg font-semibold">
            {{ \Illuminate\Support\Carbon::parse($date)->translatedFormat('l, d F Y') }}
        </h2>

        <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($daySessions as $session)
                @php
                    $remaining = $session->capacity - $session->active_bookings_count;
                    $isBooked = in_array($session->id, $myBookedSessionIds);
                @endphp
                <div class="rounded-lg border border-gray-200 bg-white p-4 text-sm">
                    <p class="break-words font-medium">{{ $session->gymClass->name }}</p>
                    <p class="text-gray-600">{{ $session->gymClass->time_range }}</p>
                    @if ($session->gymClass->instructor)
                        <p class="text-gray-500">Instruktur: {{ $session->gymClass->instructor }}</p>
                    @endif
                    <p class="mt-1 {{ $remaining <= 0 ? 'text-red-600' : 'text-gray-600' }}">
                        {{ $remaining > 0 ? "$remaining slot tersisa" : 'Penuh' }}
                    </p>

                    @auth
                        @if ($isBooked)
                            <span class="mt-3 inline-block rounded bg-green-100 px-3 py-2 text-xs font-medium text-green-800">
                                Sudah Booking
                            </span>
                        @elseif (auth()->user()->isMember() && $remaining > 0)
                            <form method="POST" action="{{ route('classes.book', $session) }}" class="mt-3">
                                @csrf
                                <button type="submit" class="w-full rounded bg-green-800 px-3 py-2 text-xs font-medium text-white hover:bg-green-900">
                                    Booking
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            @endforeach
        </div>
    </section>
@empty
    <div class="mt-6 rounded-lg border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-600">
        Jadwal belum tersedia untuk periode ini. Silakan cek kembali nanti.
    </div>
@endforelse
@endsection