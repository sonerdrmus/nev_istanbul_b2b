<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-3">
            <x-filament::button type="submit" size="lg">
                Kaydet
            </x-filament::button>
        </div>
    </form>

    <div class="mt-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
        <h3 class="text-base font-semibold text-gray-950 dark:text-white">Test maili gönder</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Kaydettikten sonra SMTP’nin çalıştığını doğrulamak için kullanın. Önce ayarlar kaydedilir, sonra test gider.
        </p>
        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="w-full sm:max-w-sm">
                <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">Alıcı e-posta</span>
                </label>
                <input
                    type="email"
                    wire:model="test_email"
                    class="fi-input block w-full rounded-lg border-none bg-white px-3 py-2 text-sm text-gray-950 shadow-sm ring-1 ring-gray-950/10 focus:ring-2 focus:ring-primary-600 dark:bg-white/5 dark:text-white dark:ring-white/20"
                    placeholder="ornek@sirket.com"
                >
            </div>
            <x-filament::button type="button" color="gray" wire:click="sendTest" wire:loading.attr="disabled">
                Test gönder
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
