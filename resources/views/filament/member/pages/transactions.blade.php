<x-filament-panels::page>
    <div class="member-transaction-history">
        <header class="member-summary-title-card">
            <x-filament::breadcrumbs
                :breadcrumbs="[
                    route('filament.member.pages.dashboard') => 'CukipGym',
                    'Transaksi',
                ]"
            />
            <h1>Transaksi</h1>
        </header>

        <section class="member-transaction-panel" x-data="{ filtersOpen: false }">
            <form method="GET" action="{{ url()->current() }}" class="member-transaction-toolbar">
                <label class="member-transaction-search">
                    <span class="fi-sr-only">Cari transaksi</span>
                    <x-filament::icon icon="heroicon-o-magnifying-glass" />
                    <input
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari transaksi"
                        autocomplete="off"
                    />
                </label>

                @if ($statusFilter !== '')
                    <input type="hidden" name="status" value="{{ $statusFilter }}" />
                @endif
                @if ($typeFilter !== '')
                    <input type="hidden" name="type" value="{{ $typeFilter }}" />
                @endif
                <input type="hidden" name="per_page" value="{{ $perPage }}" />

                <button type="submit" class="member-transaction-search-submit">Cari</button>
                <button
                    type="button"
                    class="member-transaction-filter-trigger"
                    x-on:click="filtersOpen = ! filtersOpen"
                    x-bind:aria-expanded="filtersOpen"
                    aria-controls="member-transaction-filters"
                    aria-label="Buka filter transaksi"
                >
                    <x-filament::icon icon="heroicon-m-funnel" />
                    <span>{{ $activeFilterCount }}</span>
                </button>
            </form>

            <form
                method="GET"
                action="{{ url()->current() }}"
                id="member-transaction-filters"
                class="member-transaction-filters"
                x-show="filtersOpen"
                x-on:click.outside="filtersOpen = false"
                x-on:keydown.escape.window="filtersOpen = false"
                x-cloak
            >
                @if ($search !== '')
                    <input type="hidden" name="search" value="{{ $search }}" />
                @endif
                <input type="hidden" name="per_page" value="{{ $perPage }}" />

                <div class="member-transaction-filter-header">
                    <strong>Filters</strong>
                    <a href="{{ url()->current() }}">Reset</a>
                </div>

                <label>
                    <span>Status</span>
                    <select name="status">
                        <option value="">Semua status</option>
                        <option value="pending" @selected($statusFilter === 'pending')>Menunggu Pembayaran</option>
                        <option value="paid" @selected($statusFilter === 'paid')>Lunas</option>
                        <option value="failed" @selected($statusFilter === 'failed')>Gagal</option>
                        <option value="expired" @selected($statusFilter === 'expired')>Kedaluwarsa</option>
                        <option value="refunded" @selected($statusFilter === 'refunded')>Dikembalikan</option>
                    </select>
                </label>

                <label>
                    <span>Jenis transaksi</span>
                    <select name="type">
                        <option value="">Semua jenis</option>
                        <option value="membership" @selected($typeFilter === 'membership')>Membership</option>
                        <option value="trainer" @selected($typeFilter === 'trainer')>Personal Trainer</option>
                    </select>
                </label>

                <div class="member-transaction-filter-actions">
                    <button type="submit">Terapkan filter</button>
                </div>
            </form>

            <div class="member-transaction-table-panel">
                <div class="member-transaction-table-scroll">
                    <table class="member-transaction-table">
                    <colgroup>
                        <col class="member-transaction-column-invoice" />
                        <col class="member-transaction-column-product" />
                        <col class="member-transaction-column-type" />
                        <col class="member-transaction-column-date" />
                        <col class="member-transaction-column-total" />
                        <col class="member-transaction-column-status" />
                        <col class="member-transaction-column-action" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Transaksi</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            @php
                                $trainerBookings = $payment->trainerBookings;
                                $transactions = $payment->transactions;
                                $checkoutItems = collect($payment->checkout_items ?? []);
                                $isTrainerPayment = $trainerBookings->isNotEmpty();
                                $firstTrainerBooking = $trainerBookings->first();
                                $planNames = $transactions
                                    ->map(fn ($transaction) => $transaction->membership?->plan?->name)
                                    ->filter()
                                    ->unique()
                                    ->values();
                                $checkoutNames = $checkoutItems
                                    ->pluck('name')
                                    ->filter()
                                    ->unique()
                                    ->values();
                                $trainerName = $firstTrainerBooking?->slot?->trainer?->name;
                                $itemName = $isTrainerPayment
                                    ? 'Personal Trainer · '.($trainerName ?: 'Sesi privat')
                                    : ($planNames->first() ?: $checkoutNames->first() ?: 'Membership CukipGym');
                                $itemCount = $isTrainerPayment
                                    ? $trainerBookings->count()
                                    : ($transactions->sum('quantity') ?: $checkoutItems->sum('quantity'));
                                $displayInvoice = $transactions->first()?->invoice_number
                                    ?? sprintf(
                                        '%s-%s-%05d',
                                        $isTrainerPayment ? 'PT' : 'PAY',
                                        $payment->created_at->format('Ymd'),
                                        $payment->id,
                                    );
                                $statusClass = match ($payment->status->value) {
                                    'paid' => 'is-paid',
                                    'pending' => 'is-pending',
                                    'refunded' => 'is-refunded',
                                    default => 'is-failed',
                                };
                                $statusIcon = match ($payment->status->value) {
                                    'paid' => 'heroicon-o-check-circle',
                                    'pending' => 'heroicon-o-clock',
                                    'refunded' => 'heroicon-o-arrow-uturn-left',
                                    'expired' => 'heroicon-o-exclamation-circle',
                                    default => 'heroicon-o-x-circle',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <span class="member-transaction-invoice" title="ID pembayaran: {{ $payment->order_id }}">
                                        {{ $displayInvoice }}
                                    </span>
                                </td>
                                <td>
                                    <div class="member-transaction-product">
                                        <div>
                                            <strong>{{ $itemName }}</strong>
                                            <span>
                                                {{ max(1, $itemCount) }} {{ $isTrainerPayment ? 'sesi' : 'paket' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="member-transaction-type">{{ $isTrainerPayment ? 'Personal Trainer' : 'Membership' }}</span>
                                </td>
                                <td>
                                    <strong>{{ $payment->created_at->locale('id')->translatedFormat('d M Y') }}</strong>
                                    <span>{{ $payment->created_at->format('H:i') }} WIB</span>
                                </td>
                                <td class="member-transaction-amount">Rp{{ number_format($payment->gross_amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="member-transaction-status {{ $statusClass }}">
                                        <x-filament::icon :icon="$statusIcon" />
                                        {{ $payment->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    <a
                                        href="{{ route('payments.pending', ['order_id' => $payment->order_id]) }}"
                                        class="member-transaction-detail"
                                    >
                                        <x-filament::icon icon="heroicon-o-eye" />
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="member-transaction-empty">
                                        <span aria-hidden="true"><x-filament::icon icon="heroicon-o-receipt-percent" /></span>
                                        <div>
                                            <strong>Belum ada transaksi yang cocok.</strong>
                                            <p>Pembelian membership dan Personal Trainer akan muncul di halaman ini.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                </div>

                <footer class="member-transaction-footer">
                    <p>
                        @if ($payments->total() > 0)
                            Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} results
                        @else
                            Showing 0 results
                        @endif
                    </p>

                    <form method="GET" action="{{ url()->current() }}" class="member-transaction-per-page">
                        @if ($search !== '')
                            <input type="hidden" name="search" value="{{ $search }}" />
                        @endif
                        @if ($statusFilter !== '')
                            <input type="hidden" name="status" value="{{ $statusFilter }}" />
                        @endif
                        @if ($typeFilter !== '')
                            <input type="hidden" name="type" value="{{ $typeFilter }}" />
                        @endif
                        <label for="member-transactions-per-page">Per page</label>
                        <select id="member-transactions-per-page" name="per_page" onchange="this.form.submit()">
                            @foreach ([5, 10, 25] as $option)
                                <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </form>

                    @if ($payments->lastPage() > 1)
                        <nav class="member-transaction-pagination" aria-label="Navigasi halaman transaksi">
                            @if ($payments->previousPageUrl())
                                <a href="{{ $payments->previousPageUrl() }}" aria-label="Halaman sebelumnya">
                                    <x-filament::icon icon="heroicon-o-chevron-left" />
                                </a>
                            @else
                                <span aria-hidden="true"><x-filament::icon icon="heroicon-o-chevron-left" /></span>
                            @endif
                            <strong>{{ $payments->currentPage() }} / {{ $payments->lastPage() }}</strong>
                            @if ($payments->nextPageUrl())
                                <a href="{{ $payments->nextPageUrl() }}" aria-label="Halaman berikutnya">
                                    <x-filament::icon icon="heroicon-o-chevron-right" />
                                </a>
                            @else
                                <span aria-hidden="true"><x-filament::icon icon="heroicon-o-chevron-right" /></span>
                            @endif
                        </nav>
                    @else
                        <span class="member-transaction-pagination-placeholder" aria-hidden="true"></span>
                    @endif
                </footer>
            </div>
        </section>
    </div>
</x-filament-panels::page>
