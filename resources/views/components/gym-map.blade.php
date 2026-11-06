@props(['markers' => [], 'height' => 'h-72 sm:h-96', 'full' => false, 'selectable' => false])

<div data-map
     data-full="{{ $full ? 'true' : 'false' }}"
     data-selectable="{{ $selectable ? 'true' : 'false' }}"
     data-markers="{{ json_encode($markers) }}"
     role="region" aria-label="Peta lokasi gym"
     class="relative z-0 w-full overflow-hidden bg-gray-100 {{ $selectable ? 'location-map' : '' }} {{ $height }}"></div>
