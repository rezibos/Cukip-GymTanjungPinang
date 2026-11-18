<x-filament-panels::page>
    @php
        $bookingGroups = $bookings
            ->groupBy(fn ($booking) => $booking->slot->starts_at->toDateString())
            ->values();
        $groupDates = $bookingGroups
            ->map(fn ($group) => $group->first()->slot->starts_at->toDateString())
            ->values();
    @endphp

    <div
        class="member-trainer-page member-classes-page"
        x-data="{
            filterMode: 'all',
            customDate: '',
            groupDates: @js($groupDates),
            isGroupVisible(index, date) {
                return this.filterMode === 'all'
                    || this.filterMode === `day:${index}`
                    || (this.filterMode === 'custom' && this.customDate === date)
            },
        }"
    >
        <header class="member-summary-title-card">
            <x-filament::breadcrumbs
                :breadcrumbs="[
                    route('filament.member.pages.dashboard') => 'CukipGym',
                    'Personal Trainer',
                ]"
            />
            <h1>Personal Trainer</h1>
        </header>

        <section class="member-trainer-panel member-classes-panel">
            <header class="member-classes-toolbar">
                <div class="member-classes-summary">
                    <div class="member-classes-summary-copy">
                        <span class="member-classes-summary-label">Jadwal privat</span>
                        <div class="member-classes-summary-heading">
                            <p>Sesi personal trainer yang sudah kamu beli.</p>
                        </div>
                        <span class="member-classes-summary-helper">Hanya sesi dengan pembayaran terkonfirmasi yang ditampilkan.</span>
                    </div>
                </div>

                <div class="member-classes-toolbar-actions">
                    @if ($bookingGroups->isNotEmpty())
                        <div class="member-class-filter-group">
                            <label class="member-class-filter">
                                <span class="fi-sr-only">Tampilkan sesi berdasarkan hari</span>
                                <select class="member-class-filter-select member-trainer-filter-select" x-model="filterMode">
                                    <option value="all">Semua jadwal</option>
                                    <option value="custom">Pilih tanggal</option>
                                    @foreach ($bookingGroups as $groupIndex => $bookingGroup)
                                        <option value="day:{{ $groupIndex }}">
                                            {{ $bookingGroup->first()->slot->starts_at->locale('id')->translatedFormat('l, d M') }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-filament::icon icon="heroicon-o-chevron-down" />
                            </label>

                            <label class="member-class-custom-date" x-show="filterMode === 'custom'" x-cloak>
                                <span class="fi-sr-only">Pilih tanggal sesi</span>
                                <input type="date" x-model="customDate" />
                            </label>
                        </div>
                    @endif

                    <a href="{{ route('classes.index') }}#personal-trainer" class="member-add-class-button">
                        <x-filament::icon icon="heroicon-o-plus" />
                        Tambah sesi
                    </a>
                </div>
            </header>

            @if ($bookingGroups->isEmpty())
                <div class="member-classes-empty">
                    <span aria-hidden="true"><x-filament::icon icon="heroicon-o-user-plus" /></span>
                    <div>
                        <strong>Belum ada sesi personal trainer mendatang.</strong>
                        <p>Pilih pelatih dan waktu yang sesuai untuk menambah jadwal privatmu.</p>
                    </div>
                </div>
            @else
                @foreach ($bookingGroups as $groupIndex => $bookingGroup)
                    <div
                        class="member-booked-class-track member-trainer-session-track"
                        x-show="isGroupVisible({{ $groupIndex }}, '{{ $bookingGroup->first()->slot->starts_at->toDateString() }}')"
                        x-cloak
                    >
                        @foreach ($bookingGroup as $booking)
                            <article class="member-booked-class-card member-trainer-session-card">
                                <div class="member-booked-class-date">
                                    <span>{{ $booking->slot->starts_at->locale('id')->translatedFormat('D') }}</span>
                                    <strong>{{ $booking->slot->starts_at->format('d') }}</strong>
                                    <small>{{ $booking->slot->starts_at->locale('id')->translatedFormat('M Y') }}</small>
                                </div>

                                <div class="member-booked-class-content">
                                    <div class="member-booked-class-heading">
                                        <p>Sesi personal trainer</p>
                                        <span class="member-booked-class-status">Terkonfirmasi</span>
                                    </div>

                                    <strong class="member-booked-class-time">
                                        {{ $booking->slot->starts_at->format('H:i') }} – {{ $booking->slot->ends_at->format('H:i') }}
                                    </strong>
                                    <h2>{{ $booking->slot->trainer->name }}</h2>

                                    <div class="member-booked-class-instructor">
                                        <span aria-hidden="true"><x-filament::icon icon="heroicon-o-bolt" /></span>
                                        {{ $booking->slot->trainer->specialization ?: 'Program latihan personal' }}
                                    </div>
                                </div>

                                <div class="member-trainer-session-details">
                                    <span>
                                        <x-filament::icon icon="heroicon-o-clock" />
                                        {{ $booking->slot->starts_at->locale('id')->diffForHumans($booking->slot->ends_at, true) }}
                                    </span>
                                    <span>
                                        <x-filament::icon icon="heroicon-o-banknotes" />
                                        Rp{{ number_format($booking->amount, 0, ',', '.') }}
                                    </span>
                                    <span>
                                        <x-filament::icon icon="heroicon-o-check-circle" />
                                        Pembayaran lunas
                                    </span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endforeach

                <div
                    class="member-classes-filter-empty"
                    x-show="filterMode === 'custom' && (customDate === '' || ! groupDates.includes(customDate))"
                    x-cloak
                >
                    <span aria-hidden="true"><x-filament::icon icon="heroicon-o-calendar-days" /></span>
                    <div>
                        <strong x-text="customDate === '' ? 'Pilih tanggal untuk melihat sesi.' : 'Tidak ada sesi pada tanggal ini.'"></strong>
                        <p>Gunakan pilihan Semua jadwal untuk melihat seluruh sesi personal trainermu.</p>
                    </div>
                </div>
            @endif
        </section>
    </div>
</x-filament-panels::page>
