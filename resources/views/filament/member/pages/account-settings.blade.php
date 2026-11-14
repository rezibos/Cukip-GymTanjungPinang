<x-filament-panels::page>
    <div class="member-page-intro"><p>Kelola nama, nomor WhatsApp, dan password akun. Alamat email login tidak dapat diubah dari halaman ini.</p></div>

    <form wire:submit="save" class="member-settings-form">
        {{ $this->form }}

        <div>
            <x-filament::button type="submit">Simpan perubahan</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
