<x-filament-panels::page>
    <form wire:submit="save" class="gym-profile-form">
        {{ $this->form }}

        <div class="gym-profile-save">
            <x-filament::button type="submit">
                Simpan perubahan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
