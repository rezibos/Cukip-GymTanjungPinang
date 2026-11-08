@extends('layouts.app')

@section('title', 'Profil - CukipGym')
@section('description', 'Kelola membership, pantau pembayaran, dan lihat jadwal kelas pilihanmu di CukipGym.')
@section('main-class', 'w-full flex-1 bg-white')

@section('content')
@php
    $pendingTransaction = null;
    foreach ($transactions as $transaction) {
        if ($transaction->status->value === 'pending') {
            $pendingTransaction = $transaction;
            break;
        }
    }
@endphp

<section class="px-5 pb-20 pt-32 text-[#171717] sm:px-8 lg:px-12 lg:pb-24 lg:pt-40" aria-labelledby="profile-title" data-navbar-tone="light">
    <div class="mx-auto max-w-[80rem]">
        <header class="mb-10 flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
            <div class="min-w-0">
                <p class="section-kicker">Ruang personalmu</p>
                <h1 id="profile-title" class="mt-5 break-words text-[clamp(2.25rem,4vw,4rem)] font-semibold leading-[1.05] tracking-[-.055em]">Halo, <span class="text-[#858585]">{{ $user->name }}.</span></h1>
                <p class="mt-4 text-sm leading-7 text-[#737373]">Satu tempat untuk membership, pembayaran, dan jadwal latihanmu.</p>
            </div>
            <a href="{{ route('classes.index') }}" class="inline-flex w-fit shrink-0 items-center gap-6 rounded-full border border-black/15 px-5 py-3.5 text-xs font-semibold transition hover:bg-[#171717] hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#171717]">Cari kelas berikutnya <span aria-hidden="true">↗</span></a>
        </header>

        <div class="grid items-start gap-6 xl:grid-cols-[18rem_minmax(0,1fr)]">
            <aside class="rounded-[1.75rem] border border-black/[.04] bg-[#f7f7f7] p-7 xl:sticky xl:top-28" aria-labelledby="account-title">
                <div class="flex items-center gap-4 xl:flex-col xl:items-start">
                    <span class="grid size-16 shrink-0 place-items-center rounded-[1.25rem] bg-[#171717] text-2xl font-semibold text-white" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    <div class="min-w-0">
                        <h2 id="account-title" class="break-words text-lg font-semibold tracking-[-.035em]">{{ $user->name }}</h2>
                        <p class="mt-1 text-xs text-[#858585]">{{ $user->isAdmin() ? 'Administrator' : 'Akun CukipGym' }}</p>
                    </div>
                </div>
                <dl class="mt-7 grid gap-5 border-t border-black/[.07] pt-6 text-xs sm:grid-cols-3 xl:grid-cols-1">
                    <div><dt class="text-[#a3a3a3]">Alamat email</dt><dd class="mt-1.5 break-all font-medium leading-6 text-[#525252]">{{ $user->email }}</dd></div>
                    <div><dt class="text-[#a3a3a3]">Nomor telepon</dt><dd class="mt-1.5 break-words font-medium leading-6 text-[#525252]">{{ $user->phone ?: 'Belum ditambahkan' }}</dd></div>
                    <div><dt class="text-[#a3a3a3]">Bergabung sejak</dt><dd class="mt-1.5 font-medium leading-6 text-[#525252]">{{ $user->created_at->translatedFormat('d M Y') }}</dd></div>
                </dl>
                <nav class="mt-7 flex flex-wrap gap-x-5 gap-y-3 border-t border-black/[.07] pt-6 text-xs text-[#737373] xl:flex-col" aria-label="Navigasi profil">
                    <a href="#membership-saya" class="transition hover:text-[#171717]">Membership saya <span class="ml-2 opacity-40" aria-hidden="true">↗</span></a>
                    <a href="#transaksi-saya" class="transition hover:text-[#171717]">Riwayat transaksi <span class="ml-2 opacity-40" aria-hidden="true">↗</span></a>
                    <a href="#kelas-saya" class="transition hover:text-[#171717]">Booking kelas <span class="ml-2 opacity-40" aria-hidden="true">↗</span></a>
                </nav>
                <form method="POST" action="{{ route('logout') }}" class="mt-7">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-[#a3a3a3] underline decoration-black/15 underline-offset-4 transition hover:text-[#171717]">Keluar akun</button>
                </form>
            </aside>

            <div class="min-w-0">
                <div class="grid gap-4 lg:grid-cols-[1fr_.38fr] xl:grid-cols-[1fr_.4fr]">
                    <section id="membership-saya" class="relative isolate flex min-h-64 scroll-mt-28 flex-col overflow-hidden rounded-[1.75rem] bg-[#171717] p-7 text-white sm:p-8" aria-labelledby="membership-status-title">
                        <div class="pointer-events-none absolute -right-32 -top-28 -z-10 size-80 rounded-full border border-white/[.07] p-12" aria-hidden="true"><div class="size-full rounded-full border border-white/[.05]"></div></div>
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <h2 id="membership-status-title" class="text-[.65rem] font-medium uppercase tracking-[.18em] text-white/45">Membershipmu</h2>
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 px-3 py-1.5 text-[.6rem] text-white/75"><span class="size-1 rounded-full bg-white/60" aria-hidden="true"></span>{{ $activeMembership ? 'Aktif' : ($pendingTransaction ? 'Dalam proses' : 'Belum aktif') }}</span>
                        </div>
                        @if ($activeMembership)
                            <h3 class="mt-7 break-words text-4xl font-semibold tracking-[-.05em]">{{ $activeMembership->plan->name }}</h3>
                            <p class="mt-3 text-sm text-white/55">Ruang latihanmu sudah siap. Pertahankan ritmemu.</p>
                            <div class="mt-auto flex flex-wrap items-end justify-between gap-5 pt-8">
                                <div><p class="text-[.6rem] text-white/35">Aktif sampai</p><p class="mt-1 text-sm font-medium tabular-nums">{{ $activeMembership->end_date->translatedFormat('d M Y') }}</p></div>
                                <a href="{{ route('classes.index') }}" class="inline-flex items-center gap-5 text-xs font-semibold text-white/80 transition hover:text-white">Lihat jadwal <span aria-hidden="true">↗</span></a>
                            </div>
                        @elseif ($pendingTransaction)
                            <h3 class="mt-7 text-3xl font-semibold leading-tight tracking-[-.04em]">Satu langkah lagi.</h3>
                            <p class="mt-3 max-w-sm text-sm leading-7 text-white/55">{{ $pendingTransaction->payment_proof ? 'Bukti pembayaran sudah diunggah. Pantau status konfirmasi pada invoice.' : 'Lanjutkan pembayaran dan unggah bukti transfer untuk memproses membershipmu.' }}</p>
                            <a href="{{ route('membership.invoice', $pendingTransaction) }}" class="mt-6 inline-flex w-fit items-center gap-6 rounded-full bg-white px-5 py-3 text-xs font-semibold text-[#171717] transition hover:bg-[#d4d4d4]">{{ $pendingTransaction->payment_proof ? 'Lihat pembayaran' : 'Lanjutkan pembayaran' }} <span aria-hidden="true">↗</span></a>
                        @else
                            <h3 class="mt-7 text-3xl font-semibold leading-tight tracking-[-.04em]">Rutinitas baru,<br><span class="text-white/45">mulai dari sini.</span></h3>
                            <p class="mt-3 text-sm leading-7 text-white/55">Kamu belum memiliki membership aktif.</p>
                            <a href="{{ route('membership.index') }}" class="mt-6 inline-flex w-fit items-center gap-6 rounded-full bg-white px-5 py-3 text-xs font-semibold text-[#171717] transition hover:bg-[#d4d4d4]">Pilih membership <span aria-hidden="true">↗</span></a>
                        @endif
                    </section>
                    <div class="grid grid-cols-2 gap-4 lg:grid-cols-1">
                        <div class="flex flex-col justify-between rounded-[1.5rem] border border-black/[.05] bg-[#f7f7f7] p-6"><p class="text-[.65rem] leading-5 text-[#858585]">Kelas mendatang</p><p class="mt-3 text-4xl font-semibold leading-none tracking-[-.06em]">{{ str_pad((string) $upcomingBookings->count(), 2, '0', STR_PAD_LEFT) }}<span class="text-[#bdbdbd]">.</span></p></div>
                        <div class="flex flex-col justify-between rounded-[1.5rem] border border-black/[.05] bg-[#f7f7f7] p-6"><p class="text-[.65rem] leading-5 text-[#858585]">Total transaksi</p><p class="mt-3 text-4xl font-semibold leading-none tracking-[-.06em]">{{ str_pad((string) $transactions->count(), 2, '0', STR_PAD_LEFT) }}<span class="text-[#bdbdbd]">.</span></p></div>
                    </div>
                </div>

                <section id="kelas-saya" class="mt-10 scroll-mt-28" aria-labelledby="bookings-title">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-4"><h2 id="bookings-title" class="text-xl font-semibold tracking-[-.035em]">Kelas berikutnya.</h2><a href="{{ route('classes.index') }}" class="text-xs font-medium text-[#858585] transition hover:text-[#171717]">Jelajahi kelas <span class="ml-2" aria-hidden="true">↗</span></a></div>
                    @if ($upcomingBookings->isEmpty())
                        <div class="rounded-[1.5rem] border border-dashed border-black/15 px-7 py-9">
                            <p class="text-sm font-medium text-[#525252]">Jadwalmu masih kosong.</p>
                            <p class="mt-2 max-w-md text-xs leading-6 text-[#858585]">Belum ada booking kelas mendatang. Temukan sesi yang sesuai dengan jadwal dan tujuan latihanmu.</p>
                        </div>
                    @else
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
                            @foreach ($upcomingBookings as $booking)
                                <article class="rounded-[1.5rem] border border-black/[.07] p-6">
                                    <div class="flex items-start gap-4">
                                        <div class="flex min-w-14 shrink-0 flex-col items-center rounded-xl bg-[#f5f5f5] px-3 py-3"><span class="text-2xl font-semibold leading-none tracking-[-.04em]">{{ $booking->session->date->format('d') }}</span><span class="mt-1.5 text-[.6rem] uppercase text-[#858585]">{{ $booking->session->date->translatedFormat('M') }}</span></div>
                                        <div class="min-w-0"><h3 class="break-words text-base font-semibold tracking-[-.025em]">{{ $booking->session->gymClass->name }}</h3><p class="mt-2 text-xs font-medium tabular-nums text-[#737373]">{{ $booking->session->gymClass->time_range }}</p>@if ($booking->session->gymClass->instructor)<p class="mt-1 text-xs text-[#a3a3a3]">{{ $booking->session->gymClass->instructor }}</p>@endif</div>
                                    </div>
                                    <div class="mt-5 flex items-center justify-between gap-4 border-t border-black/[.06] pt-4"><p class="flex items-center gap-1.5 text-[.65rem] text-[#737373]"><span aria-hidden="true">✓</span>Terdaftar</p><form method="POST" action="{{ route('classes.bookings.cancel', $booking) }}">@csrf @method('DELETE')<button type="submit" class="text-[.65rem] text-[#a3a3a3] underline decoration-black/15 underline-offset-4 transition hover:text-[#171717]">Batalkan booking</button></form></div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section id="transaksi-saya" class="mt-10 scroll-mt-28" aria-labelledby="transactions-title">
                    <h2 id="transactions-title" class="text-xl font-semibold tracking-[-.035em]">Riwayat transaksi.</h2>
                    <p class="mb-5 mt-2 text-xs leading-6 text-[#858585]">Pantau status pembayaran dan buka detail invoice milikmu.</p>
                    @if ($transactions->isEmpty())
                        <div class="rounded-[1.5rem] border border-dashed border-black/15 px-7 py-9"><p class="text-sm font-medium text-[#525252]">Belum ada transaksi.</p><p class="mt-2 text-xs leading-6 text-[#858585]">Pesanan membership pertamamu akan muncul di sini.</p></div>
                    @else
                        <div class="divide-y divide-black/[.06] overflow-hidden rounded-[1.5rem] border border-black/[.07]">
                            @foreach ($transactions as $trx)
                                <article class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0"><h3 class="break-words text-sm font-semibold tracking-[-.02em]">{{ $trx->membership->plan->name }}</h3><p class="mt-1.5 break-all text-[.65rem] text-[#858585]">{{ $trx->invoice_number }}</p><p class="mt-1 text-[.65rem] text-[#a3a3a3]">{{ $trx->created_at->translatedFormat('d M Y, H:i') }}</p></div>
                                    <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-4 sm:justify-end">
                                        <div class="sm:text-right"><p class="text-base font-semibold tracking-[-.035em] tabular-nums">{{ $trx->formatted_amount }}</p><span class="mt-2 inline-block rounded-full border px-2.5 py-1 text-[.55rem] font-medium {{ $trx->status->value === 'paid' ? 'border-[#171717] bg-[#171717] text-white' : 'border-black/10 bg-[#f5f5f5] text-[#737373]' }}">{{ $trx->status->label() }}</span></div>
                                        <a href="{{ route('membership.invoice', $trx) }}" class="inline-flex items-center gap-2 rounded-full border border-black/10 px-4 py-2.5 text-[.65rem] font-semibold transition hover:bg-[#171717] hover:text-white">{{ $trx->status->value === 'pending' && ! $trx->payment_proof ? 'Unggah bukti' : 'Lihat invoice' }} <span aria-hidden="true">↗</span></a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</section>

<div class="relative -mb-px h-16 overflow-hidden bg-white sm:h-24" aria-hidden="true"><svg class="absolute inset-0 h-full w-full text-[#0a0a0a]" viewBox="0 0 1440 160" preserveAspectRatio="none" fill="currentColor"><path d="M0 94C260 28 470 34 740 104S1200 166 1440 62V160H0Z"/></svg></div>
@endsection
