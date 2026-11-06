@props(['rating' => null, 'count' => null])

@php $filled = (int) round($rating ?? 0); @endphp

<div class="flex items-center gap-1 text-sm">
    @if ($rating !== null)
        <span class="text-neutral-200" aria-hidden="true">
            @for ($i = 1; $i <= 5; $i++)
                <span class="{{ $i <= $filled ? '' : 'text-gray-300' }}">★</span>
            @endfor
        </span>
        <span class="font-medium">{{ number_format($rating, 1) }}</span>
        @if ($count !== null)
            <span class="text-gray-500">({{ $count }})</span>
        @endif
        <span class="sr-only">Rating {{ number_format($rating, 1) }} dari 5</span>
    @else
        <span class="text-gray-500">Belum ada rating</span>
    @endif
</div>
