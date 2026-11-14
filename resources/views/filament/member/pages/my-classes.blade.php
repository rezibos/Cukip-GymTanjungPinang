<x-filament-panels::page>
    <div class="member-page-intro">
        <p>Daftar kelas mendatang yang sudah kamu booking. Menu ini hanya tersedia saat membership aktif.</p>
        <a href="{{ route('classes.index') }}" class="member-button member-button-primary">Cari kelas baru <span aria-hidden="true">↗</span></a>
    </div>

    @if ($bookings->isEmpty())
        <div class="member-empty-panel"><p>Belum ada kelas yang dibooking.</p><span>Pilih jadwal latihan yang sesuai dengan rutinitasmu.</span></div>
    @else
        <div class="member-booking-grid">
            @foreach ($bookings as $booking)
                <article class="member-booking-card">
                    <div class="member-date-block"><strong>{{ $booking->session->date->format('d') }}</strong><span>{{ $booking->session->date->translatedFormat('M') }}</span></div>
                    <div><h2>{{ $booking->session->gymClass->name }}</h2><p>{{ $booking->session->gymClass->time_range }}</p>@if ($booking->session->gymClass->instructor)<span>{{ $booking->session->gymClass->instructor }}</span>@endif</div>
                    <form method="POST" action="{{ route('classes.bookings.cancel', $booking) }}">@csrf @method('DELETE')<button type="submit">Batalkan</button></form>
                </article>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
