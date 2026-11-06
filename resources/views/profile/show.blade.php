@extends('layouts.app')

@section('title', 'Profil - CukipGym')

@section('content')
    <h1 class="text-xl font-bold">Profil Saya</h1>

    <div class="mt-4 rounded border bg-white p-4 text-sm">
        <p><span class="font-medium">Nama:</span> {{ $user->name }}</p>
        <p><span class="font-medium">Email:</span> {{ $user->email }}</p>
        <p><span class="font-medium">Telepon:</span> {{ $user->phone ?? '-' }}</p>
        <p><span class="font-medium">Role:</span> {{ $user->role }}</p>
    </div>

    {{-- Status membership --}}
    <h2 class="mt-6 text-lg font-semibold">Status Membership</h2>
    <div class="mt-2 rounded border bg-white p-4 text-sm">
        @if ($activeMembership)
            <p class="font-medium text-neutral-900">
                Member Aktif — {{ $activeMembership->plan->name }}
            </p>
            <p class="mt-1 text-gray-600">
                Berlaku sampai {{ $activeMembership->end_date->translatedFormat('d F Y') }}
            </p>
        @else
            <p class="text-gray-600">Anda belum memiliki membership aktif.</p>
            <a href="{{ route('membership.index') }}" class="mt-2 inline-block text-neutral-900 underline">
                Lihat Paket Membership
            </a>
        @endif
    </div>

    {{-- Riwayat transaksi --}}
    <h2 class="mt-6 text-lg font-semibold">Riwayat Transaksi</h2>
    @forelse ($transactions as $trx)
        <div class="mt-2 rounded border bg-white p-4 text-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <p class="font-medium">{{ $trx->invoice_number }}</p>
                <span class="rounded px-2 py-1 text-xs font-medium
                    {{ match($trx->status->value) {
                        'paid' => 'bg-neutral-100 text-neutral-900',
                        'rejected' => 'bg-neutral-200 text-neutral-900',
                        default => 'bg-neutral-200 text-neutral-800',
                    } }}">
                    {{ $trx->status->label() }}
                </span>
            </div>
            <p class="mt-1 text-gray-600">{{ $trx->membership->plan->name }} — {{ $trx->formatted_amount }}</p>
            <p class="text-gray-500">{{ $trx->created_at->translatedFormat('d F Y, H:i') }}</p>

            @if ($trx->status->value === 'pending' && ! $trx->payment_proof)
                <a href="{{ route('membership.invoice', $trx) }}" class="mt-2 inline-block text-sm text-neutral-900 underline">
                    Upload bukti transfer
                </a>
            @endif
        </div>
    @empty
        <p class="mt-2 text-sm text-gray-500">Belum ada transaksi.</p>
    @endforelse

    {{-- Booking kelas mendatang --}}
    <h2 class="mt-6 text-lg font-semibold">Booking Kelas Mendatang</h2>
    @forelse ($upcomingBookings as $booking)
        <div class="mt-2 flex flex-wrap items-center justify-between gap-2 rounded border bg-white p-4 text-sm">
            <div class="min-w-0 break-words">
                <p class="font-medium">{{ $booking->session->gymClass->name }}</p>
                <p class="text-gray-600">
                    {{ $booking->session->date->translatedFormat('l, d F Y') }}
                    — {{ $booking->session->gymClass->time_range }}
                </p>
            </div>
            <form method="POST" action="{{ route('classes.bookings.cancel', $booking) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-neutral-800 underline">Batalkan</button>
            </form>
        </div>
    @empty
        <p class="mt-2 text-sm text-gray-500">Belum ada booking kelas.</p>
    @endforelse
@endsection