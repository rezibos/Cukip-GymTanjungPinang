@extends('layouts.app')

@section('title', 'Invoice ' . $transaction->invoice_number . ' - CukipGym')

@section('content')
<div class="mx-auto max-w-lg">
    <h1 class="text-xl font-bold">Invoice</h1>

    <div class="mt-4 rounded-lg border border-gray-200 bg-white p-4 text-sm">
        <div class="flex items-center justify-between">
            <p class="font-medium">{{ $transaction->invoice_number }}</p>
            <span class="rounded px-2 py-1 text-xs font-medium
                {{ match($transaction->status->value) {
                    'paid' => 'bg-neutral-100 text-neutral-900',
                    'rejected' => 'bg-neutral-200 text-neutral-900',
                    default => 'bg-neutral-200 text-neutral-800',
                } }}">
                {{ $transaction->status->label() }}
            </span>
        </div>
        <p class="mt-2 text-gray-600">{{ $transaction->membership->plan->name }}</p>
        <p class="mt-1 text-lg font-bold text-neutral-900">{{ $transaction->formatted_amount }}</p>
        <p class="mt-1 text-gray-500">{{ $transaction->created_at->translatedFormat('d F Y, H:i') }}</p>

        @if ($transaction->admin_note)
            <p class="mt-2 rounded bg-gray-50 p-2 text-gray-600">Catatan admin: {{ $transaction->admin_note }}</p>
        @endif
    </div>

    @if ($transaction->status->value === 'pending')
        <div class="mt-4 space-y-3">
            @if ($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                   class="block rounded bg-neutral-900 px-4 py-2.5 text-center font-medium text-white hover:bg-black">
                    Konfirmasi via WhatsApp
                </a>
            @endif

            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <p class="text-sm font-medium">
                    {{ $transaction->payment_proof ? 'Ganti bukti transfer' : 'Upload bukti transfer' }}
                </p>
                @if ($transaction->payment_proof_url)
                    <p class="mt-1 text-sm text-gray-500">
                        <a href="{{ $transaction->payment_proof_url }}" target="_blank" class="text-neutral-900 underline">
                            Lihat bukti yang sudah diunggah
                        </a>
                    </p>
                @endif

                <form method="POST" action="{{ route('membership.invoice.proof', $transaction) }}"
                      enctype="multipart/form-data" class="mt-3 space-y-2">
                    @csrf
                    <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" required
                           class="block w-full text-sm">
                    @error('payment_proof') <p class="text-sm text-neutral-700">{{ $message }}</p> @enderror

                    <button type="submit" class="w-full rounded border border-neutral-900 px-4 py-2.5 font-medium text-neutral-900 hover:bg-neutral-50">
                        Unggah Bukti
                    </button>
                </form>
            </div>
        </div>
    @elseif ($transaction->status->value === 'paid')
        <div class="mt-4 rounded border border-neutral-300 bg-neutral-50 p-4 text-sm text-neutral-900">
            Pembayaran sudah dikonfirmasi. Membership Anda sudah aktif.
            <a href="{{ route('profile') }}" class="underline">Lihat profil</a>
        </div>
    @else
        <div class="mt-4 rounded border border-neutral-400 bg-neutral-100 p-4 text-sm text-neutral-900">
            Transaksi ditolak. Silakan hubungi admin untuk info lebih lanjut.
        </div>
    @endif
</div>
@endsection