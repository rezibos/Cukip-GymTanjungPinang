@props(['href', 'active' => false])

<a href="{{ $href }}"
   @if ($active) aria-current="true" @endif
   {{ $attributes->class([
       'rounded border px-3 py-2 text-sm',
       'border-green-800 bg-green-800 text-white' => $active,
       'border-gray-300 bg-white text-gray-700 hover:border-green-800' => ! $active,
   ]) }}>
    {{ $slot }}
</a>