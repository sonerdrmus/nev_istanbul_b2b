<x-filament::button.group class="w-max">
    <x-filament::button
        color="primary"
        grouped
        icon="heroicon-o-check-circle"
        tag="div"
    >
        Tek seçim (zorunlu)
    </x-filament::button>

    <x-filament::button
        color="gray"
        grouped
        icon="heroicon-o-squares-plus"
        tag="div"
        class="pointer-events-none opacity-40"
    >
        Birden fazla seçilebilir
    </x-filament::button>
</x-filament::button.group>
