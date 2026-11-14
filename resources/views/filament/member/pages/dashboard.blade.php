<x-filament-panels::page>
    <div class="member-dashboard-header">
        <div>
            <p class="member-eyebrow">CukipGym / Panel member</p>
            <h1>Halo, {{ $user->name }}.</h1>
            <p>QR masuk, membership, kelas, dan transaksi dalam satu tempat.</p>
        </div>
        <a href="{{ route('home') }}" class="member-button member-button-secondary">Lihat website <span aria-hidden="true">↗</span></a>
    </div>

    <div class="member-dashboard-primary">
        <section class="member-membership-card {{ $activeMembership ? 'is-active' : 'is-expired' }}">
            <div class="member-card-heading">
                <div>
                    <p class="member-eyebrow">Status membership</p>
                    <h2>{{ $activeMembership ? 'Membership aktif' : 'Membership kedaluwarsa' }}</h2>
                </div>
                <span class="member-status {{ $activeMembership ? 'is-active' : 'is-expired' }}">
                    {{ $activeMembership ? 'Aktif' : 'Kedaluwarsa' }}
                </span>
            </div>

            @if ($activeMembership)
                <div class="member-plan-name">{{ $activeMembership->plan->name }}</div>
                <p class="member-plan-meta">Berlaku sampai {{ $activeMembership->end_date->translatedFormat('d M Y') }}</p>
                <div class="member-membership-actions">
                    <a href="{{ route('membership.index') }}" class="member-button member-button-light">Perpanjang membership <span aria-hidden="true">→</span></a>
                    <a href="{{ \App\Filament\Member\Pages\MyClasses::getUrl() }}" class="member-text-link">Lihat kelas saya <span aria-hidden="true">↗</span></a>
                </div>
            @else
                <div class="member-plan-name">{{ $latestMembership?->plan?->name ?? 'Belum ada membership' }}</div>
                <p class="member-plan-meta">{{ $latestMembership?->end_date ? 'Berakhir '.$latestMembership->end_date->translatedFormat('d M Y') : 'Pilih paket untuk mulai berlatih.' }}</p>
                <p class="member-expired-copy">Akses QR dan booking kelas dikunci sampai membership diperpanjang.</p>
                <a href="{{ route('membership.index') }}" class="member-button member-button-light">Perpanjang membership <span aria-hidden="true">→</span></a>
            @endif
        </section>

        @include('filament.member.partials.access-code', [
            'activeMembership' => $activeMembership,
            'accessCode' => $accessCode,
            'qrCells' => $qrCells,
        ])
    </div>

    <div class="member-stats-grid">
        <article><p>Kelas mendatang</p><strong>{{ str_pad((string) $upcomingBookingsCount, 2, '0', STR_PAD_LEFT) }}</strong></article>
        <article><p>Total transaksi</p><strong>{{ str_pad((string) $transactionsCount, 2, '0', STR_PAD_LEFT) }}</strong></article>
        <article><p>Sisa masa aktif</p><strong>{{ $activeMembership ? $remainingDays : 0 }}<small> hari</small></strong></article>
    </div>

    <div class="member-dashboard-lists">
        <section class="member-list-card">
            <div class="member-card-heading">
                <div><p class="member-eyebrow">Jadwal</p><h2>Kelas berikutnya</h2></div>
                @if ($activeMembership)<a href="{{ \App\Filament\Member\Pages\MyClasses::getUrl() }}" class="member-text-link">Lihat semua ↗</a>@endif
            </div>
            @if (! $activeMembership)
                <div class="member-empty-state"><p>Booking kelas sedang dikunci.</p><span>Perpanjang membership untuk kembali memesan dan melihat kelas.</span></div>
            @elseif ($upcomingBookings->isEmpty())
                <div class="member-empty-state"><p>Belum ada kelas mendatang.</p><span>Jelajahi jadwal dan pilih sesi latihanmu.</span><a href="{{ route('classes.index') }}">Cari kelas →</a></div>
            @else
                <div class="member-compact-list">
                    @foreach ($upcomingBookings as $booking)
                        <article><div><strong>{{ $booking->session->gymClass->name }}</strong><span>{{ $booking->session->date->translatedFormat('d M Y') }} · {{ $booking->session->gymClass->time_range }}</span></div><span>Terdaftar</span></article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="member-list-card">
            <div class="member-card-heading">
                <div><p class="member-eyebrow">Pembayaran</p><h2>Transaksi terbaru</h2></div>
                <a href="{{ \App\Filament\Member\Pages\Transactions::getUrl() }}" class="member-text-link">Lihat semua ↗</a>
            </div>
            @if ($recentTransactions->isEmpty())
                <div class="member-empty-state"><p>Belum ada transaksi.</p><span>Riwayat pembayaran membership akan muncul di sini.</span></div>
            @else
                <div class="member-compact-list">
                    @foreach ($recentTransactions as $transaction)
                        <article><div><strong>{{ $transaction->membership->plan->name }}</strong><span>{{ $transaction->created_at->translatedFormat('d M Y, H:i') }}</span></div><span>{{ $transaction->formatted_amount }}</span></article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-filament-panels::page>
