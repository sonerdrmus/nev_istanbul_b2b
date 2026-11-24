<?php

use App\Http\Controllers\AdminTwoFactorController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\DealerRequestController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProformaInvoiceController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StoreAccountController;
use App\Http\Controllers\StoreController;
use App\Models\Currency;
use App\Services\TcmbExchangeRateService;
use App\Support\StoreLocale;
use Illuminate\Support\Facades\Route;

Route::get('/api/store/search-products', [StoreController::class, 'searchProducts'])->name('api.store.search-products');

Route::get('/api/exchange-rates', function (TcmbExchangeRateService $tcmb) {
    $tcmbRates = $tcmb->getUsdEurCached();

    $rates = Currency::active()
        ->where('code', '!=', 'TRY')
        ->orderBy('sort_order')
        ->get(['code', 'exchange_rate'])
        ->mapWithKeys(function ($c) use ($tcmbRates) {
            $code = strtoupper((string) $c->code);

            return [$c->code => (float) ($tcmbRates[$code] ?? $c->exchange_rate)];
        })
        ->all();

    // İstemci poll ile hizalı: TCMB cache TTL (varsayılan 15 dk)
    $maxAge = max(60, (int) config('services.tcmb.cache_ttl_seconds', 900));

    return response()
        ->json($rates)
        ->header('Cache-Control', 'public, max-age='.$maxAge);
})->name('api.exchange-rates');

Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/', function () {
    return redirect('/'.StoreLocale::default(), 301);
});

$defaultLocale = StoreLocale::default();

Route::get('/giris', fn () => redirect()->route('store.login.show', ['locale' => $defaultLocale], 301));
Route::get('/urun/{product}', fn (string $product) => redirect()->route('store.product.show', ['locale' => $defaultLocale, 'product' => $product], 301));
Route::get('/blog/{post}', fn (string $post) => redirect()->route('store.blog.show', ['locale' => $defaultLocale, 'post' => $post], 301));
Route::get('/sozlesme/{slug}', fn (string $slug) => redirect()->route('store.legal.show', ['locale' => $defaultLocale, 'slug' => $slug], 301));
Route::get('/iletisim', fn () => redirect()->route('store.contact', ['locale' => $defaultLocale], 301));
Route::get('/bayi-ol', fn () => redirect()->route('store.dealer-registration', ['locale' => $defaultLocale], 301));
Route::get('/bayilik-talebi', fn () => redirect()->route('store.dealer-registration', ['locale' => $defaultLocale], 301));
Route::get('/hesabim', fn () => redirect()->route('store.account', ['locale' => $defaultLocale], 301));
Route::get('/sepet', fn () => redirect()->route('store.cart', ['locale' => $defaultLocale], 301));
Route::get('/odeme', fn () => redirect()->route('store.checkout', ['locale' => $defaultLocale], 301));
Route::get('/siparis/{order}', fn (string $order) => redirect()->route('store.order-confirmation', ['locale' => $defaultLocale, 'order' => $order], 301));

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::middleware('auth')->prefix(config('app.admin_panel_path'))->group(function () {
    Route::get('/two-factor/setup', [AdminTwoFactorController::class, 'showSetup'])->name('admin.2fa.setup');
    Route::post('/two-factor/setup', [AdminTwoFactorController::class, 'confirmSetup'])->name('admin.2fa.setup.confirm');
    Route::get('/two-factor/challenge', [AdminTwoFactorController::class, 'showChallenge'])->name('admin.2fa.challenge');
    Route::post('/two-factor/challenge', [AdminTwoFactorController::class, 'confirmChallenge'])->name('admin.2fa.challenge.confirm');
});

$localeConstraint = implode('|', StoreLocale::supported());

Route::prefix('{locale}')
    ->where(['locale' => $localeConstraint])
    ->group(function () {
        Route::get('/', [StoreController::class, 'index'])->name('home');

        Route::get('/giris', [StoreAccountController::class, 'showLogin'])->name('store.login.show');
        Route::post('/store-login', [StoreAccountController::class, 'login'])->middleware('throttle:10,1')->name('store.login');
        Route::post('/cikis', [StoreAccountController::class, 'logout'])->name('store.logout');

        Route::get('/urun/{product}', [StoreController::class, 'showProduct'])->name('store.product.show');
        Route::get('/blog/{post}', [BlogController::class, 'show'])->name('store.blog.show');
        Route::get('/sozlesme/{slug}', [LegalPageController::class, 'show'])->name('store.legal.show');
        Route::get('/iletisim', [LegalPageController::class, 'showContact'])->name('store.contact');
        Route::post('/iletisim', [ContactMessageController::class, 'store'])->middleware('throttle:5,60')->name('store.contact.send');
        Route::get('/bayi-ol', [StoreController::class, 'dealerRegistrationPage'])->name('store.dealer-registration');
        Route::post('/bayilik-talebi', [DealerRequestController::class, 'store'])->middleware('throttle:5,60')->name('dealer-requests.store');
        Route::get('/bayilik-talebi', fn () => redirect()->route('store.dealer-registration'))->name('dealer-requests.form');

        Route::middleware('auth')->group(function () {
            Route::get('/hesabim', [StoreAccountController::class, 'account'])->name('store.account');
            Route::get('/sepet', [StoreController::class, 'cart'])->name('store.cart');
            Route::post('/sepet/ekle', [StoreController::class, 'addToCart'])->name('store.cart.add');
            Route::post('/sepet/guncelle', [StoreController::class, 'updateCart'])->name('store.cart.update');
            Route::post('/sepet/kaldir/{cartKey}', [StoreController::class, 'removeFromCart'])->name('store.cart.remove');
            Route::post('/sepet/temizle', [StoreController::class, 'clearCart'])->name('store.cart.clear');
            Route::get('/odeme', [StoreController::class, 'checkout'])->name('store.checkout');
            Route::post('/siparis-olustur', [StoreController::class, 'placeOrder'])->name('store.place-order');
            Route::get('/siparis/{order}', [StoreController::class, 'orderConfirmation'])->name('store.order-confirmation');
            Route::get('/siparis/{order}/proforma.pdf', [ProformaInvoiceController::class, 'pdf'])->name('store.proforma.pdf');
            Route::get('/siparis/{order}/proforma.xlsx', [ProformaInvoiceController::class, 'excel'])->name('store.proforma.excel');
            Route::get('/siparis/{order}/siparis-formu.pdf', [ProformaInvoiceController::class, 'orderForm'])->name('store.order-form.pdf');
        });
    });
