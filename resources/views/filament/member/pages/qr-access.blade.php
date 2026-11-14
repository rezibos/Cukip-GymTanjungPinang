<x-filament-panels::page>
    <div class="member-page-intro">
        <p>Gunakan QR ini sebagai kartu masuk digital. Pada versi demo, QR bersifat simulasi dan belum terhubung ke mesin pintu fisik.</p>
    </div>

    <div class="member-qr-page-grid">
        @include('filament.member.partials.access-code', [
            'activeMembership' => $activeMembership,
            'accessCode' => $accessCode,
            'qrCells' => $qrCells,
        ])

        <section class="member-list-card">
            <p class="member-eyebrow">Informasi akses</p>
            <h2>{{ $activeMembership ? $activeMembership->plan->name : ($latestMembership?->plan?->name ?? 'Belum ada membership') }}</h2>
            <dl class="member-details-list">
                <div><dt>Status</dt><dd>{{ $activeMembership ? 'Aktif' : 'Kedaluwarsa' }}</dd></div>
                <div><dt>Berlaku mulai</dt><dd>{{ $activeMembership?->start_date?->translatedFormat('d M Y') ?? '—' }}</dd></div>
                <div><dt>Berlaku sampai</dt><dd>{{ ($activeMembership ?? $latestMembership)?->end_date?->translatedFormat('d M Y') ?? '—' }}</dd></div>
            </dl>
        </section>
    </div>
</x-filament-panels::page>
