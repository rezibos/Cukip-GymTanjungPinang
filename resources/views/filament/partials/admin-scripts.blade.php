@php
    $flashType = session()->has('error') ? 'error' : 'success';
    $flashMessage = session($flashType);
@endphp

@if ($flashMessage)
    <div
        hidden
        data-filament-flash
        data-notification-title="{{ $flashType === 'error' ? 'Terjadi kesalahan' : 'Berhasil' }}"
        data-notification-type="{{ $flashType }}"
    >{{ $flashMessage }}</div>
@endif

@vite('resources/js/filament/admin.js')
