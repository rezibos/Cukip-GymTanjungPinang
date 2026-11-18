<x-filament-panels::page>
    @php
        $bookingGroups = $bookings
            ->groupBy(fn ($booking) => $booking->session->date->toDateString())
            ->values();
        $groupDates = $bookingGroups
            ->map(fn ($group) => $group->first()->session->date->toDateString())
            ->values();
    @endphp

    <div
        class="member-classes-page"
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
        <header class="member-summary-title-card member-classes-title">
            <x-filament::breadcrumbs
                :breadcrumbs="[
                    route('filament.member.pages.dashboard') => 'CukipGym',
                    'Kelas',
                ]"
            />
            <h1>Kelas</h1>
        </header>

        <section class="member-classes-panel">
            <header class="member-classes-toolbar">
                <div class="member-classes-summary">
                    <div class="member-classes-summary-copy">
                        <span class="member-classes-summary-label">Jadwal mendatang</span>
                        <div class="member-classes-summary-heading">
                            <p>Daftar kelas mendatang yang sudah kamu booking.</p>
                        </div>
                        <span class="member-classes-summary-helper">Menu ini hanya tersedia saat membership aktif.</span>
                    </div>
                </div>

                <div class="member-classes-toolbar-actions">
                    @if ($bookingGroups->isNotEmpty())
                        <div class="member-class-filter-group">
                            <label class="member-class-filter">
                                <span class="fi-sr-only">Tampilkan jadwal berdasarkan hari</span>
                                <select class="member-class-filter-select" x-model="filterMode">
                                    <option value="all">Semua jadwal</option>
                                    <option value="custom">Pilih tanggal</option>
                                    @foreach ($bookingGroups as $groupIndex => $bookingGroup)
                                        <option value="day:{{ $groupIndex }}">
                                            {{ $bookingGroup->first()->session->date->locale('id')->translatedFormat('l, d M') }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-filament::icon icon="heroicon-o-chevron-down" />
                            </label>

                            <label class="member-class-custom-date" x-show="filterMode === 'custom'" x-cloak>
                                <span class="fi-sr-only">Pilih tanggal kelas</span>
                                <input type="date" x-model="customDate" />
                            </label>
                        </div>
                    @endif

                    <a href="{{ route('classes.index') }}#class-results" class="member-add-class-button">
                        <x-filament::icon icon="heroicon-o-plus" />
                        Tambah kelas
                    </a>
                </div>
            </header>

            @if ($bookingGroups->isEmpty())
                <div class="member-classes-empty">
                    <span aria-hidden="true"><x-filament::icon icon="heroicon-o-calendar-days" /></span>
                    <div><strong>Belum ada kelas yang dibooking.</strong><p>Pilih jadwal latihan yang sesuai dengan rutinitasmu.</p></div>
                </div>
            @else
                @foreach ($bookingGroups as $groupIndex => $bookingGroup)
                    <div
                        class="member-booked-class-track"
                        x-show="isGroupVisible({{ $groupIndex }}, '{{ $bookingGroup->first()->session->date->toDateString() }}')"
                        x-cloak
                    >
                        @foreach ($bookingGroup as $booking)
                            <article class="member-booked-class-card">
                                <div class="member-booked-class-date">
                                    <span>{{ $booking->session->date->locale('id')->translatedFormat('D') }}</span>
                                    <strong>{{ $booking->session->date->format('d') }}</strong>
                                    <small>{{ $booking->session->date->locale('id')->translatedFormat('M Y') }}</small>
                                </div>

                                <div class="member-booked-class-content">
                                    <div class="member-booked-class-heading">
                                        <p>Kelas kebugaran</p>
                                        <span class="member-booked-class-status">Terdaftar</span>
                                    </div>

                                    <strong class="member-booked-class-time">{{ $booking->session->gymClass->time_range }}</strong>
                                    <h2>{{ $booking->session->gymClass->name }}</h2>

                                    @if ($booking->session->gymClass->instructor)
                                        <div class="member-booked-class-instructor">
                                            <span aria-hidden="true"><x-filament::icon icon="heroicon-o-user" /></span>
                                            {{ $booking->session->gymClass->instructor }}
                                        </div>
                                    @endif

                                </div>

                                <div class="member-booked-class-actions">
                                    <div class="member-booked-class-meta">
                                        <span><x-filament::icon icon="heroicon-o-users" /> {{ $booking->session->capacity }} kapasitas</span>
                                        <span><x-filament::icon icon="heroicon-o-calendar-days" /> Kelas grup</span>
                                    </div>

                                    <form
                                        method="POST"
                                        action="{{ route('classes.bookings.cancel', $booking) }}"
                                        data-member-class-cancel-form
                                        data-class-name="{{ $booking->session->gymClass->name }}"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="Batalkan booking {{ $booking->session->gymClass->name }}">
                                            <x-filament::icon icon="heroicon-o-x-mark" />
                                            Batalkan booking
                                        </button>
                                    </form>
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
                        <strong x-text="customDate === '' ? 'Pilih tanggal untuk melihat jadwal.' : 'Tidak ada kelas pada tanggal ini.'"></strong>
                        <p>Gunakan pilihan Semua jadwal untuk melihat seluruh kelas yang sudah kamu booking.</p>
                    </div>
                </div>
            @endif
        </section>
    </div>
</x-filament-panels::page>
