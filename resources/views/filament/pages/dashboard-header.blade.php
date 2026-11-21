<div class="admin-dashboard-header-card">
    <div class="admin-dashboard-header-main">
        <div>
            <x-filament::breadcrumbs
                :breadcrumbs="[
                    route('filament.admin.pages.dashboard') => 'CukipGym',
                    'Dashboard admin',
                ]"
            />
            <h1>Ringkasan operasional.</h1>
        </div>
        <div class="admin-dashboard-header-filter">
            <label>
                <span class="fi-sr-only">Periode ringkasan</span>
                <select wire:model.live="dashboardPeriod" aria-label="Periode ringkasan">
                    <option value="week">Minggu ini</option>
                    <option value="month">Bulan ini</option>
                    <option value="year">Tahun ini</option>
                    <option value="custom">Custom</option>
                </select>
            </label>
            <button type="button" wire:click="$set('dashboardPeriod', 'custom')" aria-label="Pilih rentang tanggal" title="Pilih rentang tanggal">
                <x-filament::icon icon="heroicon-o-calendar-days" />
            </button>
        </div>
    </div>

    @if ($this->dashboardPeriod === 'custom')
        <div class="admin-dashboard-custom-period">
            <label>
                <span>Dari tanggal</span>
                <input type="date" wire:model.live="dashboardStartDate" max="{{ $this->dashboardEndDate }}">
            </label>
            <label>
                <span>Sampai tanggal</span>
                <input type="date" wire:model.live="dashboardEndDate" min="{{ $this->dashboardStartDate }}">
            </label>
        </div>
    @endif
</div>
