<x-filament-panels::page>
    <div class="member-account-settings-page">
        <header class="member-summary-title-card">
            <x-filament::breadcrumbs
                :breadcrumbs="[
                    route('filament.member.pages.dashboard') => 'CukipGym',
                    'Pengaturan akun',
                ]"
            />
            <h1>Pengaturan akun</h1>
        </header>

        <form wire:submit="save" class="member-settings-form">
            {{ $this->form }}

            <footer class="member-settings-actions">
                <button type="button" class="member-settings-cancel" wire:click="resetForm">
                    Batal
                </button>
                <button type="submit" class="member-settings-save">
                    Simpan
                </button>
            </footer>
        </form>
    </div>
</x-filament-panels::page>
