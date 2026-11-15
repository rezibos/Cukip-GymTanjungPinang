<x-filament-panels::page>
    @php
        $dashboard = $this->dashboardData();
        $stats = [
            ['label' => 'Member Aktif', 'value' => number_format($dashboard['stats']['activeMembers']), 'meta' => 'Membership masih berlaku', 'icon' => 'heroicon-o-user-group'],
            ['label' => 'Instruktur Aktif', 'value' => number_format($dashboard['stats']['activeTrainers']), 'meta' => 'Siap menerima jadwal', 'icon' => 'heroicon-o-academic-cap'],
            ['label' => 'Income Bulan Ini', 'value' => 'Rp'.number_format($dashboard['stats']['monthlyIncome'], 0, ',', '.'), 'meta' => now()->locale('id')->translatedFormat('F Y'), 'icon' => 'heroicon-o-banknotes'],
            ['label' => 'Booking Kelas Bulan Ini', 'value' => number_format($dashboard['stats']['monthlyBookings']), 'meta' => 'Booking berstatus aktif', 'icon' => 'heroicon-o-calendar-days'],
        ];
        $membershipMetrics = [
            ['label' => 'Membership expired', 'data' => $dashboard['membershipSummary']['expired']],
            ['label' => 'Membership aktif', 'data' => $dashboard['membershipSummary']['active']],
            ['label' => 'Membership baru', 'data' => $dashboard['membershipSummary']['new']],
        ];
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    @endphp

    <div class="operational-dashboard">
        <section class="operational-stats" aria-label="Statistik utama">
            @foreach ($stats as $stat)
                <article class="operational-stat">
                    <div class="operational-stat-copy">
                        <span>{{ $stat['label'] }}</span>
                        <strong>{{ $stat['value'] }}</strong>
                        <small>{{ $stat['meta'] }}</small>
                    </div>
                    <span class="operational-stat-icon" aria-hidden="true">
                        <x-filament::icon :icon="$stat['icon']" />
                    </span>
                </article>
            @endforeach
        </section>

        <div class="operational-dashboard-grid">
            <section class="operational-panel membership-summary-panel">
                <div class="operational-panel-heading">
                    <div>
                        <h2>Rangkuman membership</h2>
                        <p>Perubahan status member pada bulan terpilih.</p>
                    </div>
                    <div class="dashboard-period-control" aria-label="Pilih bulan rangkuman membership">
                        <button type="button" wire:click="previousSummaryMonth" wire:loading.attr="disabled" aria-label="Bulan sebelumnya">‹</button>
                        <span>{{ $dashboard['monthLabel'] }}</span>
                        <button type="button" wire:click="nextSummaryMonth" wire:loading.attr="disabled" aria-label="Bulan berikutnya">›</button>
                    </div>
                </div>

                <div class="membership-rings">
                    @foreach ($membershipMetrics as $metric)
                        <div class="membership-ring-item">
                            <div class="membership-ring" style="--ring-progress: {{ min(100, $metric['data']['percentage']) }}%;" role="img" aria-label="{{ $metric['label'] }} {{ $metric['data']['count'] }}">
                                <span>{{ $metric['data']['count'] }}</span>
                                <small>{{ round($metric['data']['percentage']) }}%</small>
                            </div>
                            <div>
                                <p>{{ $metric['label'] }}</p>
                                <span>{{ $metric['data']['count'] }} data tercatat</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            @foreach ([['title' => 'Membership chart', 'description' => 'Paket membership paling laris bulan ini', 'data' => $dashboard['membershipPlans']], ['title' => 'Instruktur chart', 'description' => 'Instruktur dengan sesi terbanyak bulan ini', 'data' => $dashboard['instructors']]] as $chart)
                <section class="operational-panel distribution-panel" x-data="{ selectedIndex: null, selectItem(index) { this.selectedIndex = this.selectedIndex === index ? null : index; } }">
                    <div class="operational-panel-heading">
                        <div>
                            <h2>{{ $chart['title'] }}</h2>
                            <p>{{ $chart['description'] }}</p>
                        </div>
                        <span class="distribution-total">{{ $chart['data']['total'] }} total</span>
                    </div>
                    <div class="distribution-content">
                        <div class="distribution-donut" role="group" aria-label="Diagram {{ $chart['title'] }}">
                            <svg viewBox="0 0 42 42" aria-hidden="true">
                                <circle class="distribution-donut-track" cx="21" cy="21" r="15.9155" pathLength="100" />
                                @foreach ($chart['data']['items'] as $index => $item)
                                    <circle
                                        class="distribution-donut-segment"
                                        x-bind:class="{ 'is-selected': selectedIndex === {{ $index }}, 'is-muted': selectedIndex !== null && selectedIndex !== {{ $index }} }"
                                        x-on:click.stop="selectItem({{ $index }})"
                                        x-on:keydown.enter.stop.prevent="selectItem({{ $index }})"
                                        x-on:keydown.space.stop.prevent="selectItem({{ $index }})"
                                        cx="21"
                                        cy="21"
                                        r="15.9155"
                                        pathLength="100"
                                        fill="none"
                                        stroke="{{ $item['color'] }}"
                                        stroke-dasharray="{{ $item['percentage'] }} {{ 100 - $item['percentage'] }}"
                                        stroke-dashoffset="{{ -$item['offset'] }}"
                                        role="button"
                                        tabindex="0"
                                        aria-label="Pilih {{ $item['label'] }}, {{ $item['count'] }} data"
                                    />
                                @endforeach
                            </svg>
                            <div class="distribution-donut-center">
                                <div x-show="selectedIndex === null">
                                    <strong>{{ $chart['data']['total'] }}</strong>
                                    <span>total</span>
                                </div>
                                @foreach ($chart['data']['items'] as $index => $item)
                                    <div x-cloak x-show="selectedIndex === {{ $index }}">
                                        <strong>{{ $item['count'] }}</strong>
                                        <span>{{ round($item['percentage']) }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if ($chart['data']['items'] !== [])
                            <ul class="distribution-legend">
                                @foreach ($chart['data']['items'] as $index => $item)
                                    <li>
                                        <button
                                            type="button"
                                            x-bind:class="{ 'is-selected': selectedIndex === {{ $index }}, 'is-muted': selectedIndex !== null && selectedIndex !== {{ $index }} }"
                                            x-on:click="selectItem({{ $index }})"
                                            aria-label="Pilih {{ $item['label'] }}"
                                        >
                                            <i style="--legend-color: {{ $item['color'] }};"></i>
                                            <span>{{ $item['label'] }}</span>
                                            <strong>{{ $item['count'] }}</strong>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="distribution-empty">Belum ada data bulan ini.</p>
                        @endif
                    </div>
                    @if ($chart['data']['items'] !== [])
                        <div class="distribution-selection" aria-live="polite">
                            <p x-show="selectedIndex === null">Pilih warna atau nama untuk melihat detail.</p>
                            @foreach ($chart['data']['items'] as $index => $item)
                                <div x-cloak x-show="selectedIndex === {{ $index }}">
                                    <span><i style="--legend-color: {{ $item['color'] }};"></i>{{ $item['label'] }}</span>
                                    <strong>{{ $item['count'] }} data · {{ round($item['percentage']) }}%</strong>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach

            <section class="operational-panel income-panel">
                <div class="operational-panel-heading">
                    <div>
                        <h2>Total income</h2>
                        <p>Pendapatan lunas per bulan sepanjang tahun.</p>
                    </div>
                    <div class="dashboard-period-control" aria-label="Pilih tahun total income">
                        <button type="button" wire:click="previousIncomeYear" wire:loading.attr="disabled" aria-label="Tahun sebelumnya">‹</button>
                        <span>{{ $this->incomeYear }}</span>
                        <button type="button" wire:click="nextIncomeYear" wire:loading.attr="disabled" aria-label="Tahun berikutnya">›</button>
                    </div>
                </div>
                <div class="income-chart">
                    <div class="income-chart-scale" aria-hidden="true">
                        <span>{{ $dashboard['income']['maximumLabel'] }}</span>
                        <span>{{ $dashboard['income']['midpointLabel'] }}</span>
                        <span>Rp0</span>
                    </div>
                    <svg viewBox="0 0 720 210" role="img" aria-label="Grafik total income tahun {{ $this->incomeYear }}">
                        <defs>
                            <linearGradient id="income-area-gradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#171717" stop-opacity="0.16" />
                                <stop offset="100%" stop-color="#171717" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                        <line x1="16" y1="30" x2="704" y2="30" />
                        <line x1="16" y1="110" x2="704" y2="110" />
                        <line x1="16" y1="190" x2="704" y2="190" />
                        <path class="income-chart-area" d="{{ $dashboard['income']['areaPath'] }}" />
                        <path class="income-chart-line" d="{{ $dashboard['income']['linePath'] }}" />
                        @foreach ($dashboard['income']['pointCoordinates'] as $index => $point)
                            <circle class="{{ $point['value'] === max($dashboard['income']['values']) && $point['value'] > 0 ? 'is-peak' : '' }}" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="{{ $point['value'] > 0 ? 4 : 2.5 }}" />
                        @endforeach
                    </svg>
                    <div class="income-chart-months" aria-hidden="true">
                        @foreach ($monthLabels as $label)
                            <span>{{ $label }}</span>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="operational-panel today-classes-panel">
                <div class="operational-panel-heading">
                    <div>
                        <h2>Kelas hari ini</h2>
                        <p>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <a href="{{ \App\Filament\Resources\ClassSessions\ClassSessionResource::getUrl('index') }}">Semua sesi <span aria-hidden="true">↗</span></a>
                </div>

                @if ($dashboard['todayClasses']->isEmpty())
                    <div class="today-classes-empty">
                        <span aria-hidden="true"><x-filament::icon icon="heroicon-o-calendar-days" /></span>
                        <strong>Belum ada kelas hari ini</strong>
                        <p>Jadwalkan sesi agar aktivitas kelas hari ini terlihat di ringkasan.</p>
                        <a href="{{ \App\Filament\Resources\ClassSessions\ClassSessionResource::getUrl('index', ['action' => 'create']) }}">Tambah jadwal sesi</a>
                    </div>
                @else
                    <div class="today-class-list">
                        @foreach ($dashboard['todayClasses'] as $session)
                            <a class="today-class-item" href="{{ \App\Filament\Resources\ClassSessions\ClassSessionResource::getUrl('edit', ['record' => $session]) }}">
                                <span>{{ $session->date->locale('id')->translatedFormat('l, d M') }}</span>
                                <strong>{{ $session->gymClass?->time_range ?? 'Waktu belum diatur' }}</strong>
                                <p>{{ $session->gymClass?->name ?? 'Kelas CukipGym' }}</p>
                                <div class="today-class-meta">
                                    <span>{{ $session->gymClass?->instructor ?: 'Tim CukipGym' }}</span>
                                    <span>{{ $session->active_bookings_count }} / {{ $session->capacity }} peserta</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-filament-panels::page>
