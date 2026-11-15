<x-filament-panels::page>
    <form wire:submit="save" class="homepage-content-form">
        {{ $this->form }}

        <div class="homepage-content-save">
            <x-filament::button type="submit">
                Simpan perubahan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
