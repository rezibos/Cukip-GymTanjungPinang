@props(['gym'])

<a href="{{ route('gyms.show', $gym) }}"
   class="flex h-full flex-col overflow-hidden rounded-lg border border-gray-200 bg-white hover:border-green-800">

    @if ($gym->image_url)
        <img src="{{ $gym->image_url }}" alt="Foto {{ $gym->name }}" loading="lazy"
             class="aspect-[4/3] w-full object-cover">
    @else
        <div class="flex aspect-[4/3] w-full items-center justify-center bg-green-50 text-3xl font-semibold text-green-800"
             role="img" aria-label="Foto {{ $gym->name }} belum tersedia">
            {{ mb_strtoupper(mb_substr($gym->name, 0, 2)) }}
        </div>
    @endif

    <div class="flex flex-1 flex-col p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-green-800">{{ $gym->category->name }}</p>
        <h3 class="mt-1 break-words text-base font-semibold text-gray-900">{{ $gym->name }}</h3>

        <p class="mt-1 text-sm text-gray-600">
            {{ $gym->district ? $gym->district . ', ' : '' }}{{ $gym->region->value }}
            @isset($gym->distance)
                <p class="text-sm font-medium text-green-800">{{ number_format($gym->distance, 1) }} km dari lokasi Anda</p>
            @endisset
        </p>

        <div class="mt-2">
            <x-rating-stars :rating="$gym->approved_reviews_avg_rating" :count="$gym->approved_reviews_count" />
        </div>

        <p class="mt-auto pt-3 text-sm text-gray-600">
            @if ($gym->packages_min_price !== null)
                Mulai dari <span class="font-semibold text-gray-900">Rp{{ number_format($gym->packages_min_price, 0, ',', '.') }}</span>
            @else
                Harga belum tersedia
            @endif
        </p>
    </div>
</a>