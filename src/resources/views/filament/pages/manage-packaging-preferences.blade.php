<x-filament-panels::page>
    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Ambalaj türleri, kilitli poşet malzemeleri, özelleştirme seçenekleri ve barkod/etiket alanı ayarlarını tek sayfadan yönetin.
        Ürün varyasyonu tipi <strong>Ambalaj Türü</strong> seçildiğinde ambalaj türleri otomatik içe aktarılır; mağazada malzeme ve özelleştirme adımları bu kayıtlardan gelir.
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
