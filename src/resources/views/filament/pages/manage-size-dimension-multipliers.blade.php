<x-filament-panels::page>
    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Ebat, renk ve adet çarpan tablolarını aynı sütun yapısıyla düzenleyin. Sırayı sol tutamaçtan sürükleyerek değiştirebilirsiniz.
    </p>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-start">
            <x-filament::button type="submit" size="lg">
                Kaydet
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
