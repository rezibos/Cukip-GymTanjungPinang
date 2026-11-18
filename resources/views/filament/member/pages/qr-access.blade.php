<x-filament-panels::page>
    <div class="member-qr-access-page">
        <header class="member-summary-title-card member-qr-access-title">
            <x-filament::breadcrumbs
                :breadcrumbs="[
                    route('filament.member.pages.dashboard') => 'CukipGym',
                    'QR akses',
                ]"
            />
            <h1>QR akses</h1>
        </header>

        <div class="member-qr-access-grid">
            <section class="member-qr-access-panel">
                <header>
                    <h2>QR akses</h2>
                    <p>Tunjukkan QR ini saat masuk.</p>
                </header>

                @if ($activeMembership)
                    <div class="member-qr-grid" role="img" aria-label="QR akses member aktif">
                        @foreach ($qrCells as $isDark)
                            <span class="{{ $isDark ? 'is-dark' : '' }}"></span>
                        @endforeach
                    </div>
                    <p class="member-access-code">{{ $accessCode }}</p>
                    <span class="member-status is-active">Aktif</span>
                @else
                    <div class="member-qr-locked" aria-hidden="true">
                        <x-filament::icon icon="heroicon-o-lock-closed" />
                    </div>
                    <span class="member-status is-expired">Tidak aktif</span>
                @endif
            </section>

            <section class="member-access-information-panel">
                <header>
                    <h2>Informasi akses</h2>
                    <p>{{ $activeMembership ? 'Membership aktif' : 'Membership tidak aktif' }}</p>
                </header>

                <div class="member-access-plan">
                    <strong>{{ $activeMembership?->plan?->name ?? $latestMembership?->plan?->name ?? 'Belum ada membership' }}</strong>
                    <span>
                        @if (($activeMembership ?? $latestMembership)?->end_date)
                            {{ ($activeMembership ? 'Berlaku sampai ' : 'Berakhir ') . ($activeMembership ?? $latestMembership)->end_date->locale('id')->translatedFormat('d F Y') }}
                        @else
                            Pilih paket membership untuk mengaktifkan akses.
                        @endif
                    </span>
                </div>

                <a href="{{ route('membership.index') }}" class="member-button member-button-secondary">
                    {{ $activeMembership ? 'Perpanjang membership' : 'Pilih membership' }}
                </a>
            </section>
        </div>
    </div>
</x-filament-panels::page>
