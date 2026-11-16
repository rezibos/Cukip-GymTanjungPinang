<x-filament-panels::page>
    <div class="member-personal-dashboard">
        <header class="member-summary-title-card">
            <p>CukipGym / Dashboard member</p>
            <h1>Ringkasan personal.</h1>
        </header>

        <section class="member-summary-stats" aria-label="Statistik ringkasan member">
            <article>
                <div><strong>{{ $remainingDays }}<small> hari</small></strong><span>Sisa masa membership</span></div>
                <span class="member-summary-stat-icon" aria-hidden="true"><x-filament::icon icon="heroicon-o-clock" /></span>
            </article>
            <article>
                <div><strong>{{ $upcomingTrainerBookingsCount }}</strong><span>Sesi personal trainer</span></div>
                <span class="member-summary-stat-icon" aria-hidden="true"><x-filament::icon icon="heroicon-o-user" /></span>
            </article>
            <article>
                <div><strong>{{ $upcomingBookingsCount }}</strong><span>Booking kelas mendatang</span></div>
                <span class="member-summary-stat-icon" aria-hidden="true"><x-filament::icon icon="heroicon-o-calendar-days" /></span>
            </article>
            <article>
                <div><strong>{{ $monthlyBookingsCount }}</strong><span>Booking kelas bulan ini</span></div>
                <span class="member-summary-stat-icon" aria-hidden="true"><x-filament::icon icon="heroicon-o-check-circle" /></span>
            </article>
        </section>

        <div class="member-summary-overview">
            <section class="member-summary-welcome">
                <p>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                <h2>Halo, {{ $user->name }}.</h2>
                <span>Semua jadwal latihan dan akses membership-mu ada di satu tempat.</span>
            </section>

            <section class="member-summary-membership {{ $activeMembership ? 'is-active' : 'is-expired' }}">
                <div class="member-membership-heading">
                    <p>Status membership</p>
                    <span class="member-status {{ $activeMembership ? 'is-active' : 'is-expired' }}">{{ $activeMembership ? 'Aktif' : 'Tidak aktif' }}</span>
                </div>
                <div class="member-membership-plan">
                    <span>Paket saat ini</span>
                    <strong>{{ $activeMembership?->plan?->name ?? $latestMembership?->plan?->name ?? 'Belum ada paket' }}</strong>
                    <small>
                        @if ($activeMembership)
                            Berlaku sampai {{ $activeMembership->end_date->locale('id')->translatedFormat('d F Y') }}
                        @elseif ($latestMembership?->end_date)
                            Berakhir {{ $latestMembership->end_date->locale('id')->translatedFormat('d F Y') }}
                        @else
                            Pilih paket untuk mulai berlatih.
                        @endif
                    </small>
                </div>
                <a href="{{ route('membership.index') }}">{{ $activeMembership ? 'Perpanjang membership' : 'Pilih membership' }} <span aria-hidden="true">→</span></a>
            </section>

            @include('filament.member.partials.access-code', [
                'activeMembership' => $activeMembership,
                'accessCode' => $accessCode,
                'qrCells' => $qrCells,
                'compact' => true,
            ])
        </div>

        <div class="member-summary-schedules">
            <section class="member-schedule-panel">
                <header>
                    <div><h2>Sesi personal trainer saya</h2><p>Jadwal privat yang sudah dikonfirmasi.</p></div>
                </header>

                @if ($upcomingTrainerBookings->isEmpty())
                    <div class="member-schedule-empty">
                        <span class="member-schedule-empty-icon" aria-hidden="true"><x-filament::icon icon="heroicon-o-user-plus" /></span>
                        <div><strong>Belum ada sesi personal trainer.</strong><span>Pilih pelatih dan waktu yang sesuai dengan rutinitasmu.</span></div>
                        <a class="member-empty-action" href="{{ route('classes.index') }}#personal-trainer">Atur sesimu <span aria-hidden="true">↗</span></a>
                    </div>
                @else
                    <div class="member-schedule-carousel" x-data="{ canScrollBack: false, canScrollForward: false, syncArrows() { const track = this.$refs.track; this.canScrollBack = track.scrollLeft > 2; this.canScrollForward = track.scrollLeft + track.clientWidth < track.scrollWidth - 2 } }" x-init="$nextTick(() => syncArrows())" x-on:resize.window="syncArrows()">
                        <button type="button" class="member-schedule-arrow is-previous" aria-label="Geser jadwal ke kiri" x-bind:disabled="! canScrollBack" x-on:click="$refs.track.scrollBy({ left: -Math.max(280, $refs.track.clientWidth * 0.8), behavior: 'smooth' })"><x-filament::icon icon="heroicon-o-chevron-left" /></button>
                        <div class="member-schedule-track" x-ref="track" x-on:scroll.debounce.100ms="syncArrows()" tabindex="0">
                            @foreach ($upcomingTrainerBookings as $booking)
                                <article class="member-session-card">
                                    <p>{{ $booking->slot->starts_at->locale('id')->translatedFormat('l, d M') }}</p>
                                    <strong>{{ $booking->slot->starts_at->format('H:i') }} – {{ $booking->slot->ends_at->format('H:i') }}</strong>
                                    <span>Sesi personal trainer</span>
                                    <div class="member-session-person"><span aria-hidden="true"><x-filament::icon icon="heroicon-o-user" /></span>{{ $booking->slot->trainer->name }}</div>
                                </article>
                            @endforeach
                        </div>
                        <button type="button" class="member-schedule-arrow is-next" aria-label="Geser jadwal ke kanan" x-bind:disabled="! canScrollForward" x-on:click="$refs.track.scrollBy({ left: Math.max(280, $refs.track.clientWidth * 0.8), behavior: 'smooth' })"><x-filament::icon icon="heroicon-o-chevron-right" /></button>
                    </div>
                    <a class="member-schedule-action" href="{{ route('classes.index') }}#personal-trainer">Atur sesimu <span aria-hidden="true">↗</span></a>
                @endif
            </section>

            <section class="member-schedule-panel">
                <header>
                    <div><h2>Kelas saya</h2><p>Kelas grup yang sudah kamu booking.</p></div>
                </header>

                @if (! $activeMembership)
                    <div class="member-schedule-empty">
                        <span class="member-schedule-empty-icon" aria-hidden="true"><x-filament::icon icon="heroicon-o-lock-closed" /></span>
                        <div><strong>Booking kelas sedang dikunci.</strong><span>Aktifkan membership untuk melihat dan memesan kelas.</span></div>
                        <a class="member-empty-action" href="{{ route('membership.index') }}">Aktifkan <span aria-hidden="true">↗</span></a>
                    </div>
                @elseif ($upcomingBookings->isEmpty())
                    <div class="member-schedule-empty">
                        <span class="member-schedule-empty-icon" aria-hidden="true"><x-filament::icon icon="heroicon-o-calendar-days" /></span>
                        <div><strong>Belum ada kelas mendatang.</strong><span>Pilih kelas untuk mengisi jadwal latihanmu.</span></div>
                        <a class="member-empty-action" href="{{ route('classes.index') }}#class-results">Atur kelasmu <span aria-hidden="true">↗</span></a>
                    </div>
                @else
                    <div class="member-schedule-carousel" x-data="{ canScrollBack: false, canScrollForward: false, syncArrows() { const track = this.$refs.track; this.canScrollBack = track.scrollLeft > 2; this.canScrollForward = track.scrollLeft + track.clientWidth < track.scrollWidth - 2 } }" x-init="$nextTick(() => syncArrows())" x-on:resize.window="syncArrows()">
                        <button type="button" class="member-schedule-arrow is-previous" aria-label="Geser kelas ke kiri" x-bind:disabled="! canScrollBack" x-on:click="$refs.track.scrollBy({ left: -Math.max(280, $refs.track.clientWidth * 0.8), behavior: 'smooth' })"><x-filament::icon icon="heroicon-o-chevron-left" /></button>
                        <div class="member-schedule-track" x-ref="track" x-on:scroll.debounce.100ms="syncArrows()" tabindex="0">
                            @foreach ($upcomingBookings as $booking)
                                <article class="member-session-card member-class-session-card">
                                    <form
                                        method="POST"
                                        action="{{ route('classes.bookings.cancel', $booking) }}"
                                        data-member-class-cancel-form
                                        data-class-name="{{ $booking->session->gymClass->name }}"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="member-class-cancel-button"
                                            aria-label="Batalkan booking {{ $booking->session->gymClass->name }}"
                                        ><span aria-hidden="true">&times;</span></button>
                                    </form>
                                    <p>{{ $booking->session->date->locale('id')->translatedFormat('l, d M') }}</p>
                                    <strong>{{ $booking->session->gymClass->time_range }}</strong>
                                    <span>{{ $booking->session->gymClass->name }}</span>
                                    <div class="member-session-person"><span aria-hidden="true"><x-filament::icon icon="heroicon-o-user" /></span>{{ $booking->session->gymClass->instructor ?: 'Instruktur CukipGym' }}</div>
                                    <small>{{ $booking->session->capacity }} kapasitas</small>
                                </article>
                            @endforeach
                        </div>
                        <button type="button" class="member-schedule-arrow is-next" aria-label="Geser kelas ke kanan" x-bind:disabled="! canScrollForward" x-on:click="$refs.track.scrollBy({ left: Math.max(280, $refs.track.clientWidth * 0.8), behavior: 'smooth' })"><x-filament::icon icon="heroicon-o-chevron-right" /></button>
                    </div>
                    <a class="member-schedule-action" href="{{ route('classes.index') }}#class-results">Atur kelasmu <span aria-hidden="true">↗</span></a>
                @endif
            </section>
        </div>
    </div>
</x-filament-panels::page>
