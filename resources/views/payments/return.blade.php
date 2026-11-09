@extends('layouts.app')

@section('title', 'Status Pembayaran - CukipGym')
@section('main-class', 'w-full flex-1 bg-[#f5f5f5]')

@section('content')
<section class="flex min-h-[70vh] items-center px-5 pb-20 pt-32 text-[#171717] sm:px-8 lg:px-12" data-navbar-tone="light">
    <div class="mx-auto w-full max-w-2xl rounded-[1.7rem] border border-black/10 bg-white p-8 sm:p-12">
        <p class="section-kicker">Pembayaran Midtrans</p>
        @if ($result === 'finish')
            <h1 class="mt-5 text-4xl font-semibold tracking-[-.055em]">Pembayaran sedang diverifikasi.</h1>
            <p class="mt-5 text-sm leading-7 text-[#737373]">Status membership akan diperbarui otomatis setelah Midtrans mengirimkan konfirmasi ke sistem.</p>
        @elseif ($result === 'pending')
            <h1 class="mt-5 text-4xl font-semibold tracking-[-.055em]">Pembayaran belum selesai.</h1>
            <p class="mt-5 text-sm leading-7 text-[#737373]">Kamu dapat melanjutkan pembayaran dari invoice selama transaksi masih berlaku.</p>
        @else
            <h1 class="mt-5 text-4xl font-semibold tracking-[-.055em]">Pembayaran belum berhasil.</h1>
            <p class="mt-5 text-sm leading-7 text-[#737373]">Silakan kembali ke invoice dan coba lagi atau pilih metode pembayaran lain.</p>
        @endif

        <div class="mt-8 flex flex-wrap gap-3">
            @if ($transaction)
                <a href="{{ route('membership.invoice', $transaction) }}" class="inline-flex min-h-11 items-center rounded-full bg-[#171717] px-6 text-xs font-semibold text-white">Lihat invoice</a>
            @endif
            <a href="{{ auth()->check() ? route('profile') : route('home') }}" class="inline-flex min-h-11 items-center rounded-full border border-black/10 px-6 text-xs font-semibold">{{ auth()->check() ? 'Lihat profil' : 'Kembali ke beranda' }}</a>
        </div>
    </div>
</section>
@endsection
