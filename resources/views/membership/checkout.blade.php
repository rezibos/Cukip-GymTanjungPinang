@extends('layouts.app')

@section('title', 'Checkout - CukipGym')

@section('content')
<div class="mx-auto max-w-lg">
    <h1 class="text-xl font-bold">Checkout</h1>

    <div class="mt-4 rounded-lg border border-gray-200 bg-white p-4 text-sm">
        <p class="font-medium">{{ $plan->name }}</p>
        <p class="text-gray-600">{{ $plan->duration_days }} hari</p>
        <p class="mt-2 text-lg font-bold text-neutral-900">{{ $plan->formatted_price }}</p>
    </div>

    <form method="POST" action="{{ route('membership.checkout', $plan) }}" class="mt-4 space-y-4">
        @csrf

        <div>
            <label for="phone" class="block text-sm font-medium">Nomor WhatsApp</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" required
                   placeholder="6281234567890"
                   class="mt-1 w-full rounded border border-gray-300 px-3 py-2.5">
            <p class="mt-1 text-xs text-gray-500">Dipakai untuk konfirmasi pembayaran via WhatsApp.</p>
            @error('phone') <p class="mt-1 text-sm text-neutral-700">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full rounded bg-neutral-900 px-5 py-2.5 font-medium text-white hover:bg-black">
            Buat Pesanan
        </button>
    </form>
</div>
@endsection