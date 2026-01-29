<?php

namespace App\Providers;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Currency;
use App\Models\FooterMenuGroup;
use App\Models\FooterSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Docker ortamında port mapping (80->8010) nedeniyle redirect'ler yanlış URL'e gidiyor.
        // APP_URL'i zorla kullanarak bu sorunu çözüyoruz.
        $appUrl = config('app.url');
        if ($appUrl) {
            URL::forceRootUrl($appUrl);
        }

        // Livewire geçici yükleme dizini (Repeater içi FileUpload "failed to upload" hatalarını önlemek için)
        $livewireDisk = config('livewire.temporary_file_upload.disk') ?? config('filesystems.default');
        $livewireDir = config('livewire.temporary_file_upload.directory') ?? 'livewire-tmp';
        File::ensureDirectoryExists(Storage::disk($livewireDisk)->path($livewireDir), 0755);

        // Varyasyon görselleri için public dizinler
        File::ensureDirectoryExists(Storage::disk('public')->path('product_variations'), 0755);
        File::ensureDirectoryExists(Storage::disk('public')->path('variation_options'), 0755);

        View::composer('store.layout', function ($view) {
            $view->with('menuCategories', Category::treeForMenu());
            $currencies = Currency::forCurrentUser();
            $code = request('currency', session('store_currency', 'TRY'));
            $selectedCurrency = $currencies->firstWhere('code', $code) ?? $currencies->first() ?? Currency::getDefault();
            if ($selectedCurrency) {
                session(['store_currency' => $selectedCurrency->code]);
            }
            $view->with('currencies', $currencies);
            $view->with('selectedCurrency', $selectedCurrency);
            $footerSetting = FooterSetting::get();
            $view->with('footerSetting', $footerSetting);
            $view->with('footerMenuGroups', FooterMenuGroup::with('items')->orderBy('sort_order')->get());
            $view->with('bankAccounts', BankAccount::active()->orderBy('sort_order')->get());
        });
    }
}
