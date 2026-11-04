@props(['plan'])

<div class="flex flex-col rounded-lg border border-gray-200 bg-white p-5">
    <h3 class="break-words text-lg font-semibold">{{ $plan->name }}</h3>
    <p class="mt-1 text-sm text-gray-500">{{ $plan->duration_days }} hari</p>
    <p class="mt-3 text-2xl font-bold text-green-800">{{ $plan->formatted_price }}</p>
    @if ($plan->description)
        <p class="mt-2 text-sm text-gray-600">{{ $plan->description }}</p>
    @endif

    <a href="{{ auth()->check() ? route('membership.checkout', $plan) : route('login') }}"
       class="mt-auto pt-4 block rounded bg-green-800 px-4 py-2.5 text-center font-medium text-white hover:bg-green-900">
        Pilih Paket
    </a>
</div>