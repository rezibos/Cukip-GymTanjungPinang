@extends('layouts.app')

@section('title', 'Invoice ' . $transaction->invoice_number . ' - CukipGym')
@section('main-class', 'w-full flex-1 bg-[#f5f5f5]')

@section('content')
<section class="px-5 pb-20 pt-32 text-[#171717] sm:px-8 lg:px-12 lg:pb-28 lg:pt-40" aria-labelledby="invoice-title" data-navbar-tone="light">
    <div class="mx-auto max-w-[68rem]">
        <div class="flex flex-wrap items-end justify-between gap-6 border-b border-black/10 pb-8">
            <div>
                <p class="section-kicker">Invoice membership</p>
                <h1 id="invoice-title" class="mt-5 text-[clamp(2.7rem,5vw,4.6rem)] font-semibold leading-none tracking-[-.06em]">Pesananmu.<br><span class="text-[#858585]">Menunggu konfirmasi.</span></h1>
            </div>
            <div class="text-right">
                <p class="text-[.65rem] uppercase tracking-[.16em] text-[#858585]">Nomor invoice</p>
                <p class="mt-2 text-sm font-semibold">{{ $transaction->invoice_number }}</p>
            </div>
        </div>

        <div class="mt-9 grid gap-8 lg:grid-cols-[1fr_23rem] lg:items-start">
            <div class="flex flex-col gap-3">
                @foreach ($transactions as $invoiceItem)
                    <article class="rounded-[1.4rem] border border-black/10 bg-white p-6">
                        <div class="flex items-start justify-between gap-6">
                            <div>
                                <h2 class="text-lg font-semibold tracking-[-.03em]">{{ $invoiceItem->membership->plan->name }}</h2>
                                <p class="mt-2 text-xs text-[#737373]">{{ $invoiceItem->membership->plan->duration_days }} hari × {{ $invoiceItem->quantity }} periode</p>
                            </div>
                            <p class="shrink-0 font-semibold">{{ $invoiceItem->formatted_amount }}</p>
                        </div>
                        @if ($invoiceItem->admin_note)
                            <p class="mt-5 rounded-xl bg-[#f5f5f2] px-4 py-3 text-xs leading-6 text-[#737373]">Catatan admin: {{ $invoiceItem->admin_note }}</p>
                        @endif
                    </article>
                @endforeach
            </div>

            <aside class="rounded-[1.7rem] bg-[#171717] p-7 text-white lg:sticky lg:top-28">
                <div class="flex items-center justify-between gap-4">
                    <p class="text-[.65rem] font-medium uppercase tracking-[.18em] text-white/45">Ringkasan pembayaran</p>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-[.6rem] font-medium text-white/75">{{ $transaction->status->label() }}</span>
                </div>
                <div class="mt-6 flex items-end justify-between gap-5 border-b border-white/15 pb-5">
                    <span class="text-sm text-white/60">Total bayar</span>
                    <span class="text-xl font-semibold tracking-[-.04em]">Rp{{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <p class="mt-5 text-[.68rem] leading-5 text-white/40">Dibuat {{ $transaction->created_at->translatedFormat('d F Y, H:i') }}</p>

                @if ($transaction->status->value === 'pending')
                    @if ($transaction->payment)
                        <div class="mt-6 rounded-xl bg-white/10 px-4 py-4">
                            <p class="text-sm font-medium">Bayar aman melalui Midtrans</p>
                            <p class="mt-2 text-[.65rem] leading-5 text-white/50">Pilih QRIS, transfer bank, e-wallet, atau metode lain yang tersedia di halaman Midtrans.</p>
                        </div>
                        <form method="POST" action="{{ route('payments.start', $transaction->payment) }}" class="mt-4">
                            @csrf
                            <button type="submit" class="flex min-h-11 w-full cursor-pointer items-center justify-between rounded-full bg-white px-5 text-xs font-semibold text-[#171717] transition hover:bg-[#d4d4d4]">
                                Bayar dengan Midtrans <span aria-hidden="true">↗</span>
                            </button>
                        </form>
                    @else
                        @if ($waUrl)
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="mt-6 flex min-h-11 items-center justify-between rounded-full bg-white px-5 text-xs font-semibold text-[#171717] transition hover:bg-[#d4d4d4]">
                                Konfirmasi WhatsApp <span aria-hidden="true">↗</span>
                            </a>
                        @endif

                        <div class="mt-6 border-t border-white/15 pt-6">
                            <p class="text-sm font-medium">{{ $transaction->payment_proof ? 'Ganti bukti transfer' : 'Unggah satu bukti transfer' }}</p>
                            <p class="mt-2 text-[.65rem] leading-5 text-white/40">Bukti ini berlaku untuk seluruh paket dalam invoice.</p>

                            @if ($transaction->payment_proof_url)
                                <a href="{{ $transaction->payment_proof_url }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex text-xs text-white/75 underline decoration-white/25 underline-offset-4 hover:text-white">Lihat bukti yang diunggah</a>
                            @endif

                            <form method="POST" action="{{ route('membership.invoice.proof', $transaction) }}" enctype="multipart/form-data" class="mt-4">
                                @csrf
                                <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" required class="block w-full text-xs text-white/60 file:mr-3 file:cursor-pointer file:rounded-full file:border-0 file:bg-white/10 file:px-4 file:py-2 file:text-xs file:text-white">
                                @error('payment_proof')<p class="mt-2 text-xs text-white/80">{{ $message }}</p>@enderror
                                <button type="submit" class="mt-4 min-h-11 w-full cursor-pointer rounded-full border border-white/20 px-5 text-xs font-semibold transition hover:bg-white hover:text-[#171717]">Unggah bukti</button>
                            </form>
                        </div>
                    @endif
                @elseif ($transaction->status->value === 'paid')
                    <p class="mt-6 rounded-xl bg-white/10 px-4 py-3 text-xs leading-6 text-white/75">Pembayaran sudah dikonfirmasi dan seluruh membership pada invoice ini telah aktif.</p>
                    <a href="{{ route('profile') }}" class="mt-4 inline-flex text-xs font-semibold underline underline-offset-4">Lihat profil</a>
                @else
                    <p class="mt-6 rounded-xl bg-white/10 px-4 py-3 text-xs leading-6 text-white/75">Pesanan ditolak. Hubungi admin untuk informasi lebih lanjut.</p>
                @endif
            </aside>
        </div>
    </div>
</section>
@endsection
