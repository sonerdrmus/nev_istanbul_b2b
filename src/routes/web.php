<?php

use App\Http\Controllers\StoreController;
use App\Http\Controllers\DealerRequestController;
use App\Models\Currency;
use Illuminate\Support\Facades\Route;

Route::get('/api/store/search-products', [StoreController::class, 'searchProducts'])->name('api.store.search-products');

Route::get('/api/exchange-rates', function () {
    $rates = Currency::active()
        ->where('code', '!=', 'TRY')
        ->orderBy('sort_order')
        ->get(['code', 'exchange_rate'])
        ->mapWithKeys(fn ($c) => [$c->code => (float) $c->exchange_rate]);
    return response()->json($rates->all());
})->name('api.exchange-rates');

Route::get('/', [StoreController::class, 'index'])->name('home');
Route::get('/urun/{product}', [StoreController::class, 'showProduct'])->name('store.product.show');
Route::post('/store-login', [StoreController::class, 'login'])->name('store.login');
Route::get('/bayi-ol', [StoreController::class, 'dealerRegistrationPage'])->name('store.dealer-registration');
Route::post('/bayilik-talebi', [DealerRequestController::class, 'store'])->name('dealer-requests.store');
Route::get('/bayilik-talebi', fn () => redirect()->route('store.dealer-registration'))->name('dealer-requests.form');

// Sepet, ödeme ve sipariş sadece giriş yapmış müşterilere açık
Route::middleware('auth')->group(function () {
    Route::get('/sepet', [StoreController::class, 'cart'])->name('store.cart');
    Route::post('/sepet/ekle', [StoreController::class, 'addToCart'])->name('store.cart.add');
    Route::post('/sepet/guncelle', [StoreController::class, 'updateCart'])->name('store.cart.update');
    Route::post('/sepet/kaldir/{cartKey}', [StoreController::class, 'removeFromCart'])->name('store.cart.remove');
    Route::get('/odeme', [StoreController::class, 'checkout'])->name('store.checkout');
    Route::post('/siparis-olustur', [StoreController::class, 'placeOrder'])->name('store.place-order');
    Route::get('/siparis/{order}', [StoreController::class, 'orderConfirmation'])->name('store.order-confirmation');
});
