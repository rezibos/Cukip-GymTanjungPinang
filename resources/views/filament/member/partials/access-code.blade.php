<div class="member-qr-card {{ $activeMembership ? 'is-active' : 'is-locked' }}">
    <div class="member-card-heading">
        <div>
            <p class="member-eyebrow">QR akses pintu</p>
            <h3>{{ $activeMembership ? 'QR akses aktif' : 'QR akses dikunci' }}</h3>
        </div>
        <span class="member-status {{ $activeMembership ? 'is-active' : 'is-expired' }}">
            {{ $activeMembership ? 'Aktif' : 'Tidak aktif' }}
        </span>
    </div>

    @if ($activeMembership)
        <div class="member-qr-grid" role="img" aria-label="QR akses member aktif">
            @foreach ($qrCells as $isDark)
                <span class="{{ $isDark ? 'is-dark' : '' }}"></span>
            @endforeach
        </div>
        <p class="member-access-code">{{ $accessCode }}</p>
        <p class="member-card-note">Tunjukkan QR ini saat masuk. QR demo ini hanya aktif selama membership berlaku.</p>
    @else
        <div class="member-qr-locked" aria-hidden="true">
            <x-filament::icon icon="heroicon-o-lock-closed" />
        </div>
        <p class="member-card-note">Perpanjang membership untuk mengaktifkan kembali QR akses pintu gym.</p>
        <a href="{{ route('membership.index') }}" class="member-button member-button-primary">Perpanjang membership <span aria-hidden="true">→</span></a>
    @endif
</div>
