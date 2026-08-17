<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\DealerRequestController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\StoreAccountController;
use App\Models\Currency;
use App\Services\TcmbExchangeRateService;
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

    $maxAge = max(10, (int) config('services.tcmb.cache_ttl_seconds', 45));

    return response()
        ->json($rates)
        ->header('Cache-Control', 'public, max-age='.$maxAge);
})->name('api.exchange-rates');

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/giris', [StoreAccountController::class, 'showLogin'])->name('store.login.show');
Route::post('/store-login', [StoreAccountController::class, 'login'])->name('store.login');
Route::post('/cikis', [StoreAccountController::class, 'logout'])->name('store.logout');

Route::get('/', [StoreController::class, 'index'])->name('home');
Route::get('/urun/{product}', [StoreController::class, 'showProduct'])->name('store.product.show');
Route::get('/sozlesme/{slug}', [LegalPageController::class, 'show'])->name('store.legal.show');
Route::get('/iletisim', [LegalPageController::class, 'showContact'])->name('store.contact');
Route::post('/iletisim', [ContactMessageController::class, 'store'])->middleware('throttle:5,60')->name('store.contact.send');
Route::get('/bayi-ol', [StoreController::class, 'dealerRegistrationPage'])->name('store.dealer-registration');
Route::post('/bayilik-talebi', [DealerRequestController::class, 'store'])->name('dealer-requests.store');
Route::get('/bayilik-talebi', fn () => redirect()->route('store.dealer-registration'))->name('dealer-requests.form');

// Sepet, ödeme, sipariş ve hesap sayfası giriş yapmış kullanıcılara açık
Route::middleware('auth')->group(function () {
    Route::get('/hesabim', [StoreAccountController::class, 'account'])->name('store.account');
    Route::get('/sepet', [StoreController::class, 'cart'])->name('store.cart');
    Route::post('/sepet/ekle', [StoreController::class, 'addToCart'])->name('store.cart.add');
    Route::post('/sepet/guncelle', [StoreController::class, 'updateCart'])->name('store.cart.update');
    Route::post('/sepet/kaldir/{cartKey}', [StoreController::class, 'removeFromCart'])->name('store.cart.remove');
    Route::get('/odeme', [StoreController::class, 'checkout'])->name('store.checkout');
    Route::post('/siparis-olustur', [StoreController::class, 'placeOrder'])->name('store.place-order');
    Route::get('/siparis/{order}', [StoreController::class, 'orderConfirmation'])->name('store.order-confirmation');
});
