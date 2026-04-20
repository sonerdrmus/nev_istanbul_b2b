<?php

namespace App\Providers;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Currency;
use App\Models\FooterMenuGroup;
use App\Models\FooterSetting;
use App\Models\Product;
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

        // Livewire geçici yükleme: config'de public + livewire-tmp kullanılıyor (Docker'da yazılabilir)
        $livewireDisk = config('livewire.temporary_file_upload.disk') ?? config('filesystems.default');
        $livewireDir = config('livewire.temporary_file_upload.directory') ?? 'livewire-tmp';
        File::ensureDirectoryExists(Storage::disk($livewireDisk)->path($livewireDir), 0755);

        // Varyasyon görselleri ve Livewire temp için public dizinler
        File::ensureDirectoryExists(Storage::disk('public')->path('product_variations'), 0755);
        File::ensureDirectoryExists(Storage::disk('public')->path('variation_options'), 0755);
        File::ensureDirectoryExists(Storage::disk('public')->path('livewire-tmp'), 0755);

        View::composer('store.layout', function ($view) {
            $view->with('menuCategories', Category::treeForMenu());

            $homeMegaSlugs = ['tisort', 'bags', 'towels', 'hats', 'socks', 'aprons'];
            $homeMegaLabels = [
                'tisort' => 'Tişört',
                'bags' => 'Bags',
                'towels' => 'Towels',
                'hats' => 'Hats',
                'socks' => 'Socks',
                'aprons' => 'Aprons',
            ];
            $homeMegaCategories = Category::query()
                ->active()
                ->whereIn('slug', $homeMegaSlugs)
                ->with(['children' => fn ($q) => $q->active()->orderBy('sort_order')->orderBy('name')])
                ->get()
                ->keyBy('slug');
            $homeMegaNav = [];
            foreach ($homeMegaSlugs as $slug) {
                $category = $homeMegaCategories->get($slug);
                $categoryIds = collect([$category?->id])
                    ->merge($category?->children?->pluck('id') ?? [])
                    ->filter()
                    ->values();

                $featuredProducts = collect();
                if ($categoryIds->isNotEmpty()) {
                    $featuredProducts = Product::query()
                        ->active()
                        ->visibleInStore()
                        ->whereIn('category_id', $categoryIds)
                        ->with(['currency', 'productImages'])
                        ->orderBy('sort_order')
                        ->orderByDesc('id')
                        ->limit(8)
                        ->get();
                }

                $homeMegaNav[] = [
                    'slug' => $slug,
                    'label' => $homeMegaLabels[$slug] ?? $slug,
                    'category' => $category,
                    'products' => $featuredProducts,
                ];
            }
            $view->with('homeMegaNav', $homeMegaNav);
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
