@props(['markers' => [], 'height' => 'h-72 sm:h-96', 'full' => false])

<div data-map
     data-full="{{ $full ? 'true' : 'false' }}"
     data-markers="{{ json_encode($markers) }}"
     role="region" aria-label="Peta lokasi gym"
     class="relative z-0 w-full overflow-hidden rounded-lg border border-gray-200 bg-gray-100 {{ $height }}"></div>