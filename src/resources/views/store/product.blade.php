@extends('store.layout')

@section('store_content_full_width', '1')

@section('title', $product->meta_title ?: $product->name)

@push('meta')
    @if($product->meta_description)
        <meta name="description" content="{{ $product->meta_description }}">
    @endif
    @if($product->meta_keywords)
        <meta name="keywords" content="{{ $product->meta_keywords }}">
    @endif
@endpush

@section('content')
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-4">
        <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">{{ __('store.product.home') }}</a>
        <span>/</span>
        @if($product->category)
            @if($product->category->parent)
                <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">{{ __('store.product.breadcrumb_products') }}</a>
                <span>/</span>
                <span class="text-slate-700">{{ $product->category->parent->name }} › {{ $product->category->name }}</span>
            @else
                <a href="{{ route('home', ['category' => $product->category->slug]) }}" class="hover:text-primary-600 transition-colors">{{ $product->category->name }}</a>
            @endif
            <span>/</span>
        @endif
        <span class="text-slate-900 font-medium truncate max-w-[200px] sm:max-w-none">{{ $product->name }}</span>
    </nav>

    @php
        $hasVariations = $product->variations->isNotEmpty();
        $showPurchasePanel = $product->isOnSale()
            && ($product->stock_quantity === null || (int) $product->stock_quantity > 0);
        $selectedCurrency = $selectedCurrency ?? \App\Models\Currency::getDefault();
        $minOrderProduct = $product->getMinimumOrderQuantity();
        $normalPriceTry = null;
        $baseTry = null;
        $baseConverted = null;
        $hasProductDiscount = false;
        if ($canSeePrices && $selectedCurrency) {
            $normalPriceTry = $product->getPriceInTRY($customerDiscountPercent ?? null);
            $discountUnitTry = $product->getDiscountUnitPriceInTRY(1, $customerGroupId ?? 1);
            $hasProductDiscount = $discountUnitTry !== null && $discountUnitTry < $normalPriceTry;
            if ($hasProductDiscount) {
                $discountedPriceTry = $discountUnitTry * (1 - ($customerDiscountPercent ?? 0) / 100);
                $baseTry = $discountedPriceTry;
            } else {
                $baseTry = $normalPriceTry;
            }
            $baseConverted = $selectedCurrency->convertFromTRY($baseTry);
        }
    @endphp

    {{-- Sol: sütun genişliği görsele sıkı (auto + sabit genişlik); sağ kalan alan (1fr). Geniş boş sütun kalmaz. --}}
    {{-- items-start + kısa sol sütun: sticky üst kutusu satırdan erken çıkar; lg:self-stretch ile sol hücre satır boyuna uzar ve sticky viewport’ta kalır --}}
    <div class="grid grid-cols-1 lg:grid-cols-[auto_minmax(0,1fr)] gap-6 lg:gap-6 xl:gap-8 items-start">
        {{-- Sol lg+: galeri + “Seçilen seçenekler” birlikte sticky; sağdaki uzun varyasyon listesi kayarken sabit kalır --}}
        @php $displayImages = $product->display_image_urls; @endphp
        <div class="w-full max-w-[30.8rem] mx-auto lg:mx-0 lg:max-w-none lg:w-[19.8rem] xl:w-[22rem] 2xl:w-[34.2rem] shrink-0 lg:self-stretch">
        <div class="lg:sticky lg:top-24 lg:z-[1] lg:shrink-0 lg:self-start w-full">
        <div class="rounded-2xl overflow-hidden bg-slate-100 aspect-square max-h-[400px] lg:max-h-[min(88vh,572px)] lg:aspect-square relative shadow-2xl shadow-slate-300/30 ring-1 ring-slate-200/70 w-full">
            @if(count($displayImages) > 0)
                <div id="product-gallery" class="relative w-full h-full flex items-center justify-center cursor-zoom-in" role="region" aria-label="{{ __('store.product.gallery_aria') }}" title="{{ __('store.product.zoom_hint') }}">
                    @foreach($displayImages as $idx => $url)
                        <div class="product-gallery-slide absolute inset-0 flex items-center justify-center transition-opacity duration-300 {{ $idx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}" data-slide-index="{{ $idx }}" data-image-url="{{ $url }}" aria-hidden="{{ $idx !== 0 }}">
                            <img src="{{ $url }}" alt="{{ __('store.product.image_alt', ['name' => $product->name, 'num' => $idx + 1]) }}" class="max-w-full max-h-full w-auto h-auto object-contain">
                        </div>
                    @endforeach
                    @if(count($displayImages) > 1)
                        <div class="absolute bottom-3 left-0 right-0 z-20 flex justify-center gap-1.5">
                            @foreach($displayImages as $idx => $url)
                                <button type="button" class="product-gallery-dot w-2.5 h-2.5 rounded-full transition-colors {{ $idx === 0 ? 'bg-white ring-2 ring-primary-500' : 'bg-white/60 hover:bg-white/80' }}" data-slide-index="{{ $idx }}" aria-label="{{ __('store.product.slide_aria', ['n' => $idx + 1]) }}"></button>
                            @endforeach
                        </div>
                        <button type="button" class="product-gallery-prev absolute left-2 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-md flex items-center justify-center text-slate-700 transition-opacity" aria-label="{{ __('store.product.prev_image') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" class="product-gallery-next absolute right-2 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-md flex items-center justify-center text-slate-700 transition-opacity" aria-label="{{ __('store.product.next_image') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endif
                </div>
                {{-- Lightbox: tıklanınca büyütme --}}
                <div id="product-image-lightbox" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/90 p-4" aria-modal="true" role="dialog" aria-label="{{ __('store.product.lightbox_aria') }}">
                    <button type="button" id="lightbox-close" class="absolute top-4 right-4 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors" aria-label="{{ __('store.product.lightbox_close') }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    @if(count($displayImages) > 1)
                        <button type="button" id="lightbox-prev" class="absolute left-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors" aria-label="{{ __('store.product.prev_image') }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" id="lightbox-next" class="absolute right-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors" aria-label="{{ __('store.product.next_image') }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endif
                    <img id="lightbox-image" src="" alt="{{ $product->name }}" class="max-w-full max-h-[90vh] w-auto h-auto object-contain">
                </div>
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="text-slate-300 text-8xl">📦</span>
                </div>
            @endif
        </div>
        @if(count($displayImages) > 0)
        @push('head')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var gallery = document.getElementById('product-gallery');
                var lightbox = document.getElementById('product-image-lightbox');
                var lightboxImg = document.getElementById('lightbox-image');
                if (!gallery || !lightbox || !lightboxImg) return;
                var slides = gallery.querySelectorAll('.product-gallery-slide');
                var urls = Array.from(slides).map(function(s) { return s.getAttribute('data-image-url'); });
                var originalFirstGalleryUrl = urls.length ? urls[0] : null;
                function refreshGalleryUrlsFromDom() {
                    slides = gallery.querySelectorAll('.product-gallery-slide');
                    urls = Array.from(slides).map(function(s) { return s.getAttribute('data-image-url'); });
                }
                window.__productGalleryRefreshUrls = refreshGalleryUrlsFromDom;
                window.__productGalleryOriginalFirstUrl = originalFirstGalleryUrl;
                var currentIndex = 0;
                function openLightbox(index) {
                    currentIndex = (index + urls.length) % urls.length;
                    lightboxImg.src = urls[currentIndex];
                    lightbox.classList.remove('hidden');
                    lightbox.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                }
                function closeLightbox() {
                    lightbox.classList.add('hidden');
                    lightbox.classList.remove('flex');
                    document.body.style.overflow = '';
                }
                function updateLightboxImage() {
                    lightboxImg.src = urls[currentIndex];
                }
                gallery.addEventListener('click', function(e) {
                    if (e.target.closest('button')) return;
                    var activeSlide = gallery.querySelector('.product-gallery-slide.opacity-100');
                    var idx = activeSlide ? parseInt(activeSlide.getAttribute('data-slide-index'), 10) : 0;
                    openLightbox(idx);
                });
                lightbox.addEventListener('click', function(e) {
                    if (e.target === lightbox) closeLightbox();
                });
                var closeBtn = document.getElementById('lightbox-close');
                if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
                var lightboxPrev = document.getElementById('lightbox-prev');
                var lightboxNext = document.getElementById('lightbox-next');
                if (lightboxPrev) {
                    lightboxPrev.addEventListener('click', function(e) { e.stopPropagation(); currentIndex = (currentIndex - 1 + urls.length) % urls.length; updateLightboxImage(); });
                }
                if (lightboxNext) {
                    lightboxNext.addEventListener('click', function(e) { e.stopPropagation(); currentIndex = (currentIndex + 1) % urls.length; updateLightboxImage(); });
                }
                document.addEventListener('keydown', function(e) {
                    if (lightbox.classList.contains('hidden')) return;
                    if (e.key === 'Escape') closeLightbox();
                    if (e.key === 'ArrowLeft' && lightboxPrev) { currentIndex = (currentIndex - 1 + urls.length) % urls.length; updateLightboxImage(); }
                    if (e.key === 'ArrowRight' && lightboxNext) { currentIndex = (currentIndex + 1) % urls.length; updateLightboxImage(); }
                });
            });
        </script>
        @endpush
        @endif
        @if(count($displayImages) > 1)
        @push('head')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var el = document.getElementById('product-gallery');
                if (!el) return;
                var slides = el.querySelectorAll('.product-gallery-slide');
                var dots = el.querySelectorAll('.product-gallery-dot');
                var prev = el.querySelector('.product-gallery-prev');
                var next = el.querySelector('.product-gallery-next');
                var current = 0;
                function goTo(i) {
                    current = (i + slides.length) % slides.length;
                    slides.forEach(function(s, idx) {
                        s.classList.toggle('opacity-100', idx === current);
                        s.classList.toggle('opacity-0', idx !== current);
                        s.classList.toggle('z-10', idx === current);
                        s.classList.toggle('z-0', idx !== current);
                        s.setAttribute('aria-hidden', idx !== current);
                    });
                    dots.forEach(function(d, idx) {
                        d.classList.toggle('bg-white', idx === current);
                        d.classList.toggle('ring-2', idx === current);
                        d.classList.toggle('ring-primary-500', idx === current);
                        d.classList.toggle('bg-white/60', idx !== current);
                    });
                }
                dots.forEach(function(dot, i) {
                    dot.addEventListener('click', function() { goTo(i); });
                });
                if (prev) prev.addEventListener('click', function() { goTo(current - 1); });
                if (next) next.addEventListener('click', function() { goTo(current + 1); });
            });
        </script>
        @endpush
        @endif

        @if($hasVariations && $showPurchasePanel)
            {{-- Seçilen seçenekler özeti: ürün görselinin altında --}}
            <div id="variation-summary-wrap" class="mt-4 lg:mt-5 rounded-2xl border border-slate-200/90 bg-white p-4 shadow-sm ring-1 ring-slate-900/5 sm:p-5">
                <p class="mb-3 flex items-center gap-2.5 text-sm font-semibold text-slate-900">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-600/10 text-primary-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </span>
                    {{ __('store.product.variation_selected_heading') }}
                </p>
                <div class="overflow-hidden rounded-xl border border-slate-200/90 bg-slate-50/40">
                    <style>
                        #variation-summary-tbody tr.summary-customization-row td { padding: 0; border: 0; vertical-align: top; }
                        #variation-summary-tbody .customization-summary-metric:nth-child(odd) { background: rgba(248, 250, 252, 0.65); }
                        @media (min-width: 640px) {
                            #variation-summary-tbody .customization-summary-metric { background: transparent; }
                        }
                    </style>
                    <table class="w-full border-collapse text-sm" aria-label="{{ __('store.product.variation_summary_aria') }}">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-100/80">
                                <th scope="col" class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-600 sm:px-4">{{ __('store.product.option_column') }}</th>
                                <th scope="col" class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-600 sm:px-4">{{ __('store.product.value_column') }}</th>
                            </tr>
                        </thead>
                        <tbody id="variation-summary-tbody" class="divide-y divide-slate-100 bg-white">
                            <tr>
                                <td colspan="2" class="px-3 py-8 text-center text-sm text-slate-400 sm:px-4">{{ __('store.product.summary_select_prompt') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p id="variation-summary-warning" class="mt-3 text-sm text-amber-700 font-medium hidden" role="alert">
                    {{ __('store.product.summary_cart_warning') }}
                </p>
                @if($canSeePrices)
                <div id="variation-confirm-wrap" class="mt-4 hidden">
                    <label class="flex items-start gap-3 cursor-pointer group">
                        <input type="checkbox" id="variation-confirm-checkbox" name="variation_confirmed" value="1" form="add-to-cart-form" class="mt-1 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">{{ __('store.product.variation_confirm_label') }}</span>
                    </label>
                </div>
                @endif
                @if($canSeePrices && $baseTry !== null && $selectedCurrency)
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50/80 px-3.5 py-3">
                    <div class="grid grid-cols-[auto_1fr] items-center gap-x-3 gap-y-1">
                        <span class="text-sm font-semibold text-slate-600">{{ __('store.product.unit_price_label') }}</span>
                        <div class="text-right">
                            <span id="product-base-price" class="text-sm font-semibold text-slate-700">
                                {{ $selectedCurrency->format($baseConverted) }}
                            </span>
                        </div>
                        <span class="text-sm font-semibold text-slate-600">{{ __('store.product.order_total_price_label') }}</span>
                        <div class="text-right">
                            <span id="product-price" data-base-try="{{ $baseTry }}" @if($hasProductDiscount && $normalPriceTry !== null) data-normal-try="{{ $normalPriceTry }}" @endif data-exchange-rate="{{ (float) $selectedCurrency->exchange_rate }}" data-currency-code="{{ $selectedCurrency->code }}" data-currency-symbol="{{ $selectedCurrency->symbol }}" class="text-lg font-bold text-slate-900">
                                {{ $selectedCurrency->format($selectedCurrency->convertFromTRY(0)) }}
                            </span>
                            @if($hasProductDiscount)
                                <div id="product-price-strike" class="hidden mt-0.5 text-right text-xs text-slate-400 line-through"></div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                @auth
                <button type="submit" id="add-to-cart-btn" form="add-to-cart-form" class="mt-4 w-full py-3 sm:py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm sm:text-base font-semibold flex items-center justify-center gap-2 shadow-md shadow-primary-600/20 disabled:opacity-60 disabled:cursor-not-allowed" disabled>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span id="add-to-cart-btn-label">{{ __('store.product.add_to_cart_hint') }}</span>
                </button>
                @endauth
                @guest
                <button type="button" id="add-to-cart-login-cta" onclick="document.getElementById('login-modal').classList.remove('hidden')" class="mt-4 w-full py-3 sm:py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm sm:text-base font-semibold flex items-center justify-center gap-2 shadow-md shadow-primary-600/20">
                    {{ __('store.product.login_for_order') }}
                </button>
                @endguest
            </div>
        @endif
        </div>{{-- /sticky: galeri + seçilen seçenekler --}}
        </div>

        {{-- Sağ: başlık, fiyat, açıklama ve sipariş / varyasyon alanı (sayfa ile birlikte kayar) --}}
        <div class="min-w-0">
            @if($product->company)
                <p class="text-xs font-medium text-primary-600 uppercase tracking-wider mb-1">{{ $product->company->name }}</p>
            @endif
            @if($product->category)
                <a href="{{ route('home', ['category' => $product->category->slug]) }}" class="inline-block text-sm text-slate-500 hover:text-primary-600 mb-2">{{ $product->category->name }}</a>
            @endif
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">{{ $product->name }}</h1>

            @php $productStatus = $product->status ?? 'satista'; @endphp
            @if($productStatus !== 'satista')
                <div class="mt-3">
                    @if($productStatus === 'stokta_yok')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('store.index.out_of_stock') }}
                        </span>
                    @elseif($productStatus === 'yakinda_gelecek')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('store.index.coming_soon_badge') }}
                        </span>
                    @endif
                </div>
            @endif

            @if(!$canSeePrices)
                <p class="mt-4 text-slate-500 text-sm">{{ __('store.product.login_for_order') }}</p>
            @endif

            @if($product->description)
                <div class="mt-6 prose prose-slate max-w-none text-slate-600 text-sm sm:text-base prose-img:rounded-lg">
                    {!! $product->description !!}
                </div>
            @endif

            @if($canSeePrices)
                <div class="mt-6 flex items-center gap-2 text-sm">
                    <span class="font-medium text-slate-700">{{ __('store.product.stock_label_short') }}</span>
                    @if($product->stock_quantity === null)
                        <span class="text-slate-600">{{ __('store.product.stock_not_tracked') }}</span>
                    @elseif((int) $product->stock_quantity > 0)
                        <span class="text-slate-600">{{ __('store.product.stock_units_fmt', ['count' => number_format($product->stock_quantity, 0, ',', '.')]) }}</span>
                    @else
                        <span class="text-red-600 font-medium">{{ __('store.index.out_of_stock') }}</span>
                    @endif
                </div>
            @endif

    @if($showPurchasePanel)
    @php
        $minOrder = $product->getMinimumOrderQuantity();
        $availableStock = $product->stock_quantity !== null ? (int) $product->stock_quantity : 999999;
    @endphp
    <section class="mt-5 lg:mt-6 w-full" aria-label="{{ __('store.product.section_order_options') }}">
        <div class="max-w-full">
            <form action="{{ route('store.cart.add') }}" method="POST" class="rounded-2xl lg:rounded-2xl border border-slate-200/90 bg-slate-50/90 p-2 sm:p-5 lg:p-2 shadow-sm shadow-slate-200/30 ring-1 ring-slate-200/40" id="add-to-cart-form" data-available-stock="{{ $availableStock }}" data-size-table-trigger-variation="{{ e($product->size_table_trigger_variation ?? '') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                @if($hasVariations)
                    <input type="hidden" name="variation_data" id="variation-data-input" value="">
                @endif
                <input type="hidden" name="size_quantities" id="size-quantities-input" value="">

                {{-- Normal adet alanı (varyasyon yoksa hemen göster, varyasyon varsa tüm seçenekler seçilince göster) --}}
                <div id="quantity-simple-wrap" class="{{ $hasVariations ? 'hidden' : '' }}">
                    <label class="block font-semibold text-slate-700 mb-2">{{ __('store.product.qty_label') }}</label>
                    <input type="number" name="quantity" id="quantity-input" value="{{ $minOrder }}" min="{{ $minOrder }}" max="{{ $availableStock }}" class="w-full max-w-xs sm:max-w-sm lg:max-w-md rounded-xl border border-slate-300 px-4 py-3.5 lg:px-5 lg:py-4 text-base text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    @if($minOrder > 1)
                        <p class="mt-1 text-sm text-slate-500">{{ __('store.product.min_order_line', ['count' => $minOrder]) }}</p>
                    @endif
                </div>

                @if($hasVariations)
                    {{-- Varyasyon adımları: üstte, "Seçenekleri belirleyin" + timeline; son step = Beden Tablosu --}}
                    @php
                        $variationSteps = [];
                        $addedNames = [];
                        $variationsCollection = $product->variations;
                        $sizeTableTriggerVariation = trim((string) ($product->size_table_trigger_variation ?? ''));
                        while (count($variationSteps) < $variationsCollection->count()) {
                            $eligible = [];
                            foreach ($variationsCollection as $v) {
                                if (in_array($v->name, $addedNames, true)) {
                                    continue;
                                }
                                $dep = $v->depends_on ?? '';
                                if ($dep === '' || in_array($dep, $addedNames, true)) {
                                    $eligible[] = $v;
                                }
                            }
                            if ($eligible === []) {
                                break;
                            }
                            $chosen = null;
                            if ($sizeTableTriggerVariation !== '') {
                                foreach ($eligible as $v) {
                                    if (strcasecmp(trim((string) $v->name), $sizeTableTriggerVariation) === 0) {
                                        $chosen = $v;
                                        break;
                                    }
                                }
                            }
                            if ($chosen === null) {
                                $chosen = $eligible[0];
                            }
                            $variationSteps[] = $chosen;
                            $addedNames[] = $chosen->name;
                        }
                        $totalSteps = count($variationSteps);
                        $sizeStepIndex = $totalSteps;
                        $customStepIndex = $totalSteps + 1;
                        $sizeTables = $sizeTables ?? collect();
                    @endphp
                    @push('head')
                    <style>
                        .product-option.fabric-option-card.option-selected .fabric-option-accent { background-color: #155fb3; }
                        .product-option.fabric-option-card.option-selected .fabric-option-radio {
                            border-color: #155fb3;
                            background-color: #155fb3;
                            box-shadow: inset 0 0 0 3px #fff;
                        }
                        .product-option.fabric-option-card.option-selected [class*="bg-slate-100"] {
                            background-color: #dbeafe;
                            color: #1e40af;
                        }
                    </style>
                    @endpush
                    <section class="mt-3 lg:mt-4 w-full" aria-labelledby="variations-heading">
                        <div class="rounded-2xl border border-slate-200/70 bg-gradient-to-b from-white to-slate-50/60 shadow-sm overflow-hidden ring-1 ring-slate-200/30">
                            <div class="px-4 sm:px-5 lg:px-6 py-3.5 lg:py-4 border-b border-slate-200/70 bg-white/95">
                                <h2 id="variations-heading" class="text-lg sm:text-xl lg:text-2xl font-semibold text-slate-800 tracking-tight flex items-center gap-2.5">
                                    <span class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    </span>
                                    {{ __('store.product.variations_heading') }}
                                </h2>
                                <p class="mt-1 text-sm sm:text-base text-slate-500 leading-snug">{{ __('store.product.variations_subtitle') }}</p>
                            </div>
                            <div id="product-variations" class="variation-steps-container px-3.5 sm:px-5 lg:px-6 py-4 lg:py-5" data-customization-step-index="{{ $customStepIndex }}" data-size-step-index="{{ $sizeStepIndex }}">
                                @foreach($variationSteps as $stepIndex => $variation)
                                    @php $isDependent = !empty($variation->depends_on); @endphp
                                    <div class="product-variation-block variation-step-panel flex flex-row gap-0 {{ $loop->first ? '' : 'mt-3 lg:mt-4' }} {{ $isDependent ? 'dependent-variation-block' : '' }}"
                                         data-variation-name="{{ $variation->name }}"
                                         data-variation-type="{{ $variation->type }}"
                                         data-depends-on="{{ $variation->depends_on ?? '' }}"
                                         data-step-index="{{ $stepIndex }}"
                                         data-replace-main-gallery="{{ $variation->replace_main_gallery_image ? '1' : '0' }}"
                                         data-allows-multiple="{{ $variation->allows_multiple ? '1' : '0' }}"
                                         data-multi-confirmed="0"
                                         @if($isDependent) style="display: none;" @endif>
                                        <div class="variation-timeline-cell flex flex-col items-center w-10 sm:w-11 shrink-0 pt-3 sm:pt-3.5">
                                            <span class="variation-step-num flex h-7 w-7 sm:h-8 sm:w-8 shrink-0 items-center justify-center rounded-full text-xs sm:text-sm font-bold ring-2 ring-white sm:ring-4 bg-slate-200 text-slate-600 shadow-sm z-10 transition-colors duration-300">{{ $stepIndex + 1 }}</span>
                                            <div class="w-0.5 flex-1 min-h-[6px] -mt-0.5 -mb-4 pb-4 bg-slate-200 rounded-full self-center" aria-hidden="true"></div>
                                        </div>
                                        <div class="variation-step-card flex-1 min-w-0 rounded-xl border border-slate-200/90 bg-white overflow-hidden transition-all duration-300 -ml-px shadow-sm">
                                            <button type="button" class="variation-step-dot w-full flex flex-row items-center gap-2.5 text-left py-3 sm:py-3.5 px-4 sm:px-5 bg-slate-50/90 hover:bg-slate-100/80 border-b border-slate-100/90 transition-colors {{ $stepIndex === 0 ? 'bg-primary-50/90 border-primary-100/80' : '' }} focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-inset" data-step="{{ $stepIndex }}" aria-label="{{ __('store.product.variation_pick_aria', ['name' => $variation->name]) }}">
                                                <span class="variation-step-name text-sm sm:text-base font-semibold text-slate-800">{{ $variation->name }}</span>
                                                <span class="variation-step-check hidden shrink-0 text-emerald-600 ml-auto" aria-hidden="true"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>
                                            </button>
                                            <div class="variation-step-summary hidden flex items-center justify-between gap-2 px-4 sm:px-5 py-2.5 sm:py-3 bg-slate-50/70 border-b border-slate-100/90">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="text-slate-500 text-sm">{{ $variation->name }}:</span>
                                                    <span class="variation-step-summary-value font-medium text-slate-800">—</span>
                                                </div>
                                                <button type="button" class="variation-step-change-btn text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('store.product.change') }}</button>
                                            </div>
                                            <div class="variation-step-full p-3.5 sm:p-4 lg:px-5 lg:py-4 {{ $stepIndex > 0 ? 'hidden' : '' }}">
                                                <div class="@if($variation->type === 'fabric') fabric-options-grid grid grid-cols-1 lg:grid-cols-2 gap-2.5 sm:gap-3 @else flex flex-wrap gap-2.5 sm:gap-3 lg:gap-3.5 @endif product-variation-options">
                                    @foreach($variation->options as $option)
                                        @php $optionClasses = 'product-option border-2 border-slate-300 hover:border-primary-500 hover:shadow-md hover:shadow-primary-500/10 focus:outline-none focus:ring-2 focus:ring-primary-500/30 transition-all rounded-xl'; @endphp
                                        @php $parentIdsList = $option->getParentOptionIdsList(); @endphp
                                        @php
                                            $colorFabricGroupId = ($variation->type === 'color')
                                                ? optional($option->interfaceColorVariation)->interface_fabric_type_variation_id
                                                : null;
                                        @endphp
                                        @if($variation->type === 'fabric')
                                            @php
                                                $fabricParts = \App\Support\FabricOptionDisplay::parse($option->option_value);
                                                $fabricImageUrl = $option->option_image ? \App\Support\MediaUrl::public($option->option_image) : null;
                                            @endphp
                                            <button type="button"
                                                class="{{ $optionClasses }} fabric-option-card group w-full text-left flex items-stretch overflow-hidden bg-white hover:bg-slate-50/80 min-h-[4.25rem]"
                                                data-variation="{{ $variation->name }}"
                                                data-option="{{ $option->option_value }}"
                                                data-option-id="{{ $option->id }}"
                                                data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}"
                                                data-parent-option-id="{{ $option->parent_option_id ?? '' }}"
                                                data-parent-option-ids="{{ json_encode($parentIdsList) }}"
                                                data-price-delta="{{ (float) $option->price_delta }}"
                                                data-option-image-url="{{ $fabricImageUrl ?? '' }}"
                                                data-fabric-preset-id="{{ $option->interface_fabric_type_variation_id ?? '' }}"
                                                title="{{ $fabricParts['full'] }}">
                                                <span class="fabric-option-accent w-1 shrink-0 bg-slate-200 transition-colors" aria-hidden="true"></span>
                                                <span class="flex flex-1 items-center gap-3 px-3.5 sm:px-4 py-3 min-w-0">
                                                    @if($fabricImageUrl)
                                                        <img src="{{ $fabricImageUrl }}" alt="" class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg object-cover shrink-0 border border-slate-200/80">
                                                    @endif
                                                    <span class="fabric-option-radio shrink-0 w-4 h-4 rounded-full border-2 border-slate-300 bg-white transition-colors" aria-hidden="true"></span>
                                                    <span class="flex-1 min-w-0">
                                                        <span class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                                            @if($fabricParts['yarn_count'])
                                                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[11px] sm:text-xs font-semibold text-slate-600 tabular-nums">{{ $fabricParts['yarn_count'] }}</span>
                                                            @endif
                                                            <span class="text-sm sm:text-[0.9375rem] font-semibold text-slate-800 leading-snug">{{ $fabricParts['name'] }}</span>
                                                        </span>
                                                        @if($fabricParts['weight'])
                                                            <span class="mt-0.5 block text-xs sm:text-sm text-slate-500 leading-snug">{{ $fabricParts['weight'] }}</span>
                                                        @endif
                                                    </span>
                                                </span>
                                            </button>
                                        @elseif(($variation->type === 'image' || $variation->type === 'color') && $option->option_image)
                                            @php $optionImageUrl = \App\Support\MediaUrl::public($option->option_image); $imgSize = $option->option_image_size ?? 'medium'; $imgSizeClass = match($imgSize) { 'small' => 'w-14 h-14 sm:w-16 sm:h-16', 'large' => 'w-28 h-28 sm:w-32 sm:h-32', default => 'w-20 h-20 sm:w-24 sm:h-24' }; $minWClass = match($imgSize) { 'small' => 'min-w-[72px] sm:min-w-[88px]', 'large' => 'min-w-[120px] sm:min-w-[140px]', default => 'min-w-[88px] sm:min-w-[110px]' }; $labelMaxW = match($imgSize) { 'small' => 'max-w-[80px] sm:max-w-[88px]', 'large' => 'max-w-[120px] sm:max-w-[140px]', default => 'max-w-[96px] sm:max-w-[110px]' }; @endphp
                                            <button type="button" class="{{ $optionClasses }} flex flex-col items-center rounded-xl p-2 sm:p-3 {{ $minWClass }} relative" data-variation="{{ $variation->name }}" data-option="{{ $option->option_value }}" data-option-id="{{ $option->id }}" data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}" data-parent-option-id="{{ $option->parent_option_id ?? '' }}" data-parent-option-ids="{{ json_encode($parentIdsList) }}" data-price-delta="{{ (float) $option->price_delta }}" data-option-image-url="{{ $optionImageUrl }}" @if($variation->type === 'color') data-color-fabric-group-id="{{ $colorFabricGroupId ?? '' }}" @endif>
                                                <span class="relative inline-block group">
                                                    <img src="{{ $optionImageUrl }}" alt="{{ $option->option_value }}" class="{{ $imgSizeClass }} object-cover rounded-xl">
                                                    <span class="variation-zoom-btn absolute top-1 right-1 w-6 h-6 rounded-md bg-black/50 hover:bg-primary-600 flex items-center justify-center text-white cursor-pointer transition-all opacity-0 group-hover:opacity-100" data-image-url="{{ $optionImageUrl }}" data-image-alt="{{ $option->option_value }}" title="{{ __('store.product.variation_zoom') }}" role="button" aria-label="{{ $option->option_value }}{{ __('store.product.variation_zoom_aria_suffix') }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                                    </span>
                                                </span>
                                                <span class="text-sm font-medium text-slate-600 mt-2 w-full {{ $labelMaxW }} text-center break-words leading-tight">{{ $option->option_value }}</span>
                                            </button>
                                        @elseif($variation->type === 'color' && $option->option_color)
                                            <button type="button" class="{{ $optionClasses }} flex flex-col items-center rounded-xl p-1.5 shrink-0 min-w-[72px]" data-variation="{{ $variation->name }}" data-option="{{ $option->option_value }}" data-option-id="{{ $option->id }}" data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}" data-parent-option-id="{{ $option->parent_option_id ?? '' }}" data-parent-option-ids="{{ json_encode($parentIdsList) }}" data-price-delta="{{ (float) $option->price_delta }}" data-option-image-url="{{ $option->option_image ? \App\Support\MediaUrl::public($option->option_image) : '' }}" data-color-fabric-group-id="{{ $colorFabricGroupId ?? '' }}" title="{{ $option->option_value }}">
                                                <span class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg shrink-0 border border-slate-200" style="background-color: {{ $option->option_color }}"></span>
                                                <span class="text-xs font-medium text-slate-600 mt-1.5 w-full max-w-[88px] sm:max-w-[100px] text-center break-words leading-tight">{{ $option->option_value }}</span>
                                            </button>
                                        @else
                                            <button type="button" class="{{ $optionClasses }} px-4 py-2.5 sm:px-5 sm:py-3 text-sm sm:text-base font-medium text-slate-700 min-h-[2.75rem]" data-variation="{{ $variation->name }}" data-option="{{ $option->option_value }}" data-option-id="{{ $option->id }}" data-option-solo="{{ $variation->optionValueIsSoloChoice($option->option_value) ? '1' : '0' }}" data-parent-option-id="{{ $option->parent_option_id ?? '' }}" data-parent-option-ids="{{ json_encode($parentIdsList) }}" data-price-delta="{{ (float) $option->price_delta }}" data-option-image-url="{{ $option->option_image ? \App\Support\MediaUrl::public($option->option_image) : '' }}" @if($variation->type === 'color') data-color-fabric-group-id="{{ $colorFabricGroupId ?? '' }}" @endif>{{ $option->option_value }}</button>
                                        @endif
                                    @endforeach
                                                </div>
                                                @if($variation->allows_multiple)
                                                    <div class="variation-multi-continue-wrap mt-4 pt-3 border-t border-slate-100">
                                                        <p class="text-xs text-slate-500 mb-2">{{ __('store.product.variation_multi_hint') }}</p>
                                                        <button type="button" class="variation-multi-continue-btn w-full py-2.5 sm:py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                                            {{ __('store.product.variation_continue') }}
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                {{-- Beden / sipariş adeti — özelleştirmeden önce (zorunlu adım: Devam et) --}}
                                <div class="variation-step-panel flex flex-row gap-0 mt-3 lg:mt-4"
                                     data-step-index="{{ $sizeStepIndex }}"
                                     data-size-step-panel="1"
                                     data-size-step-confirmed="0">
                                    <div class="variation-timeline-cell flex flex-col items-center w-10 sm:w-11 shrink-0 pt-3 sm:pt-3.5">
                                        <span class="variation-step-num flex h-7 w-7 sm:h-8 sm:w-8 shrink-0 items-center justify-center rounded-full text-xs sm:text-sm font-bold ring-2 ring-white sm:ring-4 bg-slate-200 text-slate-600 shadow-sm z-10">{{ $sizeStepIndex + 1 }}</span>
                                        <div class="w-0.5 flex-1 min-h-[6px] -mt-0.5 -mb-4 pb-4 bg-slate-200 rounded-full self-center" aria-hidden="true"></div>
                                    </div>
                                    <div class="variation-step-card flex-1 min-w-0 rounded-xl border border-slate-200/90 bg-white overflow-hidden -ml-px shadow-sm">
                                        <button type="button" class="variation-step-dot w-full flex flex-row items-center gap-2.5 text-left py-3 sm:py-3.5 px-4 sm:px-5 bg-slate-50/90 hover:bg-slate-100/80 border-b border-slate-100/90 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-inset" data-step="{{ $sizeStepIndex }}" aria-label="{{ __('store.product.size_table_dot_aria') }}">
                                            <span class="variation-step-name text-sm sm:text-base font-semibold text-slate-800">{{ __('store.product.size_table_heading') }}</span>
                                            <span class="variation-step-check hidden shrink-0 text-emerald-600 ml-auto" aria-hidden="true"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>
                                        </button>
                                        {{-- İçerik sadece tüm varyasyonlar seçildikten sonra görünür; hangi tablo gösterileceği JS ile güncellenir --}}
                                        <div class="variation-step-full size-table-step-content p-3.5 sm:p-4 lg:px-5 lg:py-4 hidden">
                                            @forelse($sizeTables as $sizeTable)
                                            <div id="{{ $sizeTable->slug }}-size-table-wrap" class="hidden mt-4 first:mt-0 size-table-wrap" data-slug="{{ $sizeTable->slug }}" data-trigger-variation="{{ e($sizeTable->trigger_variation_name ?? '') }}" data-trigger-value="{{ e($sizeTable->trigger_option_value ?? '') }}">
                                                <p class="text-base sm:text-lg font-semibold text-slate-700 mb-3 sm:mb-4 flex items-center gap-2">
                                                    <span class="h-px flex-1 max-w-[40px] rounded-full bg-primary-200"></span>
                                                    {{ $sizeTable->title ?: __('store.product.choose_sizes_default') }}
                                                </p>
                                                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                                                    <table class="w-full min-w-[520px] border-collapse text-sm">
                                                        <thead>
                                                            <tr class="bg-primary-600 text-white">
                                                                <th class="text-left font-semibold py-3 px-3 rounded-tl-xl">{{ $sizeTable->title ?: $sizeTable->name }}</th>
                                                                @foreach($sizeTable->columns as $col)
                                                                    <th class="font-semibold py-3 px-2 text-center">{{ $col->size_value }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="bg-slate-100">
                                                                <td class="font-medium text-slate-700 py-3 px-3">{{ __('store.product.qty_order_row') }}</td>
                                                                @foreach($sizeTable->columns as $col)
                                                                    <td class="py-2 px-1 text-center">
                                                                        <input type="number" name="{{ $sizeTable->slug }}_size_qty_{{ $col->size_value }}" data-size="{{ $col->size_value }}" data-price-multiplier="{{ number_format((float) ($col->price_multiplier ?? 1), 4, '.', '') }}" min="0" max="999" value="0" class="size-table-input {{ $sizeTable->slug }}-size-input w-full max-w-[72px] mx-auto rounded-lg border border-slate-300 px-2 py-2 text-center text-slate-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30">
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="mt-3 flex flex-wrap items-center justify-center gap-3 rounded-xl border border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100/80 px-4 py-3 text-sm">
                                                    <span class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-1.5 shadow-sm ring-1 ring-slate-200/80">
                                                        <span class="text-slate-500 font-medium">{{ __('store.product.size_min_chip') }}</span>
                                                        <span class="font-bold text-slate-800">{{ __('store.product.stock_units_fmt', ['count' => number_format($minOrder)]) }}</span>
                                                    </span>
                                                    <span class="text-slate-300">·</span>
                                                    <span class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-1.5 shadow-sm ring-1 ring-slate-200/80">
                                                        <span class="text-slate-500 font-medium">{{ __('store.product.size_max_chip') }}</span>
                                                        <span class="font-bold text-slate-800">{{ $availableStock >= 999999 ? __('store.product.unlimited_qty') : __('store.product.stock_units_fmt', ['count' => number_format($availableStock)]) }}</span>
                                                    </span>
                                                    <span class="text-slate-300">·</span>
                                                    <span class="inline-flex items-center gap-2 rounded-lg bg-primary-50 px-3 py-1.5 shadow-sm ring-1 ring-primary-200/80">
                                                        <span class="text-slate-600 font-medium">{{ __('store.product.size_entered_total') }}</span>
                                                        <span id="{{ $sizeTable->slug }}-size-total" class="font-bold text-primary-700">0</span>
                                                        <span class="text-slate-600">{{ __('store.product.units_suffix') }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                            @empty
                                            <p class="text-sm text-slate-600 mb-4">{{ __('store.product.size_step_simple_qty_hint') }}</p>
                                            @endforelse
                                            <div id="size-step-continue-wrap" class="mt-4 pt-3 border-t border-slate-100">
                                                <button type="button" id="size-step-continue-btn" class="w-full py-2.5 sm:py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                                    {{ __('store.product.variation_continue') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Özelleştirme — son zorunlu adım (atlanamaz) --}}
                                <div class="variation-step-panel variation-customization-panel flex flex-row gap-0 mt-3 lg:mt-4"
                                     data-step-index="{{ $customStepIndex }}"
                                     data-customization-panel="1"
                                     data-customization-confirmed="0">
                                    <div class="variation-timeline-cell flex flex-col items-center w-10 sm:w-11 shrink-0 pt-3 sm:pt-3.5">
                                        <span class="variation-step-num flex h-7 w-7 sm:h-8 sm:w-8 shrink-0 items-center justify-center rounded-full text-xs sm:text-sm font-bold ring-2 ring-white sm:ring-4 bg-slate-200 text-slate-600 shadow-sm z-10 transition-colors duration-300">{{ $customStepIndex + 1 }}</span>
                                    </div>
                                    <div class="variation-step-card flex-1 min-w-0 rounded-xl border border-slate-200/90 bg-white overflow-hidden transition-all duration-300 -ml-px shadow-sm">
                                        <button type="button" class="variation-step-dot w-full flex flex-row items-center gap-2.5 text-left py-3 sm:py-3.5 px-4 sm:px-5 bg-slate-50/90 hover:bg-slate-100/80 border-b border-slate-100/90 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-inset" data-step="{{ $customStepIndex }}" aria-label="{{ __('store.product.customize_product') }}">
                                            <span class="variation-step-name text-sm sm:text-base font-semibold text-slate-800">{{ __('store.product.customize_product') }}</span>
                                            <span class="variation-step-check hidden shrink-0 text-emerald-600 ml-auto" aria-hidden="true"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>
                                        </button>
                                        <div class="variation-step-summary hidden flex items-center justify-between gap-2 px-4 sm:px-5 py-2.5 sm:py-3 bg-slate-50/70 border-b border-slate-100/90">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="text-slate-500 text-sm">{{ __('store.product.customize_product') }}:</span>
                                                <span class="variation-step-summary-value font-medium text-slate-800">—</span>
                                            </div>
                                            <button type="button" class="variation-step-change-btn text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('store.product.change') }}</button>
                                        </div>
                                        <div class="variation-step-full customization-step-full p-3.5 sm:p-4 lg:px-5 lg:py-4 hidden">
                                            <p class="text-sm text-slate-600 leading-snug mb-4">{{ __('store.product.customization_step_intro') }}</p>
                                            <p class="text-sm font-semibold text-slate-800 mb-2">{{ __('store.product.customization_table_caption') }}</p>
                                            @php
                                                $customizationPrintOptions = $productCustomization['print_techniques'] ?? [];
                                                $customizationDefaultPrint = (string) ($productCustomization['default_print_slug'] ?? 'emprime');
                                                $customizationMaxColors = max(1, (int) ($productCustomization['max_color_count'] ?? 7));
                                            @endphp
                                            <div id="product-customization-table" class="mb-5 space-y-3">
                                                <div class="hidden items-center gap-3 text-left sm:flex sm:rounded-xl sm:bg-slate-100/90 sm:px-4 sm:py-2.5">
                                                    <div class="flex w-11 shrink-0 justify-center" aria-hidden="true">
                                                        <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ __('store.product.customization_col_select') }}</span>
                                                    </div>
                                                    <div class="grid min-w-0 flex-1 grid-cols-1 gap-x-3 sm:grid-cols-11 sm:items-center">
                                                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-600 sm:col-span-3">{{ __('store.product.customization_col_position') }}</div>
                                                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-600 sm:col-span-3">{{ __('store.product.customization_col_dimensions') }}</div>
                                                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-600 sm:col-span-2">{{ __('store.product.customization_col_colors') }}</div>
                                                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-600 sm:col-span-3">{{ __('store.product.customization_col_print') }}</div>
                                                    </div>
                                                </div>
                                                @foreach ($productCustomization['rows'] ?? [] as $customRow)
                                                    @php
                                                        $rowId = is_object($customRow) ? ($customRow->id ?? null) : ($customRow['id'] ?? null);
                                                        $clKonum = is_object($customRow) ? ($customRow->position_name ?? '') : ($customRow['position_name'] ?? '');
                                                        $defEn = is_object($customRow) ? ($customRow->default_width ?? '') : ($customRow['default_width'] ?? '');
                                                        $defBoy = is_object($customRow) ? ($customRow->default_height ?? '') : ($customRow['default_height'] ?? '');
                                                        $defEn = $defEn !== null && $defEn !== '' ? rtrim(rtrim((string) $defEn, '0'), '.') : '';
                                                        $defBoy = $defBoy !== null && $defBoy !== '' ? rtrim(rtrim((string) $defBoy, '0'), '.') : '';
                                                        $defRenk = (int) (is_object($customRow) ? ($customRow->default_color_count ?? 3) : ($customRow['default_color_count'] ?? 3));
                                                        if ($defRenk < 1 || $defRenk > $customizationMaxColors) {
                                                            $defRenk = min(3, $customizationMaxColors);
                                                        }
                                                        $rowDefaultPrint = (string) (is_object($customRow) ? ($customRow->default_print_technique_slug ?? '') : ($customRow['default_print_technique_slug'] ?? ''));
                                                        if ($rowDefaultPrint === '' || ! isset($customizationPrintOptions[$rowDefaultPrint])) {
                                                            $rowDefaultPrint = $customizationDefaultPrint;
                                                        }
                                                    @endphp
                                                    <label class="customization-row-card flex w-full max-w-full cursor-pointer items-center gap-3 sm:gap-4 has-[input:checked]:[&_.customization-konum-text]:text-primary-800" data-konum="{{ $clKonum }}">
                                                        <input type="checkbox" name="product_customization_row[]" value="{{ $rowId }}" class="peer sr-only customization-row-check" aria-label="{{ __('store.product.customization_row_check_aria') }} — {{ $clKonum }}">
                                                        <span class="pointer-events-none relative flex h-11 w-11 shrink-0 items-center justify-center self-center rounded-full border-2 border-slate-300 bg-gradient-to-b from-white to-slate-50 shadow-inner transition-all duration-200 peer-checked:border-primary-500 peer-checked:bg-gradient-to-br peer-checked:from-primary-500 peer-checked:to-primary-600 peer-checked:shadow-lg peer-checked:shadow-primary-500/40 peer-checked:ring-4 peer-checked:ring-primary-400/30 peer-checked:[&_svg]:opacity-100 peer-checked:[&_svg]:scale-100" aria-hidden="true">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5 text-white opacity-0 transition-all duration-200 scale-75"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        </span>
                                                        <div class="min-w-0 flex-1 rounded-2xl border border-slate-200/90 bg-slate-50/40 p-3.5 shadow-sm transition-all duration-200 hover:border-slate-300 hover:bg-white hover:shadow-md peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary-500 sm:p-4 peer-checked:border-primary-500 peer-checked:bg-gradient-to-br peer-checked:from-primary-50 peer-checked:via-white peer-checked:to-emerald-50/40 peer-checked:shadow-lg peer-checked:shadow-primary-500/10 peer-checked:ring-2 peer-checked:ring-primary-400/25">
                                                            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-11 sm:items-center sm:gap-x-3 sm:gap-y-0">
                                                                <div class="flex min-h-[2.75rem] min-w-0 flex-col justify-center sm:col-span-3">
                                                                    <span class="mb-1 block text-center text-[11px] font-semibold uppercase tracking-wide text-slate-500 sm:hidden">{{ __('store.product.customization_col_position') }}</span>
                                                                    <span class="customization-konum-text text-center text-sm font-semibold leading-snug text-slate-600 transition-colors sm:text-left">{{ $clKonum }}</span>
                                                                </div>
                                                                <div class="flex min-h-[2.75rem] min-w-0 flex-col justify-center sm:col-span-3">
                                                                    <span class="mb-1.5 block text-center text-[11px] font-semibold uppercase tracking-wide text-slate-500 sm:hidden">{{ __('store.product.customization_col_dimensions') }}</span>
                                                                    <div class="flex flex-wrap items-center justify-center gap-2 sm:justify-center">
                                                                        <span class="sr-only">{{ __('store.product.customization_dim_en') }}</span>
                                                                        <input type="number" inputmode="decimal" min="0" step="any" autocomplete="off" value="{{ $defEn }}" data-default="{{ $defEn }}" class="customization-dim-en h-10 w-[4.85rem] shrink-0 rounded-xl border border-slate-300/90 bg-white px-2 text-center text-sm tabular-nums leading-none text-slate-800 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/25" aria-label="{{ __('store.product.customization_dim_en') }}, {{ $clKonum }}">
                                                                        <span class="flex h-10 shrink-0 select-none items-center justify-center text-slate-400" aria-hidden="true">×</span>
                                                                        <span class="sr-only">{{ __('store.product.customization_dim_boy') }}</span>
                                                                        <input type="number" inputmode="decimal" min="0" step="any" autocomplete="off" value="{{ $defBoy }}" data-default="{{ $defBoy }}" class="customization-dim-boy h-10 w-[4.85rem] shrink-0 rounded-xl border border-slate-300/90 bg-white px-2 text-center text-sm tabular-nums leading-none text-slate-800 shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/25" aria-label="{{ __('store.product.customization_dim_boy') }}, {{ $clKonum }}">
                                                                    </div>
                                                                </div>
                                                                <div class="flex min-h-[2.75rem] min-w-0 flex-col justify-center sm:col-span-2">
                                                                    <span class="mb-1.5 block text-center text-[11px] font-semibold uppercase tracking-wide text-slate-500 sm:hidden">{{ __('store.product.customization_col_colors') }}</span>
                                                                    <div class="flex justify-center sm:justify-center">
                                                                        <select name="customization_row_{{ $rowId }}_renk" data-default-renk="{{ $defRenk }}" class="customization-color-count h-10 w-full max-w-[7rem] rounded-xl border border-slate-300/90 bg-white px-3 text-center text-sm text-slate-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/25 sm:max-w-[6.5rem]" aria-label="{{ __('store.product.customization_col_colors') }}, {{ $clKonum }}">
                                                                            @for ($ci = 1; $ci <= $customizationMaxColors; $ci++)
                                                                                <option value="{{ $ci }}" {{ (int) $ci === (int) $defRenk ? 'selected' : '' }}>{{ $ci }}</option>
                                                                            @endfor
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="flex min-h-[2.75rem] min-w-0 flex-col justify-center sm:col-span-3">
                                                                    <span class="mb-1.5 block text-center text-[11px] font-semibold uppercase tracking-wide text-slate-500 sm:hidden">{{ __('store.product.customization_col_print') }}</span>
                                                                    <div class="flex justify-center">
                                                                        <select name="customization_row_{{ $rowId }}_baski" data-default-baski="{{ $rowDefaultPrint }}" class="customization-print-tech h-10 w-full min-w-0 rounded-xl border border-slate-300/90 bg-white px-3 text-sm text-slate-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/25" aria-label="{{ __('store.product.customization_col_print') }}, {{ $clKonum }}">
                                                                            @foreach ($customizationPrintOptions as $pSlug => $pLabel)
                                                                                <option value="{{ $pSlug }}" {{ $pSlug === $rowDefaultPrint ? 'selected' : '' }}>{{ $pLabel }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <label for="product-customization-notes-field" class="block text-sm font-semibold text-slate-800 mb-2">{{ __('store.product.customization_panel_title') }}</label>
                                            <textarea id="product-customization-notes-field" rows="3" maxlength="2000" class="w-full rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20" placeholder="{{ __('store.product.customization_notes_placeholder') }}"></textarea>
                                            <p class="mt-2 text-xs text-slate-500">{{ __('store.product.customization_notes_footer') }}</p>
                                            <button type="button" id="customization-continue-btn" disabled class="mt-4 w-full py-3 sm:py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                                {{ __('store.product.variation_continue') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <div id="variation-image-lightbox" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/90 p-4" aria-modal="true" role="dialog" aria-label="{{ __('store.product.variation_lightbox_aria') }}">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" id="variation-lightbox-backdrop"></div>
                        <button type="button" id="variation-lightbox-close" class="absolute top-4 right-4 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors" aria-label="{{ __('store.product.lightbox_close') }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <img id="variation-lightbox-image" src="" alt="" class="relative z-10 max-w-full max-h-[90vh] w-auto h-auto object-contain rounded-lg shadow-2xl">
                    </div>

                @endif

                @if(!$hasVariations)
                    @if($canSeePrices && $baseTry !== null)
                        <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50/80 px-3.5 py-3">
                            <div class="grid grid-cols-[auto_1fr] items-center gap-x-3 gap-y-1">
                                <span class="text-sm font-semibold text-slate-600">{{ __('store.product.unit_price_label') }}</span>
                                <div class="text-right">
                                    <span class="text-sm font-semibold text-slate-700">
                                        {{ $selectedCurrency->format($baseConverted) }}
                                    </span>
                                </div>
                                <span class="text-sm font-semibold text-slate-600">{{ __('store.product.no_variation_product_total_label') }}</span>
                                <div class="text-right">
                                    <span id="product-price" data-base-try="{{ $baseTry }}" @if($hasProductDiscount && $normalPriceTry !== null) data-normal-try="{{ $normalPriceTry }}" @endif data-exchange-rate="{{ (float) $selectedCurrency->exchange_rate }}" data-currency-code="{{ $selectedCurrency->code }}" data-currency-symbol="{{ $selectedCurrency->symbol }}" class="text-lg font-bold text-slate-900">
                                        {{ $selectedCurrency->format($selectedCurrency->convertFromTRY($baseTry * $minOrderProduct)) }}
                                    </span>
                                    @if($hasProductDiscount)
                                        <div id="product-price-strike" class="mt-0.5 text-right text-xs text-slate-400 line-through">
                                            {{ $selectedCurrency->format($selectedCurrency->convertFromTRY($normalPriceTry * $minOrderProduct)) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    @auth
                    <button type="submit" id="add-to-cart-btn" class="mt-4 lg:mt-5 w-full py-3 sm:py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm sm:text-base font-semibold flex items-center justify-center gap-2 shadow-md shadow-primary-600/20 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        {{ __('store.product.add_to_cart') }}
                    </button>
                    @endauth
                    @guest
                    <button type="button" onclick="document.getElementById('login-modal').classList.remove('hidden')" class="mt-4 lg:mt-5 w-full py-3 sm:py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm sm:text-base font-semibold flex items-center justify-center gap-2 shadow-md shadow-primary-600/20">
                        {{ __('store.product.login_for_order') }}
                    </button>
                    @endguest
                @endif
            </form>

            {{-- Modern uyarı dialog: minimum sipariş vb. --}}
            <div id="product-warning-dialog" class="fixed inset-0 z-[70] hidden items-center justify-center p-4" aria-modal="true" role="alertdialog" aria-labelledby="product-warning-dialog-title" aria-describedby="product-warning-dialog-desc">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" id="product-warning-dialog-backdrop"></div>
                <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200/80 overflow-hidden transform transition-all">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 mx-auto mb-5">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h2 id="product-warning-dialog-title" class="text-xl font-semibold text-slate-900 text-center mb-2">{{ __('store.product.warn_min_title') }}</h2>
                        <p id="product-warning-dialog-desc" class="text-slate-600 text-center text-sm sm:text-base leading-relaxed min-h-[3rem]"></p>
                    </div>
                    <div class="px-6 sm:px-8 pb-6 sm:pb-8">
                        <button type="button" id="product-warning-dialog-close" class="w-full py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            {{ __('store.product.zoom_ok') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @elseif(!$product->isOnSale())
    <section class="mt-5 lg:mt-6 w-full" aria-label="{{ __('store.product.section_order_options') }}">
        <div class="w-full">
            <div class="p-6 rounded-2xl bg-slate-100 border border-slate-200">
                <p class="font-medium text-slate-700">{{ $product->getStatusLabel() }}</p>
                <p class="mt-1 text-sm text-slate-600">
                    @if($productStatus === 'stokta_yok')
                        {{ __('store.product.status_out_stock_detail') }}
                    @else
                        {{ __('store.product.status_coming_detail') }}
                    @endif
                </p>
            </div>
        </div>
    </section>
    @elseif($product->stock_quantity !== null && (int) $product->stock_quantity === 0)
    <section class="mt-5 lg:mt-6 w-full" aria-label="{{ __('store.product.stock_label_short') }}">
        <div class="w-full">
            <div class="p-6 rounded-2xl bg-slate-100 border border-slate-200">
                <p class="text-red-600 font-medium">{{ __('store.product.stock_out_heading') }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ __('store.product.stock_out_body') }}</p>
            </div>
        </div>
    </section>
    @endif

        </div>
    </div>

    @if($hasVariations && $showPurchasePanel)
        @php
            $storeProductUi = [
                'summary_select_prompt' => __('store.product.summary_select_prompt'),
                'variation_continue' => __('store.product.variation_continue'),
                'variation_multi_hint' => __('store.product.variation_multi_hint'),
                'add_to_cart' => __('store.product.add_to_cart'),
                'add_to_cart_hint' => __('store.product.add_to_cart_hint'),
                'summary_total_qty' => __('store.product.summary_total_qty'),
                'qty_row_label' => __('store.product.qty_row_label'),
                'size_row_prefix' => __('store.product.size_row_prefix'),
                'units_suffix' => __('store.product.units_suffix'),
                'warn_min_title' => __('store.product.warn_min_title'),
                'warn_min_desc' => __('store.product.warn_min_desc'),
                'warn_stock_title' => __('store.product.warn_stock_title'),
                'warn_stock_desc_breakdown' => __('store.product.warn_stock_desc_breakdown'),
                'warn_stock_desc_simple' => __('store.product.warn_stock_desc_simple'),
                'customization_summary_empty' => __('store.product.customization_summary_empty'),
                'customization_summary_section_label' => __('store.product.customization_summary_section_label'),
                'customization_colors_unit' => __('store.product.customization_colors_unit'),
                'customization_area_cm2' => __('store.product.customization_area_cm2'),
                'customization_dim_cm_suffix' => __('store.product.customization_dim_cm_suffix'),
                'customization_matched_ebat' => __('store.product.customization_matched_ebat'),
                'customization_summary_dim' => __('store.product.customization_summary_dim'),
                'customization_summary_area' => __('store.product.customization_summary_area'),
                'customization_summary_ebat' => __('store.product.customization_summary_ebat'),
                'customization_summary_colors' => __('store.product.customization_summary_colors'),
                'customization_summary_print' => __('store.product.customization_summary_print'),
                'customization_col_position' => __('store.product.customization_col_position'),
                'customization_summary_multiplier' => __('store.product.customization_summary_multiplier'),
                'customization_size_multiplier_formula' => __('store.product.customization_size_multiplier_formula'),
                'customization_summary_price' => __('store.product.customization_summary_price'),
                'customization_summary_order_qty' => __('store.product.customization_summary_order_qty'),
                'customization_summary_qty_range' => __('store.product.customization_summary_qty_range'),
                'customization_summary_qty_multiplier' => __('store.product.customization_summary_qty_multiplier'),
                'customization_qty_range_fmt' => __('store.product.customization_qty_range_fmt'),
                'customization_qty_not_set' => __('store.product.customization_qty_not_set'),
                'customization_summary_color_multiplier' => __('store.product.customization_summary_color_multiplier'),
                'customization_summary_total_price' => __('store.product.customization_summary_total_price'),
                'customization_total_price_formula' => __('store.product.customization_total_price_formula'),
                'customization_section_grand_total' => __('store.product.customization_section_grand_total'),
            ];
            $sizeEbatMultipliers = $sizeEbatMultipliers ?? [];
            $quantityDimensionMultipliers = $quantityDimensionMultipliers ?? [];
            $colorDimensionMultipliers = $colorDimensionMultipliers ?? [];
            $usdCurrency = ($currencies ?? collect())->firstWhere('code', 'USD');
            $storeCurrencyConfig = [
                'canSeePrices' => (bool) ($canSeePrices ?? false),
                'usdExchangeRate' => $usdCurrency ? (float) $usdCurrency->exchange_rate : null,
                'selectedCode' => $selectedCurrency->code ?? 'TRY',
                'selectedSymbol' => $selectedCurrency->symbol ?? '₺',
                'selectedExchangeRate' => (float) ($selectedCurrency->exchange_rate ?? 1),
                'selectedDecimalPlaces' => (int) ($selectedCurrency->decimal_places ?? 2),
            ];
        @endphp
        <script>window.storeProductUi = @json($storeProductUi);</script>
        <script>window.sizeEbatMultipliers = @json($sizeEbatMultipliers);</script>
        <script>window.quantityDimensionMultipliers = @json($quantityDimensionMultipliers);</script>
        <script>window.colorDimensionMultipliers = @json($colorDimensionMultipliers);</script>
        <script>window.storeCurrencyConfig = @json($storeCurrencyConfig);</script>
        @push('head')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var PU = window.storeProductUi || {};
                    var variationInput = document.getElementById('variation-data-input');
                    if (!variationInput) return;

                    function getVariationStepsMeta() {
                        var wrap = document.getElementById('product-variations');
                        if (!wrap) return { customizationIdx: -1, sizeIdx: -1 };
                        var c = wrap.getAttribute('data-customization-step-index');
                        var s = wrap.getAttribute('data-size-step-index');
                        return {
                            customizationIdx: c !== null && c !== '' ? parseInt(c, 10) : -1,
                            sizeIdx: s !== null && s !== '' ? parseInt(s, 10) : -1
                        };
                    }

                    function computeCustomizationAreaCm2(enStr, boyStr) {
                        var en = parseFloat(String(enStr || '').replace(',', '.'));
                        var boy = parseFloat(String(boyStr || '').replace(',', '.'));
                        if (!isFinite(en) || !isFinite(boy) || en <= 0 || boy <= 0) {
                            return null;
                        }
                        return en * boy;
                    }

                    function formatCustomizationAreaCm2(cm2) {
                        if (cm2 === null || !isFinite(cm2)) {
                            return null;
                        }
                        var isWhole = Math.abs(cm2 - Math.round(cm2)) < 1e-9;
                        return cm2.toLocaleString('tr-TR', {
                            minimumFractionDigits: isWhole ? 0 : 2,
                            maximumFractionDigits: isWhole ? 0 : 2
                        });
                    }

                    function customizationAreaCm2Label(areaDisplay) {
                        if (!areaDisplay) {
                            return '';
                        }
                        var tpl = (PU.customization_area_cm2 || ':area cm²');
                        return tpl.replace(':area', areaDisplay);
                    }

                    function findEbatRowForAreaCm2(cm2) {
                        var list = window.sizeEbatMultipliers || [];
                        if (!list.length || cm2 === null || !isFinite(cm2) || cm2 <= 0) {
                            return null;
                        }
                        var sorted = list.slice().sort(function(a, b) {
                            return parseFloat(a.ebat_cm2) - parseFloat(b.ebat_cm2);
                        });
                        for (var i = 0; i < sorted.length; i++) {
                            if (parseFloat(sorted[i].ebat_cm2) >= cm2 - 1e-9) {
                                return sorted[i];
                            }
                        }
                        return sorted[sorted.length - 1] || null;
                    }

                    function findEbatLabelForAreaCm2(cm2) {
                        var row = findEbatRowForAreaCm2(cm2);
                        return row ? (row.size_label || null) : null;
                    }

                    function parseMultiplierNumber(raw) {
                        if (raw === null || raw === undefined || raw === '') {
                            return null;
                        }
                        var s = String(raw).trim().replace(',', '.');
                        if (!/^-?\d+(\.\d+)?$/.test(s)) {
                            return null;
                        }
                        var n = parseFloat(s);
                        return isFinite(n) ? n : null;
                    }

                    function formatMultiplierNumber(n) {
                        if (n === null || !isFinite(n)) {
                            return '—';
                        }
                        var isWhole = Math.abs(n - Math.round(n)) < 1e-9;
                        return n.toLocaleString('tr-TR', {
                            minimumFractionDigits: isWhole ? 0 : 2,
                            maximumFractionDigits: isWhole ? 0 : 4
                        });
                    }

                    function computeEbatMultiplierResult(ebatRow) {
                        if (!ebatRow) {
                            return { display: null, fixed: null, extra: null, product: null, fixedLabel: null };
                        }
                        var extra = parseMultiplierNumber(ebatRow.extra_multiplier);
                        if (extra === null) {
                            extra = 0;
                        }
                        var fixedRaw = ebatRow.fixed_multiplier;
                        var fixedNum = parseMultiplierNumber(fixedRaw);
                        var fixedLabel = fixedNum === null && fixedRaw ? String(fixedRaw).trim() : null;
                        if (fixedNum !== null) {
                            var product = fixedNum * extra;
                            return {
                                fixed: fixedNum,
                                extra: extra,
                                product: product,
                                fixedLabel: null,
                                display: formatMultiplierResultDisplay({
                                    fixed: fixedNum,
                                    extra: extra,
                                    product: product,
                                    fixedLabel: null
                                })
                            };
                        }
                        if (fixedLabel) {
                            return {
                                fixed: null,
                                extra: extra,
                                product: null,
                                fixedLabel: fixedLabel,
                                display: fixedLabel
                            };
                        }
                        return { display: null, fixed: null, extra: extra, product: null, fixedLabel: null };
                    }

                    function formatMultiplierResultDisplay(result) {
                        if (!result) {
                            return '';
                        }
                        if (result.fixed !== null && result.product !== null) {
                            var tpl = (PU.customization_size_multiplier_formula || ':fixed × :extra = :result');
                            return tpl
                                .replace(':fixed', formatMultiplierNumber(result.fixed))
                                .replace(':extra', formatMultiplierNumber(result.extra))
                                .replace(':result', formatMultiplierNumber(result.product));
                        }
                        return result.fixedLabel || result.display || '';
                    }

                    function getUsdExchangeRate() {
                        var cfg = window.storeCurrencyConfig || {};
                        var rate = parseFloat(cfg.usdExchangeRate);
                        return isFinite(rate) && rate > 0 ? rate : null;
                    }

                    function computeCustomizationPrintPriceTry(multiplierProduct) {
                        if (multiplierProduct === null || !isFinite(multiplierProduct)) {
                            return null;
                        }
                        var usdRate = getUsdExchangeRate();
                        if (!usdRate) {
                            return null;
                        }
                        return multiplierProduct * usdRate;
                    }

                    function formatStoreCurrencyAmount(tryAmount) {
                        if (tryAmount === null || !isFinite(tryAmount)) {
                            return null;
                        }
                        var cfg = window.storeCurrencyConfig || {};
                        var code = cfg.selectedCode || 'TRY';
                        var symbol = cfg.selectedSymbol || '₺';
                        var rate = parseFloat(cfg.selectedExchangeRate) || 1;
                        var decimals = parseInt(cfg.selectedDecimalPlaces, 10);
                        if (!isFinite(decimals)) {
                            decimals = 2;
                        }
                        var amount = code === 'TRY' ? tryAmount : tryAmount / rate;
                        if (code === 'TRY') {
                            return new Intl.NumberFormat('tr-TR', {
                                minimumFractionDigits: decimals,
                                maximumFractionDigits: decimals
                            }).format(amount) + ' ' + symbol;
                        }
                        return symbol + new Intl.NumberFormat('en-US', {
                            minimumFractionDigits: decimals,
                            maximumFractionDigits: decimals
                        }).format(amount);
                    }

                    function computeCustomizationPrintPriceDisplay(multiplierProduct) {
                        var cfg = window.storeCurrencyConfig || {};
                        if (!cfg.canSeePrices) {
                            return null;
                        }
                        var tryAmount = computeCustomizationPrintPriceTry(multiplierProduct);
                        return formatStoreCurrencyAmount(tryAmount);
                    }

                    function computeCustomizationRowTotalPrice(row, qtyCtx) {
                        var cfg = window.storeCurrencyConfig || {};
                        if (!cfg.canSeePrices) {
                            return { total_try: null, total_display: null, formula_display: null };
                        }
                        var baseTry = row.size_multiplier_price_try;
                        if (baseTry === null || !isFinite(baseTry)) {
                            return { total_try: null, total_display: null, formula_display: null };
                        }
                        var qtyMult = qtyCtx && qtyCtx.quantity_multiplier_price != null && isFinite(qtyCtx.quantity_multiplier_price)
                            ? qtyCtx.quantity_multiplier_price
                            : null;
                        var colorMult = row.color_multiplier_price != null && isFinite(row.color_multiplier_price)
                            ? row.color_multiplier_price
                            : null;
                        if (qtyMult === null || colorMult === null) {
                            return { total_try: null, total_display: null, formula_display: null };
                        }
                        var totalTry = baseTry * qtyMult * colorMult;
                        var baseDisp = row.size_multiplier_price_display || formatStoreCurrencyAmount(baseTry) || '—';
                        var qtyDisp = qtyCtx.quantity_multiplier_price_display || formatDimensionMultiplierPrice(qtyMult);
                        var colorDisp = row.color_multiplier_price_display || formatDimensionMultiplierPrice(colorMult);
                        var totalDisp = formatStoreCurrencyAmount(totalTry) || '—';
                        var tpl = (PU.customization_total_price_formula || ':base × :qty × :color = :total');
                        var formula = tpl
                            .replace(':base', baseDisp)
                            .replace(':qty', qtyDisp)
                            .replace(':color', colorDisp)
                            .replace(':total', totalDisp);
                        return {
                            total_try: totalTry,
                            total_display: totalDisp,
                            formula_display: formula
                        };
                    }

                    function enrichCustomizationRowsWithTotals(rows, qtyCtx) {
                        return (rows || []).map(function(row) {
                            var enriched = Object.assign({}, row);
                            var totals = computeCustomizationRowTotalPrice(enriched, qtyCtx);
                            enriched.total_price_try = totals.total_try;
                            enriched.total_price_display = totals.total_display;
                            enriched.total_price_formula_display = totals.formula_display;
                            return enriched;
                        });
                    }

                    function customizationMatchedEbatLabel(ebat) {
                        if (!ebat) {
                            return '';
                        }
                        var tpl = (PU.customization_matched_ebat || 'EBAT: :ebat');
                        return tpl.replace(':ebat', ebat);
                    }

                    function customizationRowPayloadFromCard(row, cb) {
                        if (!row) return null;
                        var enInp = row.querySelector('.customization-dim-en');
                        var boyInp = row.querySelector('.customization-dim-boy');
                        var renkSel = row.querySelector('.customization-color-count');
                        var printSel = row.querySelector('.customization-print-tech');
                        var cmSuffix = (PU.customization_dim_cm_suffix || 'cm').trim();
                        var en = enInp ? String(enInp.value || '').trim().replace(',', '.') : '';
                        var boy = boyInp ? String(boyInp.value || '').trim().replace(',', '.') : '';
                        var enDisp = en !== '' ? en : '—';
                        var boyDisp = boy !== '' ? boy : '—';
                        var en_boy_cm = enDisp + ' × ' + boyDisp + (cmSuffix ? ' ' + cmSuffix : '');
                        var alanCm2 = computeCustomizationAreaCm2(en, boy);
                        var alanCm2Display = formatCustomizationAreaCm2(alanCm2);
                        var ebatRow = findEbatRowForAreaCm2(alanCm2);
                        var ebat = ebatRow ? (ebatRow.size_label || null) : null;
                        var multiplier = computeEbatMultiplierResult(ebatRow);
                        var renk_sayisi = renkSel ? String(renkSel.value || '').trim() : '';
                        var colorMultRow = findColorMultiplierRowForCount(parseInt(renk_sayisi, 10));
                        var baski_slug = printSel ? String(printSel.value || '').trim() : '';
                        var baski_teknigi = '';
                        if (printSel && printSel.selectedIndex >= 0 && printSel.options[printSel.selectedIndex]) {
                            baski_teknigi = String(printSel.options[printSel.selectedIndex].textContent || '').trim();
                        }
                        return {
                            row_id: String(cb && cb.value ? cb.value : ''),
                            konum: row.getAttribute('data-konum') || '',
                            en_boy_cm: en_boy_cm,
                            en_cm: en,
                            boy_cm: boy,
                            alan_cm2: alanCm2,
                            alan_cm2_display: alanCm2Display,
                            ebat: ebat,
                            size_fixed_multiplier: ebatRow ? ebatRow.fixed_multiplier : null,
                            size_extra_multiplier: ebatRow ? ebatRow.extra_multiplier : null,
                            size_multiplier_product: multiplier.product,
                            size_multiplier_display: multiplier.display,
                            size_multiplier_price_try: computeCustomizationPrintPriceTry(multiplier.product),
                            size_multiplier_price_display: computeCustomizationPrintPriceDisplay(multiplier.product),
                            renk_sayisi: renk_sayisi,
                            color_multiplier_price: colorMultRow ? parseFloat(colorMultRow.multiplier_price) : null,
                            color_multiplier_price_display: colorMultRow ? formatDimensionMultiplierPrice(parseFloat(colorMultRow.multiplier_price)) : null,
                            baski_slug: baski_slug,
                            baski_teknigi: baski_teknigi
                        };
                    }

                    function customizationRowDetailLine(row) {
                        var detail = (row.en_boy_cm != null && row.en_boy_cm !== '') ? String(row.en_boy_cm) : '—';
                        if (row.alan_cm2_display) {
                            detail += ' · ' + customizationAreaCm2Label(row.alan_cm2_display);
                        }
                        if (row.ebat) {
                            detail += ' · ' + customizationMatchedEbatLabel(row.ebat);
                        }
                        if (row.size_multiplier_display) {
                            detail += ' · ' + (PU.customization_summary_multiplier || 'Çarpan') + ': ' + row.size_multiplier_display;
                        }
                        if (row.size_multiplier_price_display) {
                            detail += ' · ' + (PU.customization_summary_price || 'Fiyat') + ': ' + row.size_multiplier_price_display;
                        }
                        if (row.color_multiplier_price_display) {
                            detail += ' · ' + (PU.customization_summary_color_multiplier || 'Renk çarpanı') + ': ' + row.color_multiplier_price_display;
                        }
                        if (row.total_price_display) {
                            detail += ' · ' + (PU.customization_summary_total_price || 'Toplam fiyat') + ': ' + row.total_price_display;
                        }
                        var colUnit = (PU.customization_colors_unit || '').trim();
                        var renkPart = row.renk_sayisi && colUnit ? (String(row.renk_sayisi) + ' ' + colUnit) : (row.renk_sayisi || '');
                        if (renkPart) {
                            detail += ' · ' + renkPart;
                        }
                        detail += ' · ' + (row.baski_teknigi || '');
                        return detail;
                    }

                    function customizationSummaryMetric(label, value) {
                        return '<div class="customization-summary-metric min-w-0 px-3 py-2.5 sm:px-3.5 sm:py-3">' +
                            '<span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500">' + escapeHtml(label) + '</span>' +
                            '<span class="mt-1 block text-sm font-semibold leading-snug text-slate-900" title="' + escapeHtml(value || '—') + '">' + escapeHtml(value || '—') + '</span>' +
                            '</div>';
                    }

                    function customizationSummaryMetricPrice(label, priceText) {
                        return '<div class="customization-summary-metric min-w-0 bg-emerald-50/50 px-3 py-2.5 sm:px-3.5 sm:py-3">' +
                            '<span class="block text-[10px] font-semibold uppercase tracking-wider text-emerald-700/80">' + escapeHtml(label) + '</span>' +
                            '<span class="mt-1 block text-base font-bold leading-snug text-emerald-800" title="' + escapeHtml(priceText || '—') + '">' + escapeHtml(priceText || '—') + '</span>' +
                            '</div>';
                    }

                    function renderCustomizationSummaryCardHtml(row, index) {
                        var dimLbl = PU.customization_summary_dim || 'Ölçü';
                        var areaLbl = PU.customization_summary_area || 'Alan';
                        var ebatLbl = PU.customization_summary_ebat || 'EBAT';
                        var multLbl = PU.customization_summary_multiplier || 'Çarpan';
                        var priceLbl = PU.customization_summary_price || 'Fiyat';
                        var colorsLbl = PU.customization_summary_colors || 'Renk';
                        var colorMultLbl = PU.customization_summary_color_multiplier || 'Renk çarpanı';
                        var cfg = window.storeCurrencyConfig || {};
                        var showPrice = !!(cfg.canSeePrices && row.size_multiplier_price_display);
                        var dim = (row.en_boy_cm != null && row.en_boy_cm !== '') ? String(row.en_boy_cm) : '—';
                        var area = row.alan_cm2_display ? customizationAreaCm2Label(row.alan_cm2_display) : '—';
                        var ebat = row.ebat ? String(row.ebat) : '—';
                        var mult = row.size_multiplier_display ? String(row.size_multiplier_display) : '—';
                        var price = row.size_multiplier_price_display ? String(row.size_multiplier_price_display) : '—';
                        var colUnit = (PU.customization_colors_unit || '').trim();
                        var renk = row.renk_sayisi && colUnit ? (String(row.renk_sayisi) + ' ' + colUnit) : (row.renk_sayisi || '—');
                        var colorMult = row.color_multiplier_price_display ? String(row.color_multiplier_price_display) : '—';
                        var print = row.baski_teknigi || '—';
                        var position = row.konum || '—';
                        var rowNum = typeof index === 'number' ? (index + 1) : '';
                        var positionLbl = PU.customization_col_position || 'Konum';
                        var totalLbl = PU.customization_summary_total_price || 'Toplam fiyat';
                        var gridCols = showPrice ? 'sm:grid-cols-7' : 'sm:grid-cols-6';
                        var totalFooter = '';
                        if (cfg.canSeePrices && row.total_price_display) {
                            totalFooter = '<footer class="border-t border-primary-100 bg-gradient-to-r from-primary-50/80 to-emerald-50/50 px-3.5 py-3 sm:px-4">' +
                                '<div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">' +
                                (row.total_price_formula_display
                                    ? '<p class="text-xs leading-snug text-slate-600">' + escapeHtml(row.total_price_formula_display) + '</p>'
                                    : '<span></span>') +
                                '<p class="shrink-0 text-right">' +
                                '<span class="block text-[10px] font-semibold uppercase tracking-wider text-primary-700 sm:inline">' + escapeHtml(totalLbl) + '</span>' +
                                '<span class="mt-0.5 block text-xl font-bold text-primary-900 sm:mt-0 sm:ml-2 sm:inline">' + escapeHtml(row.total_price_display) + '</span>' +
                                '</p></div></footer>';
                        }

                        return '<article class="customization-summary-card overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm ring-1 ring-slate-900/[0.03]">' +
                            '<header class="flex items-start justify-between gap-3 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-3.5 py-3 sm:px-4">' +
                            '<div class="flex min-w-0 items-center gap-3">' +
                            '<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-600/10 text-xs font-bold text-primary-700">' + escapeHtml(String(rowNum)) + '</span>' +
                            '<div class="min-w-0">' +
                            '<p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">' + escapeHtml(positionLbl) + '</p>' +
                            '<p class="mt-0.5 truncate text-sm font-semibold text-slate-900 sm:text-base" title="' + escapeHtml(position) + '">' + escapeHtml(position) + '</p>' +
                            '</div></div>' +
                            '<span class="shrink-0 rounded-full border border-primary-200/80 bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-800">' + escapeHtml(print) + '</span>' +
                            '</header>' +
                            '<div class="grid grid-cols-2 divide-x divide-y divide-slate-100 ' + gridCols + ' sm:divide-y-0">' +
                            customizationSummaryMetric(dimLbl, dim) +
                            customizationSummaryMetric(areaLbl, area) +
                            customizationSummaryMetric(ebatLbl, ebat) +
                            customizationSummaryMetric(multLbl, mult) +
                            (showPrice ? customizationSummaryMetricPrice(priceLbl, price) : '') +
                            customizationSummaryMetric(colorsLbl, renk) +
                            customizationSummaryMetricColorMultiplier(colorMultLbl, colorMult) +
                            '</div>' +
                            totalFooter +
                            '</article>';
                    }

                    function customizationSummaryMetricColorMultiplier(label, value) {
                        return '<div class="customization-summary-metric min-w-0 bg-violet-50/40 px-3 py-2.5 sm:px-3.5 sm:py-3">' +
                            '<span class="block text-[10px] font-semibold uppercase tracking-wider text-violet-700/80">' + escapeHtml(label) + '</span>' +
                            '<span class="mt-1 block text-sm font-bold leading-snug text-violet-900" title="' + escapeHtml(value || '—') + '">' + escapeHtml(value || '—') + '</span>' +
                            '</div>';
                    }

                    function formatDimensionMultiplierPrice(price) {
                        if (price === null || !isFinite(price)) {
                            return '—';
                        }
                        var isWhole = Math.abs(price - Math.round(price)) < 1e-9;
                        return price.toLocaleString('tr-TR', {
                            minimumFractionDigits: isWhole ? 0 : 2,
                            maximumFractionDigits: isWhole ? 0 : 4
                        });
                    }

                    function findColorMultiplierRowForCount(colorCount) {
                        var list = window.colorDimensionMultipliers || [];
                        if (!list.length || colorCount === null || !isFinite(colorCount) || colorCount <= 0) {
                            return null;
                        }
                        var c = Math.floor(colorCount);
                        for (var i = 0; i < list.length; i++) {
                            if (parseInt(list[i].color_count, 10) === c) {
                                return list[i];
                            }
                        }
                        return null;
                    }

                    function findQuantityMultiplierRowForQty(qty) {
                        var list = window.quantityDimensionMultipliers || [];
                        if (!list.length || qty === null || !isFinite(qty) || qty <= 0) {
                            return null;
                        }
                        var q = Math.floor(qty);
                        for (var i = 0; i < list.length; i++) {
                            var row = list[i];
                            var from = parseInt(row.quantity_from, 10);
                            var to = parseInt(row.quantity_to, 10);
                            if (q >= from && q <= to) {
                                return row;
                            }
                        }
                        var fallback = null;
                        for (var j = 0; j < list.length; j++) {
                            var r = list[j];
                            if (q >= parseInt(r.quantity_from, 10)) {
                                fallback = r;
                            }
                        }
                        return fallback;
                    }

                    function formatQuantityRangeLabel(row) {
                        if (!row) return '—';
                        var tpl = (PU.customization_qty_range_fmt || ':from - :to');
                        return tpl
                            .replace(':from', String(row.quantity_from))
                            .replace(':to', String(row.quantity_to));
                    }

                    function formatQuantityMultiplierPrice(price) {
                        return formatDimensionMultiplierPrice(price);
                    }

                    function getOrderQuantityContext() {
                        var qty = 0;
                        if (typeof getSizeQuantities === 'function') {
                            var info = getSizeQuantities();
                            qty = info && isFinite(info.total) ? info.total : 0;
                        }
                        var matched = findQuantityMultiplierRowForQty(qty);
                        return {
                            order_quantity: qty,
                            quantity_range_label: matched ? formatQuantityRangeLabel(matched) : null,
                            quantity_multiplier_price: matched ? parseFloat(matched.multiplier_price) : null,
                            quantity_multiplier_price_display: matched ? formatQuantityMultiplierPrice(parseFloat(matched.multiplier_price)) : null
                        };
                    }

                    function renderQuantityMultiplierSummaryHtml() {
                        var ctx = getOrderQuantityContext();
                        var qtyLbl = PU.customization_summary_order_qty || 'Sipariş adeti';
                        var rangeLbl = PU.customization_summary_qty_range || 'Adet aralığı';
                        var multLbl = PU.customization_summary_qty_multiplier || 'Çarpan fiyatı';
                        var qtyVal = ctx.order_quantity > 0
                            ? (ctx.order_quantity.toLocaleString('tr-TR') + ' ' + (PU.units_suffix || 'adet'))
                            : (PU.customization_qty_not_set || '—');
                        var rangeVal = ctx.quantity_range_label || '—';
                        var multVal = ctx.quantity_multiplier_price_display || '—';
                        var metric = function(label, value, accent) {
                            return '<div class="min-w-0 rounded-lg border border-slate-200/90 bg-white px-3 py-2.5 shadow-sm">' +
                                '<p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">' + escapeHtml(label) + '</p>' +
                                '<p class="mt-0.5 text-sm font-semibold leading-snug ' + (accent || 'text-slate-900') + ' break-words">' + escapeHtml(value) + '</p>' +
                                '</div>';
                        };
                        return '<div class="quantity-multiplier-summary border-b border-slate-200/80 bg-slate-50/60 px-3.5 py-3 sm:px-4">' +
                            '<div class="grid grid-cols-1 gap-2 sm:grid-cols-3">' +
                            metric(qtyLbl, qtyVal) +
                            metric(rangeLbl, rangeVal) +
                            metric(multLbl, multVal, 'text-emerald-800') +
                            '</div></div>';
                    }

                    function renderCustomizationGrandTotalHtml(rows) {
                        var cfg = window.storeCurrencyConfig || {};
                        if (!cfg.canSeePrices || !rows || !rows.length) {
                            return '';
                        }
                        var sumTry = 0;
                        var hasAny = false;
                        rows.forEach(function(r) {
                            if (r.total_price_try != null && isFinite(r.total_price_try)) {
                                sumTry += r.total_price_try;
                                hasAny = true;
                            }
                        });
                        if (!hasAny) {
                            return '';
                        }
                        var label = PU.customization_section_grand_total || 'Baskı toplamı';
                        var display = formatStoreCurrencyAmount(sumTry) || '—';
                        return '<div class="border-t border-slate-200/90 px-3.5 py-3 sm:px-4">' +
                            '<div class="flex items-center justify-between gap-3 rounded-xl border border-primary-200/80 bg-gradient-to-r from-primary-50 to-emerald-50/70 px-4 py-3.5 shadow-sm">' +
                            '<span class="text-sm font-semibold text-slate-800">' + escapeHtml(label) + '</span>' +
                            '<span class="text-xl font-bold text-primary-900">' + escapeHtml(display) + '</span>' +
                            '</div></div>';
                    }

                    function renderCustomizationSummarySectionHtml(rows) {
                        if (!rows || !rows.length) return '';
                        var sec = (PU.customization_summary_section_label || '').trim();
                        var qtyCtx = getOrderQuantityContext();
                        var enrichedRows = enrichCustomizationRowsWithTotals(rows, qtyCtx);
                        var cards = enrichedRows.map(function(r, i) { return renderCustomizationSummaryCardHtml(r, i); }).join('');
                        var qtySummary = renderQuantityMultiplierSummaryHtml();
                        var grandTotal = renderCustomizationGrandTotalHtml(enrichedRows);

                        return '<div class="customization-summary-section border-t border-slate-200/90 bg-slate-50/30">' +
                            (sec ? '<div class="flex items-center gap-2.5 border-b border-slate-200/80 bg-white/90 px-3.5 py-3 sm:px-4">' +
                            '<span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-600 text-white shadow-sm">' +
                            '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>' +
                            '</span>' +
                            '<p class="text-sm font-semibold text-slate-900">' + escapeHtml(sec) + '</p></div>' : '') +
                            qtySummary +
                            '<div class="space-y-2.5 p-3 sm:p-4">' + cards + '</div>' +
                            grandTotal +
                            '</div>';
                    }

                    function getCustomizationTablePayload() {
                        var wrap = document.getElementById('product-customization-table');
                        if (!wrap) return null;
                        var checked = wrap.querySelectorAll('input.customization-row-check:checked');
                        if (!checked.length) return null;
                        var rows = [];
                        checked.forEach(function(cb) {
                            var row = cb.closest('.customization-row-card');
                            var payload = customizationRowPayloadFromCard(row, cb);
                            if (payload) rows.push(payload);
                        });
                        var qtyCtx = getOrderQuantityContext();
                        var enrichedRows = enrichCustomizationRowsWithTotals(rows, qtyCtx);
                        return enrichedRows.length ? Object.assign({ rows: enrichedRows }, qtyCtx) : null;
                    }

                    function customizationSummaryLineFromInputs() {
                        var p = getCustomizationTablePayload();
                        var ta = document.getElementById('product-customization-notes-field');
                        var txt = ta ? String(ta.value || '').trim() : '';
                        var parts = [];
                        var unit = (PU.customization_colors_unit || '').trim();
                        if (p && p.rows && p.rows.length) {
                            p.rows.forEach(function(row) {
                                parts.push(row.konum + ' · ' + customizationRowDetailLine(row));
                            });
                        }
                        if (txt) parts.push(txt);
                        return parts.length ? parts.join(' — ') : (PU.customization_summary_empty || '—');
                    }

                    function updateCustomizationContinueEnabled() {
                        var btn = document.getElementById('customization-continue-btn');
                        if (!btn) return;
                        var ok = document.querySelectorAll('#product-customization-table input.customization-row-check:checked').length > 0;
                        btn.disabled = !ok;
                    }

                    function resetCustomizationTableFields() {
                        var tbl = document.getElementById('product-customization-table');
                        if (!tbl) return;
                        tbl.querySelectorAll('.customization-row-card').forEach(function(card) {
                            var en = card.querySelector('.customization-dim-en');
                            var boy = card.querySelector('.customization-dim-boy');
                            var sel = card.querySelector('.customization-color-count');
                            var printSel = card.querySelector('.customization-print-tech');
                            if (en) {
                                var de = en.getAttribute('data-default');
                                if (de !== null) en.value = de;
                            }
                            if (boy) {
                                var db = boy.getAttribute('data-default');
                                if (db !== null) boy.value = db;
                            }
                            if (sel) {
                                var dr = sel.getAttribute('data-default-renk');
                                if (dr !== null && dr !== '') sel.value = dr;
                            }
                            if (printSel) {
                                var dp = printSel.getAttribute('data-default-baski');
                                if (dp !== null && dp !== '') printSel.value = dp;
                            }
                        });
                    }

                    function syncVariationJsonFromSelections() {
                        if (!variationInput) return;
                        var cust = null;
                        var custNotes = undefined;
                        var custTable = undefined;
                        try {
                            var prev = JSON.parse(variationInput.value || '{}');
                            if (prev && typeof prev === 'object' && !Array.isArray(prev)) {
                                if (prev.product_customization) cust = prev.product_customization;
                                if (prev.product_customization_notes !== undefined) custNotes = prev.product_customization_notes;
                                if (prev.product_customization_table !== undefined) custTable = prev.product_customization_table;
                            }
                        } catch (e) {}
                        var fresh = getSelectedOptions();
                        if (cust) fresh.product_customization = cust;
                        if (custNotes !== undefined) fresh.product_customization_notes = custNotes;
                        if (custTable !== undefined) fresh.product_customization_table = custTable;
                        variationInput.value = JSON.stringify(fresh);
                    }

                    function applyCustomizationChoiceToVariationInput() {
                        if (!variationInput) return;
                        var vd = {};
                        try {
                            vd = JSON.parse(variationInput.value || '{}');
                            if (typeof vd !== 'object' || vd === null || Array.isArray(vd)) vd = {};
                        } catch (e) {
                            vd = {};
                        }
                        var sel = getSelectedOptions();
                        Object.keys(sel).forEach(function(k) {
                            vd[k] = sel[k];
                        });
                        delete vd.product_customization;
                        delete vd.product_customization_notes;
                        delete vd.product_customization_table;
                        var custPanel = document.querySelector('[data-customization-panel="1"]');
                        if (custPanel && (custPanel.getAttribute('data-customization-confirmed') || '') === '1') {
                            vd.product_customization = 'completed';
                            var ta = document.getElementById('product-customization-notes-field');
                            vd.product_customization_notes = ta ? String(ta.value || '').trim() : '';
                            var tp = getCustomizationTablePayload();
                            if (tp) vd.product_customization_table = tp;
                        }
                        variationInput.value = JSON.stringify(vd);
                    }

                    function resetProductCustomizationUi() {
                        var sizePanel = document.querySelector('[data-size-step-panel="1"]');
                        if (sizePanel) sizePanel.setAttribute('data-size-step-confirmed', '0');
                        var custPanel = document.querySelector('[data-customization-panel="1"]');
                        var ta = document.getElementById('product-customization-notes-field');
                        if (custPanel) {
                            custPanel.setAttribute('data-customization-confirmed', '0');
                            var sum = custPanel.querySelector('.variation-step-summary');
                            var sumVal = custPanel.querySelector('.variation-step-summary-value');
                            if (sum) {
                                sum.classList.add('hidden');
                                sum.classList.remove('flex');
                            }
                            if (sumVal) sumVal.textContent = '—';
                            var full = custPanel.querySelector('.customization-step-full');
                            if (full) full.classList.add('hidden');
                        }
                        if (ta) ta.value = '';
                        var custTbl = document.getElementById('product-customization-table');
                        if (custTbl) custTbl.querySelectorAll('.customization-row-check').forEach(function(cb) { cb.checked = false; });
                        resetCustomizationTableFields();
                        updateCustomizationContinueEnabled();
                        if (variationInput) {
                            variationInput.value = JSON.stringify(getSelectedOptions());
                        }
                    }

                    var priceEl = document.getElementById('product-price');
                    var baseTry = priceEl ? parseFloat(priceEl.getAttribute('data-base-try')) || 0 : 0;
                    var rate = priceEl ? parseFloat(priceEl.getAttribute('data-exchange-rate')) || 1 : 1;
                    var symbol = priceEl ? priceEl.getAttribute('data-currency-symbol') || '₺' : '₺';
                    var code = priceEl ? priceEl.getAttribute('data-currency-code') || 'TRY' : 'TRY';

                    function formatPrice(num) {
                        if (code === 'TRY') return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num) + ' ' + symbol;
                        return symbol + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
                    }

                    /** data-price-delta: DB’deki price_delta = çarpan; ≤0 veya geçersiz = 1 */
                    function variationMultiplierFromAttr(el) {
                        if (!el) return 1;
                        var m = parseFloat(el.getAttribute('data-price-delta'));
                        if (!isFinite(m) || m <= 0) return 1;
                        return m;
                    }

                    function getCombinedVariationMultiplier() {
                        var product = 1;
                        document.querySelectorAll('.variation-step-panel[data-variation-name]').forEach(function(panel) {
                            if (panel.style.display === 'none') return;
                            var isMulti = (panel.getAttribute('data-allows-multiple') || '') === '1';
                            if (isMulti) {
                                panel.querySelectorAll('.product-option.option-selected').forEach(function(sel) {
                                    if (sel.style.display === 'none') return;
                                    product *= variationMultiplierFromAttr(sel);
                                });
                            } else {
                                var sel = panel.querySelector('.product-option.option-selected');
                                if (sel && sel.style.display !== 'none') {
                                    product *= variationMultiplierFromAttr(sel);
                                }
                            }
                        });
                        return product;
                    }

                    function countVisibleSelectedOptions(panel) {
                        var n = 0;
                        panel.querySelectorAll('.product-option.option-selected').forEach(function(b) {
                            if (b.style.display !== 'none') n++;
                        });
                        return n;
                    }

                    function getSelectedOptions() {
                        var list = getSelectedOptionsInStepOrder();
                        var selected = {};
                        list.forEach(function(item) {
                            if (item.isMulti && item.values && item.values.length) {
                                selected[item.name] = item.values;
                            } else {
                                selected[item.name] = item.value;
                            }
                        });
                        return selected;
                    }

                    /** Seçilen varyasyonları adım sırasına göre döndürür; özet satırından da okur (gizli paneller dahil). */
                    function getSelectedOptionsInStepOrder() {
                        var list = [];
                        var panels = Array.from(document.querySelectorAll('.variation-step-panel[data-variation-name]')).sort(function(a, b) {
                            return (parseInt(a.getAttribute('data-step-index'), 10) || 0) - (parseInt(b.getAttribute('data-step-index'), 10) || 0);
                        });
                        panels.forEach(function(panel) {
                            if (panel.style.display === 'none') return;
                            var name = (panel.getAttribute('data-variation-name') || '').trim();
                            if (!name) return;
                            var isMulti = (panel.getAttribute('data-allows-multiple') || '') === '1';
                            var confirmed = (panel.getAttribute('data-multi-confirmed') || '') === '1';
                            var summary = panel.querySelector('.variation-step-summary');
                            var summaryVal = panel.querySelector('.variation-step-summary-value');
                            if (isMulti) {
                                if (!confirmed) return;
                                var values = [];
                                var delta = 1;
                                panel.querySelectorAll('.product-option.option-selected').forEach(function(sel) {
                                    if (sel.style.display === 'none') return;
                                    values.push((sel.getAttribute('data-option') || '').trim());
                                    delta *= variationMultiplierFromAttr(sel);
                                });
                                var displayVal = summary && summaryVal && !summary.classList.contains('hidden')
                                    ? (summaryVal.textContent || '').trim()
                                    : values.join(', ');
                                if (values.length && displayVal && displayVal !== '—') {
                                    list.push({ name: name, value: displayVal, values: values, priceDelta: delta, isMulti: true });
                                }
                            } else {
                                var value = '';
                                var delta = 1;
                                var sel = panel.querySelector('.product-option.option-selected');
                                if (sel && sel.style.display !== 'none') {
                                    delta = variationMultiplierFromAttr(sel);
                                }
                                if (summary && summaryVal && !summary.classList.contains('hidden')) {
                                    value = (summaryVal.textContent || '').trim();
                                } else if (sel && sel.style.display !== 'none') {
                                    value = (sel.getAttribute('data-option') || '').trim();
                                }
                                if (value && value !== '—') list.push({ name: name, value: value, priceDelta: delta, isMulti: false });
                            }
                        });
                        return list;
                    }

                    function updatePriceAndInput() {
                        if (variationInput) {
                            syncVariationJsonFromSelections();
                        }
                        if (!priceEl) return;
                        var mult = getCombinedVariationMultiplier();
                        var unitTry = baseTry * mult;
                        var sizeInfo = getSizeQuantities();
                        var qty = Math.max(0, parseInt(sizeInfo.total, 10) || 0);
                        var pricingWeight = typeof sizeInfo.pricingWeight === 'number' && !isNaN(sizeInfo.pricingWeight)
                            ? Math.max(0, sizeInfo.pricingWeight)
                            : qty;
                        var lineTry = unitTry * pricingWeight;
                        var baseConverted = code === 'TRY' ? baseTry : baseTry * rate;
                        var lineConverted = code === 'TRY' ? lineTry : lineTry * rate;
                        priceEl.textContent = formatPrice(lineConverted);
                        var basePriceEl = document.getElementById('product-base-price');
                        if (basePriceEl) {
                            basePriceEl.textContent = formatPrice(baseConverted);
                        }
                        var strikeEl = document.getElementById('product-price-strike');
                        if (strikeEl) {
                            var normalAttr = priceEl.getAttribute('data-normal-try');
                            var normalTry = normalAttr !== null && normalAttr !== '' ? parseFloat(normalAttr) : NaN;
                            if (qty > 0 && !isNaN(normalTry) && normalTry > baseTry) {
                                var oldLineTry = normalTry * mult * pricingWeight;
                                var oldConverted = code === 'TRY' ? oldLineTry : oldLineTry * rate;
                                strikeEl.textContent = formatPrice(oldConverted);
                                strikeEl.classList.remove('hidden');
                            } else {
                                strikeEl.textContent = '';
                                strikeEl.classList.add('hidden');
                            }
                        }
                    }

                    function getSelectedParentOptionIdsForVariation(variationName) {
                        var block = document.querySelector('.product-variation-block[data-variation-name="' + variationName + '"]');
                        if (!block || block.style.display === 'none') return [];
                        var isMulti = (block.getAttribute('data-allows-multiple') || '') === '1';
                        var confirmed = (block.getAttribute('data-multi-confirmed') || '') === '1';
                        if (isMulti && !confirmed) return [];
                        if (isMulti) {
                            var ids = [];
                            block.querySelectorAll('.product-option.option-selected').forEach(function(b) {
                                if (b.style.display === 'none') return;
                                var id = b.getAttribute('data-option-id');
                                if (id) ids.push(Number(id));
                            });
                            return ids;
                        }
                        var selected = block.querySelector('.product-option.option-selected');
                        if (selected && selected.style.display !== 'none') {
                            var sid = selected.getAttribute('data-option-id');
                            return sid ? [Number(sid)] : [];
                        }
                        return [];
                    }

                    function filterDependentVariation(block, parentOptionIds) {
                        var ids = parentOptionIds && parentOptionIds.length ? parentOptionIds : [];
                        var options = block.querySelectorAll('.product-option');
                        var visible = [];
                        options.forEach(function(opt) {
                            var parentIdsJson = opt.getAttribute('data-parent-option-ids');
                            var parentIds = [];
                            if (parentIdsJson) {
                                try { parentIds = JSON.parse(parentIdsJson) || []; } catch (e) {}
                            }
                            var parentIdSingle = (opt.getAttribute('data-parent-option-id') || '').trim();
                            var show;
                            if (ids.length === 0) {
                                show = (parentIds.length === 0 && !parentIdSingle);
                            } else {
                                var matchArr = parentIds.length > 0 && ids.some(function(id) { return parentIds.indexOf(Number(id)) !== -1; });
                                var matchSingle = parentIdSingle && ids.indexOf(Number(parentIdSingle)) !== -1;
                                show = matchArr || matchSingle || (parentIds.length === 0 && !parentIdSingle);
                            }
                            opt.style.display = show ? '' : 'none';
                            if (show) visible.push(opt);
                        });
                        if (visible.length === 0) {
                            options.forEach(function(opt) { opt.style.display = ''; });
                            visible = Array.from(options);
                        }
                        var currentSelected = block.querySelector('.product-option.option-selected');
                        if (currentSelected && currentSelected.style.display === 'none') {
                            visible.forEach(function(b, i) {
                                var isFirst = i === 0;
                                b.classList.toggle('option-selected', isFirst);
                                b.classList.toggle('ring-2', isFirst);
                                b.classList.toggle('ring-primary-500', isFirst);
                                b.classList.toggle('border-primary-500', isFirst);
                                b.classList.toggle('bg-primary-50', isFirst);
                                b.classList.toggle('text-primary-700', isFirst);
                                b.classList.toggle('border-slate-300', !isFirst);
                            });
                        }
                    }

                    /** Kumaş türü adımında seçilen arayüz preset id (interface_fabric_type_variations.id). */
                    function getSelectedFabricPresetId() {
                        var fabricBlock = document.querySelector('.product-variation-block[data-variation-type="fabric"]');
                        if (!fabricBlock || fabricBlock.style.display === 'none') return null;
                        var isMulti = (fabricBlock.getAttribute('data-allows-multiple') || '') === '1';
                        var confirmed = (fabricBlock.getAttribute('data-multi-confirmed') || '') === '1';
                        if (isMulti && !confirmed) return null;
                        var sel = null;
                        if (isMulti) {
                            fabricBlock.querySelectorAll('.product-option.option-selected').forEach(function(b) {
                                if (b.style.display === 'none') return;
                                if (!sel) sel = b;
                            });
                        } else {
                            sel = fabricBlock.querySelector('.product-option.option-selected');
                            if (sel && sel.style.display === 'none') sel = null;
                        }
                        if (!sel) return null;
                        var id = (sel.getAttribute('data-fabric-preset-id') || '').trim();
                        return id === '' ? null : id;
                    }

                    /**
                     * Tip Renk: Admin gruplaması (interface_color_variations.interface_fabric_type_variation_id).
                     * Kumaş adımı varken: seçim yoksa yalnızca grupsuz renkler; kumaş seçilince yalnızca o gruba bağlı renkler (grupsuz gizlenir).
                     */
                    function filterColorOptionsByFabric() {
                        if (!document.querySelector('.product-variation-block[data-variation-type="color"]')) return;
                        var fabricStepExists = !!document.querySelector('.product-variation-block[data-variation-type="fabric"]');
                        var preset = fabricStepExists ? getSelectedFabricPresetId() : null;

                        document.querySelectorAll('.product-variation-block[data-variation-type="color"]').forEach(function(colorBlock) {
                            if (colorBlock.style.display === 'none') return;
                            var options = colorBlock.querySelectorAll('.product-option');
                            options.forEach(function(opt) {
                                var gid = (opt.getAttribute('data-color-fabric-group-id') || '').trim();
                                if (!fabricStepExists) {
                                    opt.style.display = '';
                                    return;
                                }
                                if (preset === null) {
                                    // Kumaş henüz seçilmedi: sadece grupsuz (tüm kumaşlar) renkler; gruplular kumaş seçiminden sonra
                                    opt.style.display = gid === '' ? '' : 'none';
                                    return;
                                }
                                // Kumaş seçildi: admindeki gruba göre yalnızca bu kumaş preset’ine bağlı renkler (grupsuzlar gizlenir)
                                opt.style.display = gid !== '' && gid === String(preset) ? '' : 'none';
                            });

                            var visible = [];
                            colorBlock.querySelectorAll('.product-option').forEach(function(opt) {
                                if (opt.style.display !== 'none') visible.push(opt);
                            });

                            var currentSelected = colorBlock.querySelector('.product-option.option-selected');
                            if (currentSelected && currentSelected.style.display === 'none') {
                                var isMulti = (colorBlock.getAttribute('data-allows-multiple') || '') === '1';
                                if (!isMulti) {
                                    visible.forEach(function(b, i) {
                                        setProductOptionVisual(b, i === 0);
                                    });
                                    if (visible.length) updatePanelSummary(colorBlock, (visible[0].getAttribute('data-option') || '').trim());
                                } else {
                                    colorBlock.querySelectorAll('.product-option.option-selected').forEach(function(b) {
                                        if (b.style.display === 'none') setProductOptionVisual(b, false);
                                    });
                                    colorBlock.setAttribute('data-multi-confirmed', '0');
                                    updatePanelSummaryMulti(colorBlock);
                                }
                            }
                        });
                    }

                    function allProductVariationBlocksComplete() {
                        var blocks = document.querySelectorAll('.product-variation-block');
                        for (var i = 0; i < blocks.length; i++) {
                            var block = blocks[i];
                            if (block.style.display === 'none') continue;
                            var isMulti = (block.getAttribute('data-allows-multiple') || '') === '1';
                            if (isMulti) {
                                if ((block.getAttribute('data-multi-confirmed') || '') !== '1') return false;
                                if (countVisibleSelectedOptions(block) < 1) return false;
                            } else {
                                var selected = block.querySelector('.product-option.option-selected');
                                if (!selected || selected.style.display === 'none') return false;
                            }
                        }
                        return true;
                    }

                    function allVisibleVariationsSelected() {
                        if (!allProductVariationBlocksComplete()) return false;
                        var sizePanel = document.querySelector('[data-size-step-panel="1"]');
                        if (sizePanel && (sizePanel.getAttribute('data-size-step-confirmed') || '') !== '1') return false;
                        var custPanel = document.querySelector('[data-customization-panel="1"]');
                        if (!custPanel) return true;
                        return (custPanel.getAttribute('data-customization-confirmed') || '') === '1';
                    }

                    function updateMultiContinueUi(container) {
                        var wrap = container.querySelector('.variation-multi-continue-wrap');
                        var btn = container.querySelector('.variation-multi-continue-btn');
                        if (!btn) return;
                        var n = countVisibleSelectedOptions(container);
                        var confirmed = (container.getAttribute('data-multi-confirmed') || '') === '1';
                        if (wrap) wrap.classList.toggle('hidden', confirmed);
                        btn.disabled = n < 1;
                    }

                    function updatePanelSummaryMulti(container) {
                        var vals = [];
                        container.querySelectorAll('.product-option.option-selected').forEach(function(b) {
                            if (b.style.display === 'none') return;
                            vals.push((b.getAttribute('data-option') || '').trim());
                        });
                        updatePanelSummary(container, vals.join(', ') || '—');
                        updateMultiContinueUi(container);
                    }

                    function getSizeQuantities() {
                        var sizeQuantities = {};
                        var total = 0;
                        var pricingWeight = 0;
                        var activeWrap = document.querySelector('.size-table-wrap:not(.hidden)');
                        if (activeWrap) {
                            activeWrap.querySelectorAll('input[class*="-size-input"]').forEach(function(inp) {
                                var size = inp.getAttribute('data-size');
                                var val = parseInt(inp.value, 10) || 0;
                                var mult = parseFloat(inp.getAttribute('data-price-multiplier'));
                                if (isNaN(mult) || mult < 0) mult = 1;
                                if (size) {
                                    sizeQuantities[size] = val;
                                    total += val;
                                    pricingWeight += val * mult;
                                }
                            });
                            return { sizeQuantities: sizeQuantities, total: total, pricingWeight: pricingWeight };
                        }
                        var simpleWrap = document.getElementById('quantity-simple-wrap');
                        if (simpleWrap && simpleWrap.classList.contains('hidden')) {
                            return { sizeQuantities: null, total: 0, pricingWeight: 0, simple: false };
                        }
                        var quantityInput = document.getElementById('quantity-input');
                        if (quantityInput) {
                            total = parseInt(quantityInput.value, 10) || 0;
                            return { sizeQuantities: null, total: total, pricingWeight: total, simple: true };
                        }
                        return { sizeQuantities: null, total: 0, pricingWeight: 0, simple: true };
                    }

                    function updateSizeTotalDisplays() {
                        document.querySelectorAll('.size-table-wrap').forEach(function(wrap) {
                            var slug = wrap.getAttribute('data-slug');
                            if (!slug) return;
                            var totalEl = document.getElementById(slug + '-size-total');
                            if (!totalEl) return;
                            var sum = 0;
                            wrap.querySelectorAll('input[class*="-size-input"]').forEach(function(inp) {
                                sum += parseInt(inp.value, 10) || 0;
                            });
                            totalEl.textContent = sum;
                        });
                    }

                    function escapeHtml(s) {
                        var div = document.createElement('div');
                        div.textContent = s;
                        return div.innerHTML;
                    }

                    function updateVariationSummaryAndButton() {
                        updateSizeTotalDisplays();
                        if (typeof updateSizeTableVisibility === 'function') updateSizeTableVisibility();
                        updatePriceAndInput();
                        var tbody = document.getElementById('variation-summary-tbody');
                        var warningEl = document.getElementById('variation-summary-warning');
                        var btn = document.getElementById('add-to-cart-btn');
                        var confirmCheckbox = document.getElementById('variation-confirm-checkbox');
                        var ordered = getSelectedOptionsInStepOrder();
                        var sizeInfo = getSizeQuantities();
                        if (!tbody) {
                            syncMainGalleryFromVariations();
                            return;
                        }
                        var summaryRowClass = 'transition-colors hover:bg-slate-50/60';
                        var summaryLabelCell = 'align-top px-3 py-3 text-sm font-medium text-slate-700 sm:px-4';
                        var summaryValueCell = 'px-3 py-3 text-sm text-slate-800 sm:px-4';
                        var rows = [];
                        ordered.forEach(function(item) {
                            rows.push('<tr class="' + summaryRowClass + '"><td class="' + summaryLabelCell + '">' + escapeHtml(item.name) + '</td><td class="' + summaryValueCell + '">' + escapeHtml(item.value) + '</td></tr>');
                        });
                        var custPayload = getCustomizationTablePayload();
                        var hasCustRows = !!(custPayload && custPayload.rows && custPayload.rows.length);
                        if (hasCustRows) {
                            rows.push('<tr class="summary-customization-row"><td colspan="2" class="p-0 border-0">' +
                                renderCustomizationSummarySectionHtml(custPayload.rows) +
                                '</td></tr>');
                        }
                        if (ordered.length > 0 || hasCustRows) {
                            if (ordered.length > 0) {
                                if (sizeInfo.sizeQuantities && Object.keys(sizeInfo.sizeQuantities).length) {
                                    Object.keys(sizeInfo.sizeQuantities).forEach(function(size) {
                                        var qty = sizeInfo.sizeQuantities[size];
                                        if (qty > 0) {
                                            rows.push('<tr class="' + summaryRowClass + '"><td class="' + summaryLabelCell + '">' + escapeHtml(PU.size_row_prefix) + ' ' + escapeHtml(size) + '</td><td class="' + summaryValueCell + '">' + qty + ' ' + escapeHtml(PU.units_suffix) + '</td></tr>');
                                        }
                                    });
                                    rows.push('<tr class="bg-slate-100/70"><td class="px-3 py-3 text-sm font-semibold text-slate-900 sm:px-4">' + escapeHtml(PU.summary_total_qty) + '</td><td class="px-3 py-3 text-sm font-semibold text-slate-900 sm:px-4">' + sizeInfo.total + ' ' + escapeHtml(PU.units_suffix) + '</td></tr>');
                                } else {
                                    rows.push('<tr class="bg-slate-100/70"><td class="px-3 py-3 text-sm font-semibold text-slate-900 sm:px-4">' + escapeHtml(PU.qty_row_label) + '</td><td class="px-3 py-3 text-sm font-semibold text-slate-900 sm:px-4">' + sizeInfo.total + ' ' + escapeHtml(PU.units_suffix) + '</td></tr>');
                                }
                            }
                            tbody.innerHTML = rows.join('');
                        } else {
                            tbody.innerHTML = '<tr><td colspan="2" class="px-3 py-8 text-center text-sm text-slate-400 sm:px-4">' + escapeHtml(PU.summary_select_prompt) + '</td></tr>';
                        }
                        var confirmWrap = document.getElementById('variation-confirm-wrap');
                        if (confirmWrap) {
                            confirmWrap.classList.toggle('hidden', ordered.length === 0);
                        }
                        if (confirmCheckbox && ordered.length === 0) {
                            confirmCheckbox.checked = false;
                        }
                        var allSelected = allVisibleVariationsSelected();
                        if (!allProductVariationBlocksComplete()) {
                            resetProductCustomizationUi();
                            if (totalVariationSteps > 0 && typeof clampVariationStepIndex === 'function' && typeof showVariationStep === 'function') {
                                showVariationStep(clampVariationStepIndex(currentVariationStep));
                            }
                        }
                        var confirmed = !confirmCheckbox || confirmCheckbox.checked;
                        var canSubmit = allSelected && confirmed;
                        var btnLabel = document.getElementById('add-to-cart-btn-label');
                        if (warningEl) {
                            warningEl.classList.toggle('hidden', allSelected);
                        }
                        if (btn) {
                            btn.disabled = !canSubmit;
                            if (btnLabel) {
                                btnLabel.textContent = canSubmit ? PU.add_to_cart : PU.add_to_cart_hint;
                            }
                        }
                        if (allSelected) {
                            if (!window._variationAllSelectedScrolled) {
                                window._variationAllSelectedScrolled = true;
                                var scrollTarget = document.getElementById('variation-summary-wrap') || document.getElementById('add-to-cart-btn');
                                if (scrollTarget) {
                                    scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            }
                        } else {
                            window._variationAllSelectedScrolled = false;
                        }
                        if (typeof updateSizeTableVisibility === 'function') updateSizeTableVisibility();
                        syncMainGalleryFromVariations();
                    }

                    /** Admin’de işaretlenen varyasyon(lar): seçilen seçeneğin görseli sol galerinin ilk slaytında (sıra önceliği). */
                    function syncMainGalleryFromVariations() {
                        var galleryEl = document.getElementById('product-gallery');
                        if (!galleryEl || window.__productGalleryOriginalFirstUrl === undefined) return;
                        var firstSlide = galleryEl.querySelector('.product-gallery-slide[data-slide-index="0"]');
                        if (!firstSlide) return;
                        var imgEl = firstSlide.querySelector('img');
                        if (!imgEl) return;
                        var panels = Array.from(document.querySelectorAll('.variation-step-panel[data-variation-name]')).sort(function(a, b) {
                            return (parseInt(a.getAttribute('data-step-index'), 10) || 0) - (parseInt(b.getAttribute('data-step-index'), 10) || 0);
                        });
                        var replacementUrl = '';
                        for (var pi = 0; pi < panels.length; pi++) {
                            var panel = panels[pi];
                            if (panel.style.display === 'none') continue;
                            if ((panel.getAttribute('data-replace-main-gallery') || '') !== '1') continue;
                            var isMultiGal = (panel.getAttribute('data-allows-multiple') || '') === '1';
                            var u = '';
                            if (isMultiGal) {
                                panel.querySelectorAll('.product-option.option-selected').forEach(function(el) {
                                    if (el.style.display === 'none') return;
                                    var cu = (el.getAttribute('data-option-image-url') || '').trim();
                                    if (cu && !u) u = cu;
                                });
                            } else {
                                var sel = panel.querySelector('.product-option.option-selected');
                                if (!sel || sel.style.display === 'none') continue;
                                u = (sel.getAttribute('data-option-image-url') || '').trim();
                            }
                            if (u) {
                                replacementUrl = u;
                                break;
                            }
                        }
                        var orig = window.__productGalleryOriginalFirstUrl || '';
                        var targetUrl = replacementUrl || orig;
                        if (!targetUrl) return;
                        firstSlide.setAttribute('data-image-url', targetUrl);
                        imgEl.src = targetUrl;
                        if (typeof window.__productGalleryRefreshUrls === 'function') {
                            window.__productGalleryRefreshUrls();
                        }
                    }

                    function applyDependencyChain() {
                        document.querySelectorAll('.product-variation-block').forEach(function(block) {
                            var dependsOn = (block.getAttribute('data-depends-on') || '').trim();
                            if (!dependsOn) return;
                            var parentIds = getSelectedParentOptionIdsForVariation(dependsOn);
                            if (parentIds.length === 0) {
                                block.style.display = 'none';
                                return;
                            }
                            block.style.display = '';
                            filterDependentVariation(block, parentIds);
                        });
                        filterColorOptionsByFabric();
                        updateSizeTableVisibility();
                        updateVariationSummaryAndButton();
                    }

                    var totalVariationSteps = document.querySelectorAll('.variation-step-dot').length;
                    var currentVariationStep = 0;

                    function updatePanelSummary(panel, selectedValue) {
                        var valEl = panel.querySelector('.variation-step-summary-value');
                        if (valEl) valEl.textContent = selectedValue || '—';
                    }

                    function clampVariationStepIndex(requested) {
                        var meta = getVariationStepsMeta();
                        var n = parseInt(requested, 10);
                        if (isNaN(n)) n = 0;
                        if (meta.sizeIdx >= 0 && n >= meta.sizeIdx && !allProductVariationBlocksComplete()) {
                            n = Math.max(0, meta.sizeIdx - 1);
                        }
                        if (meta.customizationIdx >= 0 && n >= meta.customizationIdx) {
                            var sp = document.querySelector('[data-size-step-panel="1"]');
                            var sz = sp && (sp.getAttribute('data-size-step-confirmed') || '') === '1';
                            if (!allProductVariationBlocksComplete() || !sz) {
                                n = meta.sizeIdx >= 0 ? meta.sizeIdx : Math.max(0, meta.customizationIdx - 1);
                            }
                        }
                        return n;
                    }

                    function isVariationStepDotLocked(dotIndex) {
                        var meta = getVariationStepsMeta();
                        if (meta.sizeIdx >= 0 && dotIndex === meta.sizeIdx) {
                            return !allProductVariationBlocksComplete();
                        }
                        if (meta.customizationIdx >= 0 && dotIndex === meta.customizationIdx) {
                            var sp = document.querySelector('[data-size-step-panel="1"]');
                            var sz = sp && (sp.getAttribute('data-size-step-confirmed') || '') === '1';
                            return !allProductVariationBlocksComplete() || !sz;
                        }
                        return false;
                    }

                    function showVariationStep(stepIndex) {
                        var si = clampVariationStepIndex(stepIndex);
                        currentVariationStep = si;
                        document.querySelectorAll('.variation-step-panel').forEach(function(panel) {
                            var idx = parseInt(panel.getAttribute('data-step-index'), 10);
                            var summary = panel.querySelector('.variation-step-summary');
                            var full = panel.querySelector('.variation-step-full');
                            var card = panel.querySelector('.variation-step-card');
                            var isCustPanel = (panel.getAttribute('data-customization-panel') || '') === '1';
                            var custDone = isCustPanel && (panel.getAttribute('data-customization-confirmed') || '') === '1';
                            panel.style.display = '';
                            if (idx === si) {
                                if (custDone && isCustPanel) {
                                    if (summary) { summary.classList.remove('hidden'); summary.classList.add('flex'); }
                                    if (full) { full.classList.add('hidden'); }
                                } else {
                                    if (summary) { summary.classList.add('hidden'); summary.classList.remove('flex'); }
                                    if (full) { full.classList.remove('hidden'); full.style.display = ''; }
                                }
                                if (card) { card.classList.add('border-primary-400'); card.classList.remove('border-slate-200'); }
                            } else if (idx < si) {
                                if (summary) { summary.classList.remove('hidden'); summary.classList.add('flex'); }
                                if (full && !full.classList.contains('size-table-step-content')) { full.classList.add('hidden'); }
                                if (card) { card.classList.remove('border-primary-400'); card.classList.add('border-slate-200'); }
                            } else {
                                panel.style.display = '';
                                if (summary) { summary.classList.add('hidden'); summary.classList.remove('flex'); }
                                if (full && !full.classList.contains('size-table-step-content')) { full.classList.add('hidden'); }
                                if (card) { card.classList.remove('border-primary-400'); card.classList.add('border-slate-200'); }
                            }
                        });
                        document.querySelectorAll('.variation-step-dot').forEach(function(dot, i) {
                            var panel = dot.closest('.variation-step-panel');
                            var num = (panel ? panel.querySelector('.variation-step-num') : null) || dot.querySelector('.variation-step-num');
                            var check = dot.querySelector('.variation-step-check');
                            dot.classList.remove('bg-primary-50/80', 'border-primary-100', 'bg-emerald-50/80', 'bg-slate-50/80', 'opacity-60', 'cursor-not-allowed', 'pointer-events-none');
                            var locked = isVariationStepDotLocked(i);
                            if (locked) {
                                dot.classList.add('bg-slate-50/80', 'opacity-60', 'cursor-not-allowed', 'pointer-events-none');
                                if (num) { num.classList.add('bg-slate-200', 'text-slate-600'); num.classList.remove('bg-primary-500', 'bg-emerald-500', 'text-white'); }
                                if (check) check.classList.add('hidden');
                                dot.setAttribute('aria-disabled', 'true');
                            } else if (i === si) {
                                dot.classList.add('bg-primary-50/80', 'border-primary-100');
                                if (num) { num.classList.add('bg-primary-500', 'text-white'); num.classList.remove('bg-slate-200', 'text-slate-600', 'bg-emerald-500'); }
                                if (check) check.classList.add('hidden');
                                dot.removeAttribute('aria-disabled');
                            } else if (i < si) {
                                dot.classList.add('bg-emerald-50/80');
                                if (num) { num.classList.add('bg-emerald-500', 'text-white'); num.classList.remove('bg-primary-500', 'bg-slate-200', 'text-slate-600'); }
                                if (check) check.classList.remove('hidden');
                                dot.removeAttribute('aria-disabled');
                            } else {
                                dot.classList.add('bg-slate-50/80', 'opacity-60', 'cursor-not-allowed', 'pointer-events-none');
                                if (num) { num.classList.add('bg-slate-200', 'text-slate-600'); num.classList.remove('bg-primary-500', 'bg-emerald-500', 'text-white'); }
                                if (check) check.classList.add('hidden');
                                dot.setAttribute('aria-disabled', 'true');
                            }
                        });
                        updateCustomizationContinueEnabled();
                    }

                    function invalidateCustomizationStepAfterVariationEdit() {
                        var sizePanel = document.querySelector('[data-size-step-panel="1"]');
                        if (sizePanel) sizePanel.setAttribute('data-size-step-confirmed', '0');
                        var custPanel = document.querySelector('[data-customization-panel="1"]');
                        if (custPanel && (custPanel.getAttribute('data-customization-confirmed') || '') === '1') {
                            custPanel.setAttribute('data-customization-confirmed', '0');
                            var sum = custPanel.querySelector('.variation-step-summary');
                            var sumVal = custPanel.querySelector('.variation-step-summary-value');
                            if (sum) {
                                sum.classList.add('hidden');
                                sum.classList.remove('flex');
                            }
                            if (sumVal) sumVal.textContent = '—';
                            var ctbl = document.getElementById('product-customization-table');
                            if (ctbl) ctbl.querySelectorAll('.customization-row-check').forEach(function(cb) { cb.checked = false; });
                            resetCustomizationTableFields();
                            applyCustomizationChoiceToVariationInput();
                        }
                        showVariationStep(clampVariationStepIndex(currentVariationStep));
                    }

                    function maybeAdvanceVariationStepAfterSelection() {
                        if (totalVariationSteps <= 0) return;
                        var meta = getVariationStepsMeta();
                        if (!allProductVariationBlocksComplete()) {
                            var next = currentVariationStep + 1;
                            if (meta.sizeIdx >= 0 && next >= meta.sizeIdx) {
                                return;
                            }
                            if (currentVariationStep < totalVariationSteps - 1) {
                                showVariationStep(next);
                            }
                            return;
                        }
                        var sizePanel = document.querySelector('[data-size-step-panel="1"]');
                        var sizeConfirmed = sizePanel && (sizePanel.getAttribute('data-size-step-confirmed') || '') === '1';
                        if (meta.sizeIdx >= 0 && !sizeConfirmed) {
                            showVariationStep(meta.sizeIdx);
                            return;
                        }
                        var custPanel = document.querySelector('[data-customization-panel="1"]');
                        var custConfirmed = custPanel && (custPanel.getAttribute('data-customization-confirmed') || '') === '1';
                        if (meta.customizationIdx >= 0 && sizeConfirmed && !custConfirmed) {
                            showVariationStep(meta.customizationIdx);
                            return;
                        }
                        if (currentVariationStep < totalVariationSteps - 1) {
                            showVariationStep(currentVariationStep + 1);
                        } else if (allVisibleVariationsSelected()) {
                            showVariationStep(totalVariationSteps - 1);
                        }
                    }

                    function setProductOptionVisual(el, on) {
                        el.classList.toggle('option-selected', on);
                        el.classList.toggle('ring-2', on);
                        el.classList.toggle('ring-primary-500', on);
                        el.classList.toggle('border-primary-500', on);
                        el.classList.toggle('bg-primary-50', on);
                        el.classList.toggle('text-primary-700', on);
                        el.classList.toggle('border-slate-300', !on);
                    }

                    function selectOption(btn) {
                        var variation = btn.getAttribute('data-variation');
                        var optionValue = btn.getAttribute('data-option') || '';
                        var container = btn.closest('.product-variation-block');
                        if (!container) return;
                        invalidateCustomizationStepAfterVariationEdit();
                        var isMulti = (container.getAttribute('data-allows-multiple') || '') === '1';

                        if (isMulti) {
                            var isSolo = (btn.getAttribute('data-option-solo') || '') === '1';
                            if (isSolo) {
                                if (btn.classList.contains('option-selected') && countVisibleSelectedOptions(container) === 1) {
                                    setProductOptionVisual(btn, false);
                                    container.setAttribute('data-multi-confirmed', '0');
                                    updatePanelSummaryMulti(container);
                                    applyDependencyChain();
                                    updateVariationSummaryAndButton();
                                    return;
                                }
                                container.querySelectorAll('.product-option').forEach(function(b) {
                                    if (b.style.display === 'none') return;
                                    setProductOptionVisual(b, b === btn);
                                });
                                container.setAttribute('data-multi-confirmed', '1');
                                updatePanelSummaryMulti(container);
                                updateSizeTableVisibility();
                                applyDependencyChain();
                                maybeAdvanceVariationStepAfterSelection();
                                updateVariationSummaryAndButton();
                                return;
                            }
                            var turnOn = !btn.classList.contains('option-selected');
                            if (turnOn) {
                                container.querySelectorAll('.product-option.option-selected[data-option-solo="1"]').forEach(function(s) {
                                    if (s.style.display === 'none') return;
                                    setProductOptionVisual(s, false);
                                });
                            }
                            setProductOptionVisual(btn, turnOn);
                            container.setAttribute('data-multi-confirmed', '0');
                            updatePanelSummaryMulti(container);
                            applyDependencyChain();
                            updateVariationSummaryAndButton();
                            return;
                        }

                        container.querySelectorAll('.product-option').forEach(function(b) {
                            if (b.style.display === 'none') return;
                            var on = b === btn;
                            b.classList.toggle('option-selected', on);
                            b.classList.toggle('ring-2', on);
                            b.classList.toggle('ring-primary-500', on);
                            b.classList.toggle('border-primary-500', on);
                            b.classList.toggle('bg-primary-50', on);
                            b.classList.toggle('text-primary-700', on);
                            b.classList.toggle('border-slate-300', !on);
                        });
                        updatePanelSummary(container, optionValue);
                        updateSizeTableVisibility();
                        document.querySelectorAll('.product-variation-block[data-depends-on="' + variation + '"]').forEach(function(depBlock) {
                            var pid = btn.getAttribute('data-option-id');
                            depBlock.style.display = '';
                            filterDependentVariation(depBlock, pid ? [Number(pid)] : []);
                        });
                        applyDependencyChain();
                        maybeAdvanceVariationStepAfterSelection();
                    }

                    document.querySelectorAll('.product-option').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            if (this.style.display === 'none') return;
                            selectOption(this);
                        });
                    });

                    document.querySelectorAll('.variation-multi-continue-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var container = btn.closest('.product-variation-block');
                            if (!container) return;
                            if (countVisibleSelectedOptions(container) < 1) return;
                            container.setAttribute('data-multi-confirmed', '1');
                            updatePanelSummaryMulti(container);
                            updateSizeTableVisibility();
                            applyDependencyChain();
                            maybeAdvanceVariationStepAfterSelection();
                            updateVariationSummaryAndButton();
                        });
                    });

                    document.querySelectorAll('.variation-step-dot').forEach(function(dot) {
                        dot.addEventListener('click', function() {
                            if (this.getAttribute('aria-disabled') === 'true') return;
                            var step = parseInt(this.getAttribute('data-step'), 10);
                            if (isNaN(step)) return;
                            if (isVariationStepDotLocked(step)) return;
                            if (step > currentVariationStep) return;
                            showVariationStep(step);
                            requestAnimationFrame(function() {
                                if (typeof updateSizeTableVisibility === 'function') updateSizeTableVisibility();
                            });
                        });
                    });

                    document.querySelectorAll('.variation-step-change-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var panel = this.closest('.variation-step-panel');
                            if (!panel) return;
                            if ((panel.getAttribute('data-customization-panel') || '') === '1') {
                                panel.setAttribute('data-customization-confirmed', '0');
                                applyCustomizationChoiceToVariationInput();
                            }
                            if ((panel.getAttribute('data-allows-multiple') || '') === '1') {
                                panel.setAttribute('data-multi-confirmed', '0');
                            }
                            var step = parseInt(panel.getAttribute('data-step-index'), 10);
                            showVariationStep(step);
                            requestAnimationFrame(function() {
                                if ((panel.getAttribute('data-allows-multiple') || '') === '1') {
                                    updateMultiContinueUi(panel);
                                }
                            });
                        });
                    });

                    document.addEventListener('click', function(e) {
                        var zoomBtn = e.target.closest('.variation-zoom-btn');
                        if (!zoomBtn) return;
                        e.preventDefault();
                        e.stopPropagation();
                        var url = zoomBtn.getAttribute('data-image-url');
                        var alt = zoomBtn.getAttribute('data-image-alt') || '';
                        var lb = document.getElementById('variation-image-lightbox');
                        var lbImg = document.getElementById('variation-lightbox-image');
                        if (lb && lbImg && url) {
                            lbImg.src = url;
                            lbImg.alt = alt;
                            lb.classList.remove('hidden');
                            lb.classList.add('flex');
                            document.body.style.overflow = 'hidden';
                        }
                    });

                    function closeVariationLightbox() {
                        var lb = document.getElementById('variation-image-lightbox');
                        if (lb) {
                            lb.classList.add('hidden');
                            lb.classList.remove('flex');
                            document.body.style.overflow = '';
                        }
                    }
                    var variationLightboxClose = document.getElementById('variation-lightbox-close');
                    var variationLightboxBackdrop = document.getElementById('variation-lightbox-backdrop');
                    if (variationLightboxClose) variationLightboxClose.addEventListener('click', closeVariationLightbox);
                    if (variationLightboxBackdrop) variationLightboxBackdrop.addEventListener('click', closeVariationLightbox);
                    document.addEventListener('keydown', function(ev) {
                        if (ev.key === 'Escape') {
                            var lb = document.getElementById('variation-image-lightbox');
                            if (lb && !lb.classList.contains('hidden')) closeVariationLightbox();
                        }
                    });

                    function normalizeForMatch(s) {
                        if (!s || typeof s !== 'string') return '';
                        var x = s.trim().toLowerCase();
                        return x.replace(/ç/g, 'c').replace(/ğ/g, 'g').replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ş/g, 's').replace(/ü/g, 'u');
                    }
                    function valueMatchesTrigger(selectedValue, triggerValue) {
                        if (!triggerValue) return false;
                        var v = (selectedValue || '').trim();
                        var t = (triggerValue || '').trim();
                        if (v === t) return true;
                        if (v.toLowerCase() === t.toLowerCase()) return true;
                        if (normalizeForMatch(v) === normalizeForMatch(t)) return true;
                        var kadinAliases = ['kadın', 'kadin'];
                        if (kadinAliases.indexOf(v.toLowerCase()) !== -1 && kadinAliases.indexOf(t.toLowerCase()) !== -1) return true;
                        var cocukAliases = ['çocuk', 'cocuk', 'çoçuk', 'coçuk', 'cocuk'];
                        var vn = normalizeForMatch(v), tn = normalizeForMatch(t);
                        if (vn === 'cocuk' && tn === 'cocuk') return true;
                        return cocukAliases.indexOf(v.toLowerCase()) !== -1 && cocukAliases.indexOf(t.toLowerCase()) !== -1;
                    }
                    function rawSelectedValuesForVariationKey(selected, variation) {
                        if (!variation) return [];
                        var vv = variation.trim().toLowerCase();
                        var raw = selected[variation];
                        if (raw !== undefined && raw !== null) {
                            if (Array.isArray(raw)) return raw.map(function(x) { return String(x).trim(); }).filter(Boolean);
                            var s = String(raw).trim();
                            return s ? [s] : [];
                        }
                        var keyMatch = Object.keys(selected).find(function(k) {
                            var kk = (k || '').trim().toLowerCase();
                            if (kk === vv) return true;
                            if (kk.indexOf(vv) === 0) return true;
                            var firstWord = kk.split(/\s+/)[0] || '';
                            return firstWord === vv;
                        });
                        if (!keyMatch) return [];
                        var r = selected[keyMatch];
                        if (Array.isArray(r)) return r.map(function(x) { return String(x).trim(); }).filter(Boolean);
                        var t = String(r || '').trim();
                        return t ? [t] : [];
                    }
                    /**
                     * Çoklu seçimde "Devam et" öncesi veya özet nesnesinde eksik anahtar olsa bile,
                     * varyasyon panelindeki gerçek seçimi döndürür (beden önkoşulu / tetikleyici için).
                     */
                    function getDomSelectionValuesForVariation(variationName) {
                        var target = (variationName || '').trim();
                        if (!target) return [];
                        var tLower = target.toLowerCase();
                        var panel = null;
                        document.querySelectorAll('.variation-step-panel[data-variation-name]').forEach(function(p) {
                            if (panel) return;
                            var n = (p.getAttribute('data-variation-name') || '').trim();
                            if (n.toLowerCase() === tLower) panel = p;
                        });
                        if (!panel || panel.style.display === 'none') return [];
                        var isMulti = (panel.getAttribute('data-allows-multiple') || '') === '1';
                        var vals = [];
                        if (isMulti) {
                            panel.querySelectorAll('.product-option.option-selected').forEach(function(btn) {
                                if (btn.style.display === 'none') return;
                                var v = (btn.getAttribute('data-option') || '').trim();
                                if (v) vals.push(v);
                            });
                        } else {
                            var sel = panel.querySelector('.product-option.option-selected');
                            if (sel && sel.style.display !== 'none') {
                                var v = (sel.getAttribute('data-option') || '').trim();
                                if (v) vals.push(v);
                            }
                        }
                        return vals;
                    }
                    function selectedValuesForVariation(selected, variationKey) {
                        var vals = rawSelectedValuesForVariationKey(selected, variationKey);
                        if (vals.length > 0) return vals;
                        return getDomSelectionValuesForVariation(variationKey);
                    }
                    function openProductWarningDialog(title, desc) {
                        var dialog = document.getElementById('product-warning-dialog');
                        var descEl = document.getElementById('product-warning-dialog-desc');
                        var titleEl = document.getElementById('product-warning-dialog-title');
                        if (dialog && descEl) {
                            if (titleEl) titleEl.textContent = title;
                            descEl.textContent = desc;
                            dialog.classList.remove('hidden');
                            dialog.classList.add('flex');
                            document.body.style.overflow = 'hidden';
                        }
                    }
                    function validateSizeStepOrWarn() {
                        var formEl = document.getElementById('add-to-cart-form');
                        var availableStock = formEl ? parseInt(formEl.getAttribute('data-available-stock'), 10) || 999999 : 999999;
                        var quantityInput = document.getElementById('quantity-input');
                        var minOrder = quantityInput ? parseInt(quantityInput.getAttribute('min'), 10) || 1 : 1;
                        var info = getSizeQuantities();
                        var activeWrap = document.querySelector('.size-table-wrap:not(.hidden)');
                        if (activeWrap) {
                            if (info.total < minOrder) {
                                openProductWarningDialog(PU.warn_min_title, PU.warn_min_desc.replace(':min', String(minOrder)).replace(':total', String(info.total)));
                                return false;
                            }
                            if (info.total > availableStock) {
                                openProductWarningDialog(PU.warn_stock_title, PU.warn_stock_desc_breakdown.replace(':max', String(availableStock)).replace(':total', String(info.total)));
                                return false;
                            }
                            return true;
                        }
                        var qty = quantityInput ? parseInt(quantityInput.value, 10) || 0 : 0;
                        if (qty < minOrder) {
                            openProductWarningDialog(PU.warn_min_title, PU.warn_min_desc.replace(':min', String(minOrder)).replace(':total', String(qty)));
                            return false;
                        }
                        if (qty > availableStock) {
                            openProductWarningDialog(PU.warn_stock_title, PU.warn_stock_desc_simple.replace(':max', String(availableStock)).replace(':qty', String(qty)));
                            return false;
                        }
                        return true;
                    }
                    function updateSizeTableVisibility() {
                        var selected = getSelectedOptions();
                        var formEl = document.getElementById('add-to-cart-form');
                        var simpleWrap = document.getElementById('quantity-simple-wrap');
                        var quantityInput = document.getElementById('quantity-input');
                        var hasVariations = document.querySelectorAll('.product-variation-block').length > 0;
                        var variationBlocksDone = allProductVariationBlocksComplete();
                        var sizeTableContent = document.querySelector('.size-table-step-content');

                        if (!variationBlocksDone) {
                            if (sizeTableContent) sizeTableContent.classList.add('hidden');
                            document.querySelectorAll('.size-table-wrap').forEach(function(wrap) { wrap.classList.add('hidden'); });
                            if (simpleWrap) simpleWrap.classList.toggle('hidden', hasVariations);
                            if (quantityInput) quantityInput.setAttribute('name', 'quantity');
                            return;
                        }

                        if (sizeTableContent) sizeTableContent.classList.remove('hidden');
                        var productTriggerVariation = (formEl && formEl.getAttribute('data-size-table-trigger-variation')) ? formEl.getAttribute('data-size-table-trigger-variation').trim() : '';
                        if (productTriggerVariation && selectedValuesForVariation(selected, productTriggerVariation).length === 0) {
                            document.querySelectorAll('.size-table-wrap').forEach(function(wrap) { wrap.classList.add('hidden'); });
                            if (simpleWrap) simpleWrap.classList.toggle('hidden', hasVariations);
                            if (quantityInput) quantityInput.setAttribute('name', 'quantity');
                            return;
                        }
                        var anyTableVisible = false;
                        document.querySelectorAll('.size-table-wrap').forEach(function(wrap) {
                            var variation = (wrap.getAttribute('data-trigger-variation') || '').trim();
                            var value = (wrap.getAttribute('data-trigger-value') || '').trim();
                            var selectedVals = selectedValuesForVariation(selected, variation);
                            var show = false;
                            if (variation && value) {
                                show = selectedVals.some(function(sv) { return valueMatchesTrigger(sv, value); });
                            } else if (variation) {
                                show = selectedVals.length > 0;
                            } else {
                                var slug = (wrap.getAttribute('data-slug') || '').toLowerCase();
                                function selectedMatchesGender(label) {
                                    return Object.keys(selected).some(function(k) {
                                        var raw = selected[k];
                                        var parts = Array.isArray(raw) ? raw : [raw];
                                        return parts.some(function(p) { return valueMatchesTrigger(String(p || '').trim(), label); });
                                    });
                                }
                                if (slug === 'erkek') show = selectedMatchesGender('Erkek');
                                else if (slug === 'kadin') show = selectedMatchesGender('Kadın');
                                else if (slug === 'cocuk') show = selectedMatchesGender('Çocuk');
                            }
                            wrap.classList.toggle('hidden', !show);
                            if (show) anyTableVisible = true;
                        });
                        if (simpleWrap) {
                            simpleWrap.classList.toggle('hidden', anyTableVisible || (hasVariations && !variationBlocksDone));
                        }
                        if (quantityInput) quantityInput.setAttribute('name', anyTableVisible ? 'quantity_placeholder' : 'quantity');
                    }

                    applyDependencyChain();
                    if (totalVariationSteps > 0) showVariationStep(0);
                    document.querySelectorAll('.product-variation-block[data-allows-multiple="1"]').forEach(function(el) {
                        updateMultiContinueUi(el);
                    });
                    updateSizeTableVisibility();
                    updateVariationSummaryAndButton();

                    var confirmCheckbox = document.getElementById('variation-confirm-checkbox');
                    if (confirmCheckbox) {
                        confirmCheckbox.addEventListener('change', updateVariationSummaryAndButton);
                    }
                    var quantityInput = document.getElementById('quantity-input');
                    if (quantityInput) {
                        quantityInput.addEventListener('input', updateVariationSummaryAndButton);
                        quantityInput.addEventListener('change', updateVariationSummaryAndButton);
                    }
                    document.querySelectorAll('input[class*="-size-input"]').forEach(function(inp) {
                        inp.addEventListener('input', updateVariationSummaryAndButton);
                        inp.addEventListener('change', updateVariationSummaryAndButton);
                    });

                    var customizationContinueBtn = document.getElementById('customization-continue-btn');
                    if (customizationContinueBtn) {
                        customizationContinueBtn.addEventListener('click', function() {
                            var custPanel = document.querySelector('[data-customization-panel="1"]');
                            if (!custPanel) return;
                            custPanel.setAttribute('data-customization-confirmed', '1');
                            var sumVal = custPanel.querySelector('.variation-step-summary-value');
                            if (sumVal) sumVal.textContent = customizationSummaryLineFromInputs();
                            applyCustomizationChoiceToVariationInput();
                            updateSizeTableVisibility();
                            updateVariationSummaryAndButton();
                            var meta = getVariationStepsMeta();
                            if (!isNaN(meta.customizationIdx) && meta.customizationIdx >= 0) {
                                showVariationStep(meta.customizationIdx);
                            }
                            requestAnimationFrame(function() {
                                if (typeof updateSizeTableVisibility === 'function') updateSizeTableVisibility();
                            });
                        });
                    }

                    (function bindCustomizationTableUi() {
                        var tbl = document.getElementById('product-customization-table');
                        if (!tbl) return;
                        function syncCustUi() {
                            updateCustomizationContinueEnabled();
                            if (typeof updateVariationSummaryAndButton === 'function') updateVariationSummaryAndButton();
                        }
                        tbl.addEventListener('change', syncCustUi);
                        tbl.addEventListener('input', function(e) {
                            if (e.target && e.target.closest && e.target.closest('.customization-row-card')) syncCustUi();
                        });
                        syncCustUi();
                    })();

                    var sizeStepContinueBtn = document.getElementById('size-step-continue-btn');
                    if (sizeStepContinueBtn) {
                        sizeStepContinueBtn.addEventListener('click', function() {
                            if (!validateSizeStepOrWarn()) return;
                            var sp = document.querySelector('[data-size-step-panel="1"]');
                            if (sp) sp.setAttribute('data-size-step-confirmed', '1');
                            updateSizeTableVisibility();
                            updateVariationSummaryAndButton();
                            var meta = getVariationStepsMeta();
                            if (!isNaN(meta.customizationIdx) && meta.customizationIdx >= 0) {
                                showVariationStep(meta.customizationIdx);
                            } else {
                                showVariationStep(totalVariationSteps - 1);
                            }
                            requestAnimationFrame(function() {
                                if (typeof updateSizeTableVisibility === 'function') updateSizeTableVisibility();
                            });
                        });
                    }

                    var form = document.getElementById('add-to-cart-form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            if (variationInput && !allVisibleVariationsSelected()) {
                                e.preventDefault();
                                var w = document.getElementById('variation-summary-warning');
                                if (w) w.classList.remove('hidden');
                                var summaryWrap = document.getElementById('variation-summary-wrap');
                                if (summaryWrap) summaryWrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                return;
                            }
                            var confirmCb = document.getElementById('variation-confirm-checkbox');
                            if (confirmCb && !confirmCb.checked) {
                                e.preventDefault();
                                var summaryWrap = document.getElementById('variation-summary-wrap');
                                if (summaryWrap) summaryWrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                return;
                            }
                            applyCustomizationChoiceToVariationInput();
                            var sizeInput = document.getElementById('size-quantities-input');
                            var quantityInput = document.getElementById('quantity-input');
                            var minOrder = quantityInput ? parseInt(quantityInput.getAttribute('min'), 10) || 1 : 1;
                            var sizeQuantities = {};
                            var total = 0;
                            var activeWrap = document.querySelector('.size-table-wrap:not(.hidden)');
                            if (activeWrap) {
                                activeWrap.querySelectorAll('input[class*="-size-input"]').forEach(function(inp) {
                                    var size = inp.getAttribute('data-size');
                                    var val = parseInt(inp.value, 10) || 0;
                                    sizeQuantities[size] = val;
                                    total += val;
                                });
                            }
                            if (activeWrap) {
                                var formEl = document.getElementById('add-to-cart-form');
                                var availableStock = formEl ? parseInt(formEl.getAttribute('data-available-stock'), 10) || 999999 : 999999;
                                if (total < minOrder) {
                                    e.preventDefault();
                                    var dialog = document.getElementById('product-warning-dialog');
                                    var descEl = document.getElementById('product-warning-dialog-desc');
                                    var titleEl = document.getElementById('product-warning-dialog-title');
                                    if (dialog && descEl) {
                                        if (titleEl) titleEl.textContent = PU.warn_min_title;
                                        descEl.textContent = PU.warn_min_desc.replace(':min', String(minOrder)).replace(':total', String(total));
                                        dialog.classList.remove('hidden');
                                        dialog.classList.add('flex');
                                        document.body.style.overflow = 'hidden';
                                    }
                                    return;
                                }
                                if (total > availableStock) {
                                    e.preventDefault();
                                    var dialog = document.getElementById('product-warning-dialog');
                                    var descEl = document.getElementById('product-warning-dialog-desc');
                                    var titleEl = document.getElementById('product-warning-dialog-title');
                                    if (dialog && descEl) {
                                        if (titleEl) titleEl.textContent = PU.warn_stock_title;
                                        descEl.textContent = PU.warn_stock_desc_breakdown.replace(':max', String(availableStock)).replace(':total', String(total));
                                        dialog.classList.remove('hidden');
                                        dialog.classList.add('flex');
                                        document.body.style.overflow = 'hidden';
                                    }
                                    return;
                                }
                                if (sizeInput) sizeInput.value = JSON.stringify(sizeQuantities);
                                if (quantityInput) {
                                    quantityInput.setAttribute('name', 'quantity');
                                    quantityInput.value = total;
                                }
                            } else {
                                if (sizeInput) sizeInput.value = '';
                                var formEl = document.getElementById('add-to-cart-form');
                                var availableStock = formEl ? parseInt(formEl.getAttribute('data-available-stock'), 10) || 999999 : 999999;
                                var qty = quantityInput ? parseInt(quantityInput.value, 10) || 0 : 0;
                                if (qty > availableStock) {
                                    e.preventDefault();
                                    var dialog = document.getElementById('product-warning-dialog');
                                    var descEl = document.getElementById('product-warning-dialog-desc');
                                    var titleEl = document.getElementById('product-warning-dialog-title');
                                    if (dialog && descEl) {
                                        if (titleEl) titleEl.textContent = PU.warn_stock_title;
                                        descEl.textContent = PU.warn_stock_desc_simple.replace(':max', String(availableStock)).replace(':qty', String(qty));
                                        dialog.classList.remove('hidden');
                                        dialog.classList.add('flex');
                                        document.body.style.overflow = 'hidden';
                                    }
                                    return;
                                }
                            }
                        });
                    }

                    var warningDialog = document.getElementById('product-warning-dialog');
                    var warningDialogClose = document.getElementById('product-warning-dialog-close');
                    var warningDialogBackdrop = document.getElementById('product-warning-dialog-backdrop');
                    function closeProductWarningDialog() {
                        if (warningDialog) {
                            warningDialog.classList.add('hidden');
                            warningDialog.classList.remove('flex');
                            document.body.style.overflow = '';
                        }
                    }
                    if (warningDialogClose) warningDialogClose.addEventListener('click', closeProductWarningDialog);
                    if (warningDialogBackdrop) warningDialogBackdrop.addEventListener('click', closeProductWarningDialog);
                    document.addEventListener('keydown', function(ev) {
                        if (ev.key === 'Escape' && warningDialog && !warningDialog.classList.contains('hidden')) closeProductWarningDialog();
                    });
                });
            </script>
        @endpush
    @elseif($canSeePrices && !$hasVariations)
        @push('head')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var priceEl = document.getElementById('product-price');
                    var quantityInput = document.getElementById('quantity-input');
                    if (!priceEl || !quantityInput) return;

                    var baseTry = parseFloat(priceEl.getAttribute('data-base-try')) || 0;
                    var rate = parseFloat(priceEl.getAttribute('data-exchange-rate')) || 1;
                    var symbol = priceEl.getAttribute('data-currency-symbol') || '₺';
                    var code = priceEl.getAttribute('data-currency-code') || 'TRY';

                    function formatPrice(num) {
                        if (code === 'TRY') return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num) + ' ' + symbol;
                        return symbol + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
                    }

                    function updateSimpleLineTotal() {
                        var qty = Math.max(0, parseInt(quantityInput.value, 10) || 0);
                        var lineTry = baseTry * qty;
                        var lineConverted = code === 'TRY' ? lineTry : lineTry * rate;
                        priceEl.textContent = formatPrice(lineConverted);
                        var strikeEl = document.getElementById('product-price-strike');
                        if (strikeEl) {
                            var normalAttr = priceEl.getAttribute('data-normal-try');
                            var normalTry = normalAttr !== null && normalAttr !== '' ? parseFloat(normalAttr) : NaN;
                            if (qty > 0 && !isNaN(normalTry) && normalTry > baseTry) {
                                var oldLineTry = normalTry * qty;
                                var oldConverted = code === 'TRY' ? oldLineTry : oldLineTry * rate;
                                strikeEl.textContent = formatPrice(oldConverted);
                                strikeEl.classList.remove('hidden');
                            } else {
                                strikeEl.textContent = '';
                                strikeEl.classList.add('hidden');
                            }
                        }
                    }

                    quantityInput.addEventListener('input', updateSimpleLineTotal);
                    quantityInput.addEventListener('change', updateSimpleLineTotal);
                    updateSimpleLineTotal();
                });
            </script>
        @endpush
    @endif

@endsection
