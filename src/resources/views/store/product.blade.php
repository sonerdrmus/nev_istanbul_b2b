@extends('store.layout')

@section('title', $product->localized_meta_title ?: $product->localized_name)

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
                <span class="text-slate-700">{{ $product->category->parent->localized_name }} › {{ $product->category->localized_name }}</span>
            @else
                <a href="{{ route('home', ['category' => $product->category->slug]) }}" class="hover:text-primary-600 transition-colors">{{ $product->category->localized_name }}</a>
            @endif
            <span>/</span>
        @endif
        <span class="text-slate-900 font-medium truncate max-w-[200px] sm:max-w-none">{{ $product->localized_name }}</span>
    </nav>

    @php
        $labelArtworkNoticeEmail = 'info@sonerdurmus.com';
        $hasVariations = $product->variations->isNotEmpty();
        $showPurchasePanel = $product->isOnSale();
        $selectedCurrency = $selectedCurrency ?? \App\Models\Currency::getDefault();
        $minOrderProduct = $product->getMinimumOrderQuantity();
        $normalPriceTry = null;
        $baseTry = null;
        $baseConverted = null;
        $hasProductDiscount = false;
        $priceTiersForStore = [];
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
            $priceTiersForStore = $product->priceTiersForStore($customerDiscountPercent ?? null);
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
                            <img src="{{ $url }}" alt="{{ __('store.product.image_alt', ['name' => $product->localized_name, 'num' => $idx + 1]) }}" class="max-w-full max-h-full w-auto h-auto object-contain">
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
                    <img id="lightbox-image" src="" alt="{{ $product->localized_name }}" class="max-w-full max-h-[90vh] w-auto h-auto object-contain">
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
                <div id="variation-summary-body" class="space-y-4" aria-label="{{ __('store.product.variation_summary_aria') }}" role="region">
                    <p class="rounded-xl border border-dashed border-slate-200 bg-slate-50/60 px-4 py-8 text-center text-sm text-slate-400">{{ __('store.product.summary_select_prompt') }}</p>
                </div>
                <style>
                    #variation-summary-body .customization-summary-metric:nth-child(odd) { background: rgba(248, 250, 252, 0.65); }
                    @media (min-width: 640px) {
                        #variation-summary-body .customization-summary-metric { background: transparent; }
                    }
                </style>
                <div id="variation-label-artwork-notice" class="mt-3 hidden rounded-xl border-2 border-amber-400/90 bg-gradient-to-br from-amber-50 to-amber-100/80 px-4 py-3.5 shadow-sm ring-1 ring-amber-300/50" role="status" aria-live="polite">
                    <div class="flex gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-500 text-white shadow-sm" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold tracking-tight text-amber-950">{{ __('store.product.label_artwork_notice_title') }}</p>
                            <p class="mt-1.5 text-sm leading-relaxed text-amber-900">
                                {{ __('store.product.label_artwork_notice_body_before') }}<a href="mailto:{{ $labelArtworkNoticeEmail }}" class="font-semibold text-amber-950 underline underline-offset-2 decoration-amber-600/70 hover:text-amber-800">{{ $labelArtworkNoticeEmail }}</a>{{ __('store.product.label_artwork_notice_body_after') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div id="variation-delivery-estimate-notice" class="mt-3 hidden rounded-xl border-2 border-sky-400/90 bg-gradient-to-br from-sky-50 via-sky-50/95 to-blue-50 px-4 py-3.5 shadow-md ring-2 ring-sky-200/70" role="status" aria-live="polite">
                    <div class="flex gap-3 sm:gap-4">
                        <span class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-xl bg-sky-600 text-white shadow-sm" aria-hidden="true">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm sm:text-base font-bold text-sky-950 leading-snug">{{ __('store.product.delivery_estimated_time_title') }}</p>
                            <p id="variation-delivery-estimate-notice-text" class="mt-1.5 text-sm sm:text-[0.9375rem] font-semibold leading-relaxed text-sky-900"></p>
                        </div>
                    </div>
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
                    <div class="grid grid-cols-[auto_1fr] items-center gap-x-3 gap-y-1.5">
                        <span class="text-sm font-semibold text-slate-600">{{ __('store.product.unit_price_label') }}</span>
                        <div class="text-right">
                            <span id="product-base-price" class="text-sm font-semibold text-slate-700">
                                {{ $selectedCurrency->format($baseConverted) }}
                            </span>
                        </div>
                        <span class="text-sm font-semibold text-slate-600">{{ __('store.product.per_piece_price_label') }}</span>
                        <div class="text-right">
                            <span id="product-per-piece-price" class="text-sm font-semibold text-primary-800">—</span>
                            <p id="product-per-piece-price-note" class="mt-0.5 hidden text-[11px] font-normal text-slate-500 tabular-nums"></p>
                        </div>
                        <span class="text-sm font-semibold text-slate-600">{{ __('store.product.order_total_price_label') }}</span>
                        <div class="text-right">
                            <span id="product-price" data-base-try="{{ $baseTry }}" data-price-tiers='@json($priceTiersForStore)' @if($hasProductDiscount && $normalPriceTry !== null) data-normal-try="{{ $normalPriceTry }}" @endif data-exchange-rate="{{ (float) $selectedCurrency->exchange_rate }}" data-currency-code="{{ $selectedCurrency->code }}" data-currency-symbol="{{ $selectedCurrency->symbol }}" class="text-lg font-bold text-slate-900">
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
                <a href="{{ route('home', ['category' => $product->category->slug]) }}" class="inline-block text-sm text-slate-500 hover:text-primary-600 mb-2">{{ $product->category->localized_name }}</a>
            @endif
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">{{ $product->localized_name }}</h1>

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
                    {!! $product->localized_description !!}
                </div>
            @endif

    @if($showPurchasePanel)
    @php
        $minOrder = $product->getMinimumOrderQuantity();
        $availableStock = 999999;
    @endphp
    <section class="mt-5 lg:mt-6 w-full" aria-label="{{ __('store.product.section_order_options') }}">
        <div class="max-w-full">
            <form action="{{ route('store.cart.add') }}" method="POST" class="rounded-2xl lg:rounded-2xl border border-slate-200/90 bg-slate-50/90 p-2 sm:p-5 lg:p-2 shadow-sm shadow-slate-200/30 ring-1 ring-slate-200/40" id="add-to-cart-form" data-available-stock="{{ $availableStock }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="order_mode" id="order-mode-input" value="detailed">
                @if($hasVariations)
                    <input type="hidden" name="variation_data" id="variation-data-input" value="">
                @endif
                <input type="hidden" name="size_quantities" id="size-quantities-input" value="">

                {{-- Normal adet alanı (varyasyon yoksa hemen göster, varyasyon varsa tüm seçenekler seçilince göster) --}}
                <div id="quantity-simple-wrap" class="{{ $hasVariations ? 'hidden' : '' }}">
                    <label class="block font-semibold text-slate-700 mb-2">{{ __('store.product.qty_label') }}</label>
                    <input type="number" name="quantity" id="quantity-input" value="{{ $minOrder }}" min="{{ $minOrder }}" class="w-full max-w-xs sm:max-w-sm lg:max-w-md rounded-xl border border-slate-300 px-4 py-3.5 lg:px-5 lg:py-4 text-base text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    @if($minOrder > 1)
                        <p class="mt-1 text-sm text-slate-500">{{ __('store.product.min_order_line', ['count' => $minOrder]) }}</p>
                    @endif
                </div>

                @if($hasVariations)
                    {{-- Varyasyon adımları: üstte, "Seçenekleri belirleyin" + timeline; son step = Beden Tablosu --}}
                    @php
                        $variationSteps = \App\Support\ProductVariationFlowSteps::topologicallySorted($product->variations);
                        $hasCustomizationRows = collect($productCustomization['rows'] ?? [])->isNotEmpty();
                        $flow = \App\Support\ProductVariationFlowSteps::build($product, $variationSteps, $hasCustomizationRows);
                        $flowSteps = $flow['steps'];
                        $customStepIndex = $flow['customization_step_index'];
                        $sizeStepIndex = $flow['size_step_index'];
                        $showProductCustomization = $flow['show_customization'] && $customStepIndex >= 0 && $hasCustomizationRows;
                        $sizeTables = $sizeTables ?? collect();
                        $sizeTablesById = $sizeTables->keyBy('id');
                        $variationPanelStepIndexByFlowKey = [];
                        $variationNameToPanelStepIndex = [];
                        $displayPanelStepCounter = 0;
                        foreach ($flowSteps as $flowKey => $flowStep) {
                            if (($flowStep['type'] ?? '') !== 'variation') {
                                $variationPanelStepIndexByFlowKey[$flowKey] = $displayPanelStepCounter;
                                $displayPanelStepCounter++;
                                continue;
                            }
                            $flowVariation = $flowStep['variation'];
                            $variationPanelStepIndexByFlowKey[$flowKey] = $displayPanelStepCounter;
                            $variationNameToPanelStepIndex[(string) $flowVariation->name] = $displayPanelStepCounter;
                            $displayPanelStepCounter++;
                        }
                        $customizationPanelStepIndex = $showProductCustomization
                            ? ($variationPanelStepIndexByFlowKey[$customStepIndex] ?? $customStepIndex)
                            : -1;
                        $sizePanelStepIndex = $sizeStepIndex >= 0
                            ? ($variationPanelStepIndexByFlowKey[$sizeStepIndex] ?? $sizeStepIndex)
                            : -1;
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
                        .product-option.label-option-card.option-selected .label-option-accent { background-color: #155fb3; }
                        .product-option.label-option-card.option-selected .label-option-radio {
                            border-color: #155fb3;
                            background-color: #155fb3;
                            box-shadow: inset 0 0 0 3px #fff;
                        }
                        .label-type-suboptions-panel {
                            scroll-margin-top: 1.25rem;
                        }
                        .label-type-suboptions-panel .label-type-sub-section {
                            border-left: 3px solid #93c5fd;
                        }
                        .label-type-suboptions-panel .label-type-custom-print-btn.option-selected,
                        .label-type-suboptions-panel .label-type-custom-print-artwork-btn.option-selected,
                        .label-type-suboptions-panel .label-type-position-btn.option-selected {
                            border-color: #155fb3;
                            background-color: #eff6ff;
                            color: #1e40af;
                            box-shadow: 0 0 0 2px rgba(21, 95, 179, 0.15);
                        }
                        .label-type-suboptions-panel .label-type-continue-btn:not(:disabled) {
                            box-shadow: 0 4px 14px rgba(21, 95, 179, 0.25);
                        }
                        .packaging-type-sticker-design-section .packaging-type-sticker-design-btn.option-selected {
                            border-color: #155fb3;
                            background-color: #eff6ff;
                            color: #1e40af;
                            box-shadow: 0 0 0 2px rgba(21, 95, 179, 0.15);
                        }
                        /* Sabit, eşit görsel seçenek kartları */
                        .variation-image-options-grid {
                            display: flex !important;
                            flex-wrap: wrap !important;
                            align-items: flex-start !important;
                            gap: 12px !important;
                        }
                        .vio-tile-wrap {
                            flex: 0 0 auto;
                            width: 140px;
                            max-width: 140px;
                        }
                        @media (min-width: 640px) {
                            .vio-tile-wrap {
                                width: 152px;
                                max-width: 152px;
                            }
                        }
                        button.product-option.vio-tile {
                            display: flex !important;
                            flex-direction: column !important;
                            align-items: stretch !important;
                            justify-content: flex-start !important;
                            width: 140px !important;
                            min-width: 140px !important;
                            max-width: 140px !important;
                            height: auto !important;
                            min-height: 0 !important;
                            max-height: none !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            border: 2px solid #d5dee9 !important;
                            border-radius: 14px !important;
                            background: #ffffff !important;
                            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06) !important;
                            overflow: hidden !important;
                            transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease !important;
                        }
                        @media (min-width: 640px) {
                            button.product-option.vio-tile {
                                width: 152px !important;
                                min-width: 152px !important;
                                max-width: 152px !important;
                            }
                        }
                        button.product-option.vio-tile:hover {
                            border-color: #4ea6ff !important;
                            box-shadow: 0 8px 20px rgba(21, 95, 179, 0.14) !important;
                            transform: translateY(-2px);
                        }
                        button.product-option.vio-tile.option-selected {
                            border-color: #155fb3 !important;
                            box-shadow: 0 0 0 2px rgba(21, 95, 179, 0.22), 0 6px 16px rgba(21, 95, 179, 0.12) !important;
                            transform: none;
                        }
                        .vio-tile-media {
                            position: relative !important;
                            display: block !important;
                            width: 100% !important;
                            height: 140px !important;
                            min-height: 140px !important;
                            max-height: 140px !important;
                            flex: 0 0 140px !important;
                            overflow: hidden !important;
                            background: #f1f5f9 !important;
                            box-sizing: border-box !important;
                        }
                        @media (min-width: 640px) {
                            .vio-tile-media {
                                height: 152px !important;
                                min-height: 152px !important;
                                max-height: 152px !important;
                                flex-basis: 152px !important;
                            }
                        }
                        .vio-tile-media img.vio-tile-img,
                        button.product-option.vio-tile img.vio-tile-img {
                            position: absolute !important;
                            top: 8px !important;
                            left: 8px !important;
                            right: auto !important;
                            bottom: auto !important;
                            width: calc(100% - 16px) !important;
                            height: calc(100% - 16px) !important;
                            max-width: none !important;
                            max-height: none !important;
                            min-width: 0 !important;
                            min-height: 0 !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            border: 0 !important;
                            border-radius: 10px !important;
                            object-fit: cover !important;
                            object-position: center center !important;
                            transform: none !important;
                            display: block !important;
                        }
                        @media (min-width: 640px) {
                            .vio-tile-media img.vio-tile-img,
                            button.product-option.vio-tile img.vio-tile-img {
                                top: 2px !important;
                                left: 2px !important;
                                width: calc(100% - 6px) !important;
                                height: calc(100% - 6px) !important;
                            }
                        }
                        .vio-tile-label {
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                            width: 100% !important;
                            height: 48px !important;
                            min-height: 48px !important;
                            max-height: 48px !important;
                            flex: 0 0 48px !important;
                            box-sizing: border-box !important;
                            padding: 6px 8px !important;
                            margin: 0 !important;
                            background: #fff !important;
                            border-top: 1px solid #e8eef5 !important;
                            font-size: 14px !important;
                            font-weight: 600 !important;
                            line-height: 1.25 !important;
                            color: #334155 !important;
                            text-align: center !important;
                            overflow: hidden !important;
                        }
                        @media (min-width: 640px) {
                            .vio-tile-label {
                                height: 50px !important;
                                min-height: 50px !important;
                                max-height: 50px !important;
                                flex-basis: 50px !important;
                                font-size: 15px !important;
                            }
                        }
                        .vio-tile-label > span {
                            display: -webkit-box !important;
                            -webkit-box-orient: vertical !important;
                            -webkit-line-clamp: 2 !important;
                            overflow: hidden !important;
                            word-break: break-word !important;
                        }
                        .mold-model-option-shell.vio-tile-shell {
                            width: 140px;
                            max-width: 140px;
                            border-radius: 14px;
                            border: 2px solid #d5dee9;
                            background: #fff;
                            overflow: hidden;
                            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
                            transition: border-color 0.15s ease, box-shadow 0.15s ease;
                        }
                        @media (min-width: 640px) {
                            .mold-model-option-shell.vio-tile-shell {
                                width: 152px;
                                max-width: 152px;
                            }
                        }
                        .mold-model-option-shell.vio-tile-shell:has(.product-option.option-selected) {
                            border-color: #155fb3;
                            box-shadow: 0 0 0 2px rgba(21, 95, 179, 0.22), 0 6px 16px rgba(21, 95, 179, 0.12);
                        }
                        .mold-model-option-shell.vio-tile-shell button.product-option.vio-tile {
                            width: 100% !important;
                            min-width: 0 !important;
                            max-width: none !important;
                            border: 0 !important;
                            border-radius: 0 !important;
                            box-shadow: none !important;
                        }
                        .mold-model-option-shell.vio-tile-shell button.product-option.vio-tile:hover {
                            transform: none;
                            box-shadow: none !important;
                        }
                        .mold-model-option-shell.vio-tile-shell:hover {
                            border-color: #4ea6ff;
                            box-shadow: 0 8px 20px rgba(21, 95, 179, 0.14);
                        }
                        .label-type-standard-wash-info:not(.hidden) {
                            animation: labelWashInfoIn 0.35s ease-out;
                        }
                        @keyframes labelWashInfoIn {
                            from { opacity: 0; transform: translateY(-6px); }
                            to { opacity: 1; transform: translateY(0); }
                        }
                        .customization-position-preview-btn {
                            scroll-margin-top: 0.5rem;
                        }
                        .customization-position-preview-btn .customization-position-preview-thumb img {
                            transition: transform 0.2s ease;
                        }
                        .customization-position-preview-btn:hover .customization-position-preview-thumb img {
                            transform: scale(1.04);
                        }
                        .customization-row-card:has(input:checked) .customization-position-preview-btn {
                            border-color: rgba(21, 95, 179, 0.28);
                            background: rgba(239, 246, 255, 0.65);
                        }
                        .customization-dim-wrap {
                            flex-wrap: nowrap;
                            min-width: 0;
                        }
                        .customization-dim-en,
                        .customization-dim-boy {
                            font-weight: 600;
                            font-size: 0.9375rem;
                            letter-spacing: 0.01em;
                            color: #0f3c6f;
                            border-width: 1px;
                            border-color: rgba(148, 163, 184, 0.75);
                            background-color: #fff;
                        }
                        .customization-row-card:has(input:checked) .customization-dim-en,
                        .customization-row-card:has(input:checked) .customization-dim-boy {
                            border-color: rgba(21, 95, 179, 0.4);
                        }
                        .customization-dim-en:focus,
                        .customization-dim-boy:focus {
                            border-color: #155fb3;
                            color: #0b2a4d;
                        }
                        .customization-dim-separator {
                            font-size: 0.875rem;
                            font-weight: 600;
                            color: #64748b;
                        }
                        #product-customization-table .customization-row-body {
                            display: flex;
                            flex-wrap: nowrap;
                            align-items: center;
                            gap: 0.75rem;
                            min-width: 0;
                        }
                        #product-customization-table .customization-row-body > * {
                            min-width: 0;
                        }
                        #product-customization-table .customization-col-position {
                            flex: 1.35 1 0;
                            min-width: 9.5rem;
                        }
                        #product-customization-table .customization-col-dims {
                            flex: 0 0 auto;
                        }
                        #product-customization-table .customization-col-print {
                            flex: 1.1 1 0;
                            min-width: 8.5rem;
                        }
                        #product-customization-table .customization-col-colors {
                            flex: 0 0 4.75rem;
                        }
                        #product-customization-table .customization-header-row {
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            min-width: 0;
                        }
                        #product-customization-table .customization-header-row .customization-col-position { flex: 1.35 1 0; min-width: 9.5rem; }
                        #product-customization-table .customization-header-row .customization-col-dims { flex: 0 0 auto; width: 9.75rem; text-align: center; }
                        #product-customization-table .customization-header-row .customization-col-print { flex: 1.1 1 0; min-width: 8.5rem; }
                        #product-customization-table .customization-header-row .customization-col-colors { flex: 0 0 4.75rem; }
                        @media (max-width: 639px) {
                            #product-customization-table .customization-row-body {
                                flex-wrap: wrap;
                                gap: 0.65rem 0.75rem;
                            }
                            #product-customization-table .customization-col-position {
                                flex: 1 1 100%;
                                min-width: 0;
                            }
                            #product-customization-table .customization-col-dims,
                            #product-customization-table .customization-col-print,
                            #product-customization-table .customization-col-colors {
                                flex: 1 1 auto;
                                min-width: 0;
                            }
                            #product-customization-table .customization-col-dims {
                                flex: 0 0 auto;
                            }
                            #product-customization-table .customization-col-colors {
                                flex: 0 0 4.5rem;
                            }
                        }
                        .customization-summary-metric-dim .customization-summary-dim-value {
                            font-size: 1.0625rem;
                            font-weight: 800;
                            color: #0f3c6f;
                            letter-spacing: 0.01em;
                        }
                        #mold-model-size-table-zoom-viewport {
                            touch-action: none;
                            min-height: 12rem;
                        }
                        #mold-model-size-table-zoom-viewport.is-zoomed {
                            cursor: grab;
                        }
                        #mold-model-size-table-zoom-viewport.is-dragging {
                            cursor: grabbing;
                        }
                        #mold-model-size-table-modal-image {
                            transform-origin: center center;
                            will-change: transform;
                            transition: transform 0.12s ease-out;
                        }
                        #mold-model-size-table-zoom-viewport.is-dragging #mold-model-size-table-modal-image {
                            transition: none;
                        }
                        .variation-customization-panel .variation-step-summary-value {
                            line-height: 1.5;
                        }
                        .variation-step-panel.variation-step-locked {
                            opacity: 0.55;
                        }
                        .variation-step-panel.variation-step-locked .variation-step-card {
                            pointer-events: none;
                        }
                        .variation-step-panel.variation-step-locked .variation-step-dot {
                            cursor: not-allowed;
                        }
                    </style>
                    @endpush
                    <section class="mt-3 lg:mt-4 w-full" aria-labelledby="variations-heading">
                        <div class="rounded-2xl border border-slate-200/70 bg-gradient-to-b from-white to-slate-50/60 shadow-sm overflow-hidden ring-1 ring-slate-200/30">
                            <div class="px-4 sm:px-5 lg:px-6 py-3.5 lg:py-4 border-b border-slate-200/70 bg-white/95">
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                <h2 id="variations-heading" class="text-lg sm:text-xl lg:text-2xl font-semibold text-slate-800 tracking-tight flex items-center gap-2.5">
                                    <span class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    </span>
                                    {{ __('store.product.variations_heading') }}
                                </h2>
                                        <p class="mt-1 text-sm sm:text-base text-slate-500 leading-snug">{{ __('store.product.variations_subtitle') }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2 rounded-xl border border-slate-200 bg-slate-50 p-1.5" role="tablist" aria-label="{{ __('store.product.order_mode_tabs') }}">
                                        <button type="button" class="order-mode-tab rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition-colors bg-primary-600 text-white" data-order-mode="detailed" role="tab" aria-selected="true">{{ __('store.product.order_mode_detailed') }}</button>
                                        <button type="button" class="order-mode-tab rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-white" data-order-mode="quick" role="tab" aria-selected="false">{{ __('store.product.order_mode_quick') }}</button>
                                    </div>
                                </div>
                            </div>
                            <div id="order-mode-detailed-panel" class="order-mode-panel">
                            <div id="product-variations" class="variation-steps-container px-3.5 sm:px-5 lg:px-6 py-4 lg:py-5" data-customization-step-index="{{ $customizationPanelStepIndex }}" data-size-step-index="{{ $sizePanelStepIndex }}" data-customization-enabled="{{ $showProductCustomization ? '1' : '0' }}" data-customization-depends-key="{{ \App\Support\ProductVariationFlowSteps::CUSTOMIZATION_DEPENDS_ON }}">
                                @foreach($flowSteps as $stepIndex => $step)
                                @if($step['type'] === 'variation')
                                    @php
                                        $variation = $step['variation'];
                                        $dependsOnName = trim((string) ($variation->depends_on ?? ''));
                                        $isDependent = $dependsOnName !== '';
                                        $panelStepIndex = $variationPanelStepIndexByFlowKey[$stepIndex] ?? $stepIndex;
                                        $hasVariationInfoText = filled($variation->info_text);
                                    @endphp
                                    <div class="product-variation-block variation-step-panel flex flex-row gap-0 {{ $loop->first ? '' : 'mt-3 lg:mt-4' }} {{ $isDependent ? 'dependent-variation-block variation-step-locked' : '' }}"
                                         data-variation-name="{{ $variation->name }}"
                                         data-variation-type="{{ $variation->type }}"
                                         data-depends-on="{{ $dependsOnName }}"
                                         data-depends-on-option-ids="{{ json_encode($variation->getDependsOnOptionIdsList()) }}"
                                         data-step-index="{{ $panelStepIndex }}"
                                         data-replace-main-gallery="{{ $variation->replace_main_gallery_image ? '1' : '0' }}"
                                         data-allows-multiple="{{ $variation->allows_multiple ? '1' : '0' }}"
                                         data-multi-confirmed="0"
                                         @if($variation->type === 'size_table') data-size-table-confirmed="0" @endif
                                         @if($variation->type === 'label_type') data-label-options-confirmed="0" data-label-sub-flow-active="0" data-label-queue-index="0" data-label-sub-payloads="{}" @endif
                                         @if($variation->type === 'packaging_type') data-packaging-options-confirmed="0" @endif
                                         data-step-unlocked="{{ $isDependent ? '0' : '1' }}">
                                        <div class="variation-timeline-cell flex flex-col items-center w-10 sm:w-11 shrink-0 pt-3 sm:pt-3.5">
                                            <span class="variation-step-num flex h-7 w-7 sm:h-8 sm:w-8 shrink-0 items-center justify-center rounded-full text-xs sm:text-sm font-bold ring-2 ring-white sm:ring-4 bg-slate-200 text-slate-600 shadow-sm z-10 transition-colors duration-300">{{ $panelStepIndex + 1 }}</span>
                                            <div class="w-0.5 flex-1 min-h-[6px] -mt-0.5 -mb-4 pb-4 bg-slate-200 rounded-full self-center" aria-hidden="true"></div>
                                        </div>
                                        <div class="variation-step-card flex-1 min-w-0 rounded-xl border border-slate-200/90 bg-white overflow-hidden transition-all duration-300 -ml-px shadow-sm">
                                            <button type="button" class="variation-step-dot w-full flex flex-row items-center gap-2.5 text-left py-3 sm:py-3.5 px-4 sm:px-5 bg-slate-50/90 hover:bg-slate-100/80 border-b border-slate-100/90 transition-colors {{ $panelStepIndex === 0 ? 'bg-primary-50/90 border-primary-100/80' : '' }} focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-inset" data-step="{{ $panelStepIndex }}" aria-label="{{ __('store.product.variation_pick_aria', ['name' => $variation->display_name]) }}">
                                                <span class="variation-step-name flex flex-wrap items-center gap-2 text-sm sm:text-base font-semibold text-slate-800">
                                                    <span>{{ $variation->display_name }}</span>
                                                    @if($variation->type === 'color' && $variation->options->count() > 0)
                                                        <span class="variation-step-option-count inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">0 {{ __('store.product.customization_colors_unit') }}</span>
                                                    @endif
                                                </span>
                                                <span class="variation-step-check hidden shrink-0 text-emerald-600 ml-auto" aria-hidden="true"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>
                                            </button>
                                            <div class="variation-step-summary hidden flex items-center justify-between gap-2 px-4 sm:px-5 py-2.5 sm:py-3 bg-slate-50/70 border-b border-slate-100/90">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="text-slate-500 text-sm">{{ $variation->display_name }}:</span>
                                                    <span class="variation-step-summary-value font-medium text-slate-800">—</span>
                                                </div>
                                                <button type="button" class="variation-step-change-btn text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('store.product.change') }}</button>
                                            </div>
                                            <div class="variation-step-full p-3.5 sm:p-4 lg:px-5 lg:py-4 {{ $panelStepIndex > 0 ? 'hidden' : '' }}">
                                                @if($hasVariationInfoText)
                                                    <div class="mb-3 flex justify-end">
                                                        @include('store.partials.variation-detail-info-btn', [
                                                            'title' => $variation->display_name,
                                                            'text' => $variation->info_text,
                                                            'inline' => true,
                                                        ])
                                                    </div>
                                                @endif
                                                @include('store.partials.product-variation-fields')
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
                                @elseif($step['type'] === 'customization' && $showProductCustomization)
                                @php $customizationPanelStepIndex = $variationPanelStepIndexByFlowKey[$stepIndex] ?? $stepIndex; @endphp
                                {{-- Ürün özelleştirme (zorunlu adım) --}}
                                <div class="variation-step-panel variation-customization-panel flex flex-row gap-0 mt-3 lg:mt-4"
                                     data-step-index="{{ $customizationPanelStepIndex }}"
                                     data-customization-panel="1"
                                     data-customization-confirmed="0"
                                     data-step-unlocked="1"
                                     data-variation-name="{{ \App\Support\ProductVariationFlowSteps::CUSTOMIZATION_DEPENDS_ON }}">
                                    <div class="variation-timeline-cell flex flex-col items-center w-10 sm:w-11 shrink-0 pt-3 sm:pt-3.5">
                                        <span class="variation-step-num flex h-7 w-7 sm:h-8 sm:w-8 shrink-0 items-center justify-center rounded-full text-xs sm:text-sm font-bold ring-2 ring-white sm:ring-4 bg-slate-200 text-slate-600 shadow-sm z-10 transition-colors duration-300">{{ $customizationPanelStepIndex + 1 }}</span>
                                    </div>
                                    <div class="variation-step-card flex-1 min-w-0 rounded-xl border border-slate-200/90 bg-white overflow-hidden transition-all duration-300 -ml-px shadow-sm">
                                        <button type="button" class="variation-step-dot w-full flex flex-row items-center gap-2.5 text-left py-3 sm:py-3.5 px-4 sm:px-5 bg-slate-50/90 hover:bg-slate-100/80 border-b border-slate-100/90 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-inset" data-step="{{ $customizationPanelStepIndex }}" aria-label="{{ __('store.product.customize_product') }}">
                                            <span class="variation-step-name text-sm sm:text-base font-semibold text-slate-800">{{ __('store.product.customize_product') }}</span>
                                            <span class="variation-step-check hidden shrink-0 text-emerald-600 ml-auto" aria-hidden="true"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>
                                        </button>
                                        <div class="variation-step-summary hidden flex items-start justify-between gap-3 px-4 sm:px-5 py-2.5 sm:py-3 bg-slate-50/70 border-b border-slate-100/90">
                                            <div class="variation-step-summary-value min-w-0 flex-1 text-sm text-slate-700">—</div>
                                            <button type="button" class="variation-step-change-btn shrink-0 text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('store.product.change') }}</button>
                                        </div>
                                        <div class="variation-step-full customization-step-full p-3.5 sm:p-4 lg:px-5 lg:py-4 hidden">
                                            <p class="text-sm text-slate-600 leading-snug mb-4">{{ __('store.product.customization_step_intro') }}</p>
                                            <div id="customization-fields-wrap" class="transition-opacity duration-200">
                                            <p class="text-sm font-semibold text-slate-800 mb-2">{{ __('store.product.customization_table_caption') }}</p>
                                            @php
                                                $customizationPrintOptions = $productCustomization['print_techniques'] ?? [];
                                                $customizationDefaultPrint = (string) ($productCustomization['default_print_slug'] ?? 'emprime');
                                                $customizationMaxColors = max(1, (int) ($productCustomization['max_color_count'] ?? 7));
                                                $printTechniqueSlugCanonical = \App\Support\PrintTechniqueSlugResolver::canonicalMapForStoreSlugs($customizationPrintOptions);
                                            @endphp
                                            <div id="product-customization-table" class="mb-5 space-y-2">
                                                <div class="hidden items-center gap-3 text-left sm:flex sm:px-1 sm:py-1.5">
                                                    <div class="flex w-9 shrink-0 justify-center" aria-hidden="true">
                                                        <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">{{ __('store.product.customization_col_select') }}</span>
                                                    </div>
                                                    <div class="customization-header-row min-w-0 flex-1">
                                                        <div class="customization-col-position text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('store.product.customization_col_position') }}</div>
                                                        <div class="customization-col-dims text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('store.product.customization_col_dimensions') }}</div>
                                                        <div class="customization-col-print text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('store.product.customization_col_print') }}</div>
                                                        <div class="customization-col-colors customization-colors-header text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('store.product.customization_col_colors') }}</div>
                                                    </div>
                                                </div>
                                                @foreach ($productCustomization['rows'] ?? [] as $customRow)
                                                    @php
                                                        $rowId = is_object($customRow) ? ($customRow->id ?? null) : ($customRow['id'] ?? null);
                                                        $clKonum = is_object($customRow) ? ($customRow->position_name ?? '') : ($customRow['position_name'] ?? '');
                                                        $clKonumLabel = is_object($customRow)
                                                            ? $customRow->localized_position_name
                                                            : \App\Support\CatalogLabelTranslator::label($clKonum);
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
                                                        $rowDefaultPrintCanonical = \App\Support\PrintTechniqueSlugResolver::canonical($rowDefaultPrint);
                                                        $rowPositionImage = is_object($customRow) ? ($customRow->position_image ?? null) : ($customRow['position_image'] ?? null);
                                                        $rowPositionImageUrl = filled($rowPositionImage) ? \App\Support\MediaUrl::public($rowPositionImage) : '';
                                                    @endphp
                                                    <label class="customization-row-card flex w-full max-w-full cursor-pointer items-center gap-2.5 sm:gap-3 has-[input:checked]:[&_.customization-konum-text]:text-primary-800" data-konum="{{ $clKonum }}">
                                                        <input type="checkbox" name="product_customization_row[]" value="{{ $rowId }}" class="peer sr-only customization-row-check" aria-label="{{ __('store.product.customization_row_check_aria') }} — {{ $clKonumLabel }}">
                                                        <span class="pointer-events-none relative flex h-9 w-9 shrink-0 items-center justify-center self-center rounded-full border-2 border-slate-300 bg-white transition-all duration-200 peer-checked:border-primary-500 peer-checked:bg-primary-500 peer-checked:[&_svg]:opacity-100 peer-checked:[&_svg]:scale-100" aria-hidden="true">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 text-white opacity-0 transition-all duration-200 scale-75"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        </span>
                                                        <div class="min-w-0 flex-1 rounded-xl border border-slate-200/90 bg-white px-3 py-2.5 transition-all duration-200 hover:border-slate-300 peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary-500 peer-checked:border-primary-400 peer-checked:bg-primary-50/40 peer-checked:ring-1 peer-checked:ring-primary-400/25">
                                                            <div class="customization-row-body">
                                                                <div class="customization-col-position flex min-w-0 items-center">
                                                                    @if($rowPositionImageUrl !== '')
                                                                        <button type="button"
                                                                            class="customization-position-preview-btn group relative z-[1] flex w-full min-w-0 items-center gap-2.5 overflow-hidden rounded-lg border border-slate-200/80 bg-slate-50/60 px-2 py-1.5 text-left transition-colors duration-200 hover:border-slate-300 hover:bg-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                                                                            data-image-url="{{ $rowPositionImageUrl }}"
                                                                            data-image-title="{{ $clKonumLabel }}"
                                                                            data-image-alt="{{ $clKonumLabel }}"
                                                                            aria-label="{{ __('store.product.customization_position_inspect_aria', ['position' => $clKonum]) }}">
                                                                            <span class="customization-position-preview-thumb relative flex h-10 w-10 shrink-0 overflow-hidden rounded-md bg-white ring-1 ring-slate-200/80">
                                                                                <img src="{{ $rowPositionImageUrl }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
                                                                                <span class="absolute inset-0 flex items-center justify-center bg-slate-900/25 opacity-0 transition-opacity group-hover:opacity-100" aria-hidden="true">
                                                                                    <svg class="h-3.5 w-3.5 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                                                                </span>
                                                                            </span>
                                                                            <span class="min-w-0 flex-1">
                                                                                <span class="customization-konum-text block truncate text-sm font-semibold leading-snug text-slate-800 transition-colors group-hover:text-slate-900">{{ $clKonumLabel }}</span>
                                                                                <span class="mt-0.5 hidden text-[11px] font-medium text-slate-500 sm:block">{{ __('store.product.customization_position_inspect') }}</span>
                                                                            </span>
                                                                        </button>
                                                                    @else
                                                                        <span class="customization-konum-text block text-sm font-semibold leading-snug text-slate-700">{{ $clKonumLabel }}</span>
                                                                    @endif
                                                                </div>
                                                                <div class="customization-col-dims flex items-center">
                                                                    <div class="customization-dim-wrap flex items-center gap-1.5">
                                                                        <span class="sr-only">{{ __('store.product.customization_dim_en') }}</span>
                                                                        <input type="number" inputmode="decimal" min="0.01" step="any" autocomplete="off" value="{{ $defEn }}" data-default="{{ $defEn }}" class="customization-dim-en h-10 w-[4.25rem] shrink-0 rounded-lg px-1.5 text-center tabular-nums leading-none transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500/25" aria-label="{{ __('store.product.customization_dim_en') }}, {{ $clKonumLabel }}">
                                                                        <span class="customization-dim-separator flex h-10 shrink-0 select-none items-center justify-center px-0.5" aria-hidden="true">×</span>
                                                                        <span class="sr-only">{{ __('store.product.customization_dim_boy') }}</span>
                                                                        <input type="number" inputmode="decimal" min="0.01" step="any" autocomplete="off" value="{{ $defBoy }}" data-default="{{ $defBoy }}" class="customization-dim-boy h-10 w-[4.25rem] shrink-0 rounded-lg px-1.5 text-center tabular-nums leading-none transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500/25" aria-label="{{ __('store.product.customization_dim_boy') }}, {{ $clKonumLabel }}">
                                                                    </div>
                                                                </div>
                                                                <div class="customization-col-print flex items-center">
                                                                    <select name="customization_row_{{ $rowId }}_baski" data-default-baski="{{ $rowDefaultPrint }}" class="customization-print-tech h-10 w-full min-w-0 rounded-lg border border-slate-300/90 bg-white px-2.5 text-sm text-slate-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" aria-label="{{ __('store.product.customization_col_print') }}, {{ $clKonumLabel }}">
                                                                        @foreach ($customizationPrintOptions as $pSlug => $pLabel)
                                                                            <option value="{{ $pSlug }}" {{ $pSlug === $rowDefaultPrint ? 'selected' : '' }}>{{ $pLabel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="customization-col-colors customization-color-field flex items-center {{ $rowDefaultPrintCanonical !== 'emprime' ? 'invisible pointer-events-none' : '' }}">
                                                                    <select name="customization_row_{{ $rowId }}_renk" data-default-renk="{{ $defRenk }}" class="customization-color-count h-10 w-full min-w-0 rounded-lg border border-slate-300/90 bg-white px-2 text-center text-sm text-slate-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" aria-label="{{ __('store.product.customization_col_colors') }}, {{ $clKonumLabel }}" @disabled($rowDefaultPrintCanonical !== 'emprime')>
                                                                        @for ($ci = 1; $ci <= $customizationMaxColors; $ci++)
                                                                            <option value="{{ $ci }}" {{ (int) $ci === (int) $defRenk ? 'selected' : '' }}>{{ $ci }}</option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <label for="product-customization-notes-field" class="block text-sm font-semibold text-slate-800 mb-2">{{ __('store.product.customization_panel_title') }}</label>
                                            <textarea id="product-customization-notes-field" rows="3" maxlength="2000" class="w-full rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20" placeholder="{{ __('store.product.customization_notes_placeholder') }}"></textarea>
                                            <p class="mt-2 text-xs text-slate-500">{{ __('store.product.customization_notes_footer') }}</p>
                                            </div>
                                            <label for="customization-skip-checkbox" class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl border-2 border-slate-200/90 bg-slate-50/80 p-4 transition-colors hover:border-primary-300 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/60">
                                                <input type="checkbox" id="customization-skip-checkbox" class="customization-skip-check mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-primary-600 focus:ring-primary-500/30">
                                                <span class="text-sm font-semibold leading-snug text-slate-800">{{ __('store.product.skip_customization') }}</span>
                                            </label>
                                            <button type="button" id="customization-continue-btn" disabled class="mt-4 w-full py-3 sm:py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                                {{ __('store.product.variation_continue') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            </div>
                            </div>
                            <div id="order-mode-quick-panel" class="order-mode-panel hidden px-3.5 sm:px-5 lg:px-6 py-4 lg:py-5">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                    <p class="text-sm font-semibold text-slate-800">{{ __('store.product.quick_order_summary') }}</p>
                                    <p class="mt-2 text-sm text-slate-600">{{ __('store.product.quick_order_intro') }}</p>
                                    <label for="quick-order-notes" class="mt-4 block text-sm font-semibold text-slate-800">{{ __('store.product.quick_order_notes_label') }}</label>
                                    <textarea id="quick-order-notes" name="quick_order_notes" rows="6" maxlength="4000" class="mt-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20" placeholder="{{ __('store.product.quick_order_notes_placeholder') }}"></textarea>
                                    <p class="mt-2 text-xs text-slate-500">{{ __('store.product.quick_order_notes_placeholder') }}</p>
                                    <label for="quick-order-image" class="mt-4 block text-sm font-semibold text-slate-800">{{ __('store.product.quick_order_image_label') }}</label>
                                    <input id="quick-order-image" name="quick_order_image" type="file" accept="image/png,image/jpeg,image/jpg" class="mt-2 block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-primary-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-700">
                                    <p class="mt-2 text-xs text-slate-500">{{ __('store.product.quick_order_image_help') }}</p>
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
                                    <span id="product-price" data-base-try="{{ $baseTry }}" data-price-tiers='@json($priceTiersForStore)' @if($hasProductDiscount && $normalPriceTry !== null) data-normal-try="{{ $normalPriceTry }}" @endif data-exchange-rate="{{ (float) $selectedCurrency->exchange_rate }}" data-currency-code="{{ $selectedCurrency->code }}" data-currency-symbol="{{ $selectedCurrency->symbol }}" class="text-lg font-bold text-slate-900">
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

            {{-- Kumaş detay bilgisi modal --}}
            <div id="fabric-detail-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4" aria-modal="true" role="dialog" aria-labelledby="fabric-detail-modal-title">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" id="fabric-detail-modal-backdrop"></div>
                <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200/80 overflow-hidden transform transition-all">
                    <div class="flex items-start gap-3 border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                        <span class="mt-0.5 shrink-0 text-sky-600" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <h2 id="fabric-detail-modal-title" class="min-w-0 flex-1 text-base sm:text-lg font-semibold text-slate-900 leading-snug"></h2>
                        <button type="button" id="fabric-detail-modal-close-icon" class="shrink-0 rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-primary-400/40" aria-label="{{ __('store.product.lightbox_close') }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div id="fabric-detail-modal-body" class="px-5 py-4 sm:px-6 sm:py-5 text-sm sm:text-base text-slate-700 leading-relaxed whitespace-pre-line max-h-[min(60vh,28rem)] overflow-y-auto"></div>
                    <div class="px-5 pb-5 sm:px-6 sm:pb-6">
                        <button type="button" id="fabric-detail-modal-close" class="w-full py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            {{ __('store.product.zoom_ok') }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Konum görseli modal --}}
            <div id="customization-position-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4" aria-modal="true" role="dialog" aria-labelledby="customization-position-modal-title">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" id="customization-position-modal-backdrop"></div>
                <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200/80 overflow-hidden transform transition-all">
                    <div class="flex items-start gap-3 border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                        <span class="mt-0.5 shrink-0 text-primary-600" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <h2 id="customization-position-modal-title" class="min-w-0 flex-1 text-base sm:text-lg font-semibold text-slate-900 leading-snug"></h2>
                        <button type="button" id="customization-position-modal-close-icon" class="shrink-0 rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-primary-400/40" aria-label="{{ __('store.product.lightbox_close') }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="px-5 py-4 sm:px-6 sm:py-5">
                        <img id="customization-position-modal-image" src="" alt="" class="mx-auto max-h-[min(65vh,32rem)] w-auto max-w-full rounded-xl object-contain bg-slate-50">
                    </div>
                    <div class="px-5 pb-5 sm:px-6 sm:pb-6">
                        <button type="button" id="customization-position-modal-close" class="w-full py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            {{ __('store.product.zoom_ok') }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Kalıp modeli beden tablosu modal --}}
            <div id="mold-model-size-table-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4" aria-modal="true" role="dialog" aria-labelledby="mold-model-size-table-modal-title">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" id="mold-model-size-table-modal-backdrop"></div>
                <div class="relative w-full max-w-3xl rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200/80 overflow-hidden transform transition-all">
                    <div class="flex items-start gap-3 border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                        <span class="mt-0.5 shrink-0 text-primary-600" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18"/></svg>
                        </span>
                        <h2 id="mold-model-size-table-modal-title" class="min-w-0 flex-1 text-base sm:text-lg font-semibold text-slate-900 leading-snug"></h2>
                        <button type="button" id="mold-model-size-table-modal-close-icon" class="shrink-0 rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-primary-400/40" aria-label="{{ __('store.product.lightbox_close') }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="px-5 py-4 sm:px-6 sm:py-5">
                        <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">{{ __('store.product.mold_model_size_table_zoom_hint') }}</p>
                            <div class="flex shrink-0 items-center gap-1.5 self-start sm:self-auto">
                                <button type="button" id="mold-model-size-table-zoom-out" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 bg-white text-lg font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary-400/40" aria-label="{{ __('store.product.mold_model_size_table_zoom_out') }}">−</button>
                                <span id="mold-model-size-table-zoom-level" class="min-w-[3.25rem] text-center text-xs font-semibold tabular-nums text-slate-600">100%</span>
                                <button type="button" id="mold-model-size-table-zoom-in" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 bg-white text-lg font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary-400/40" aria-label="{{ __('store.product.mold_model_size_table_zoom_in') }}">+</button>
                                <button type="button" id="mold-model-size-table-zoom-reset" class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-300 bg-white px-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary-400/40">{{ __('store.product.mold_model_size_table_zoom_reset') }}</button>
                            </div>
                        </div>
                        <div id="mold-model-size-table-zoom-viewport" class="relative overflow-hidden rounded-xl border border-slate-200/80 bg-slate-50 max-h-[min(70vh,36rem)]">
                            <img id="mold-model-size-table-modal-image" src="" alt="" class="mx-auto block max-h-[min(70vh,36rem)] w-auto max-w-full select-none object-contain">
                        </div>
                    </div>
                    <div class="px-5 pb-5 sm:px-6 sm:pb-6">
                        <button type="button" id="mold-model-size-table-modal-close" class="w-full py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
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
                'color_count_badge' => __('store.product.color_count_badge'),
                'summary_pricing_weight' => __('store.product.summary_pricing_weight'),
                'summary_size_weight_formula' => __('store.product.summary_size_weight_formula'),
                'summary_product_base_price' => __('store.product.summary_product_base_price'),
                'summary_qty_multiplier' => __('store.product.summary_qty_multiplier'),
                'summary_qty_unit_price' => __('store.product.summary_qty_unit_price'),
                'summary_qty_for_tier' => __('store.product.summary_qty_for_tier'),
                'summary_variation_mult_total' => __('store.product.summary_variation_mult_total'),
                'summary_packaging_extra' => __('store.product.summary_packaging_extra'),
                'summary_print_total' => __('store.product.summary_print_total'),
                'summary_print_order_total' => __('store.product.summary_print_order_total'),
                'summary_print_times_qty' => __('store.product.summary_print_times_qty'),
                'summary_section_choices' => __('store.product.summary_section_choices'),
                'summary_section_quantities' => __('store.product.summary_section_quantities'),
                'summary_section_pricing' => __('store.product.summary_section_pricing'),
                'summary_unit_from_total' => __('store.product.summary_unit_from_total'),
                'summary_unit_from_total_note' => __('store.product.summary_unit_from_total_note'),
                'summary_price_calc' => __('store.product.summary_price_calc'),
                'summary_price_formula' => __('store.product.summary_price_formula'),
                'summary_price_formula_with_pack' => __('store.product.summary_price_formula_with_pack'),
                'summary_line_total' => __('store.product.summary_line_total'),
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
                'customization_total_price_formula_no_color' => __('store.product.customization_total_price_formula_no_color'),
                'customization_section_grand_total' => __('store.product.customization_section_grand_total'),
                'customization_print_order_formula' => __('store.product.customization_print_order_formula'),
                'customization_print_order_hint' => __('store.product.customization_print_order_hint'),
                'customization_notes_label' => __('store.product.customization_panel_title'),
                'skip_customization' => __('store.product.skip_customization'),
                'warn_customization_dims_title' => __('store.product.warn_customization_dims_title'),
                'warn_customization_dims_desc' => __('store.product.warn_customization_dims_desc'),
                'label_custom_print_summary_yes' => __('store.product.label_custom_print_summary_yes'),
                'label_custom_print_summary_no' => __('store.product.label_custom_print_summary_no'),
                'label_custom_print_artwork_summary_customer' => __('store.product.label_custom_print_artwork_summary_customer'),
                'label_custom_print_artwork_summary_company' => __('store.product.label_custom_print_artwork_summary_company'),
                'label_position_front' => __('store.product.label_position_front'),
                'label_position_back' => __('store.product.label_position_back'),
                'label_position_summary' => __('store.product.label_position_summary'),
                'label_subflow_heading' => __('store.product.label_subflow_heading'),
                'label_suboptions_panel_hint' => __('store.product.label_suboptions_panel_hint'),
                'label_description_placeholder' => __('store.product.label_description_placeholder'),
                'label_description_default_title' => __('store.product.label_description_default_title'),
                'label_description_summary' => __('store.product.label_description_summary'),
                'packaging_material_pick' => __('store.product.packaging_material_pick'),
                'packaging_customization_pick' => __('store.product.packaging_customization_pick'),
                'packaging_material_summary' => __('store.product.packaging_material_summary'),
                'packaging_barcode_summary_yes' => __('store.product.packaging_barcode_summary_yes'),
                'delivery_suboption_summary' => __('store.product.delivery_suboption_summary'),
                'delivery_estimated_time_title' => __('store.product.delivery_estimated_time_title'),
                'delivery_estimated_time_panel_prefix' => __('store.product.delivery_estimated_time_panel_prefix'),
            ];
            $dimensionMultipliersByPrint = $dimensionMultipliersByPrint ?? [];
            $printTechniqueSlugCanonical = $printTechniqueSlugCanonical
                ?? \App\Support\PrintTechniqueSlugResolver::canonicalMapForStoreSlugs($productCustomization['print_techniques'] ?? []);
            $usdCurrency = ($currencies ?? collect())->firstWhere('code', 'USD');
            $storeCurrencyConfig = [
                'canSeePrices' => (bool) ($canSeePrices ?? false),
                'usdExchangeRate' => $usdCurrency ? (float) $usdCurrency->exchange_rate : null,
                'selectedCode' => $selectedCurrency->code ?? 'TRY',
                'selectedSymbol' => $selectedCurrency->symbol ?? '₺',
                'selectedExchangeRate' => (float) ($selectedCurrency->exchange_rate ?? 1),
                'selectedDecimalPlaces' => (int) ($selectedCurrency->decimal_places ?? 2),
            ];
            $storeLocaleBcp47 = match (app()->getLocale()) {
                'en' => 'en-US',
                'it' => 'it-IT',
                default => 'tr-TR',
            };
            $storeCatalogLabels = [];
            foreach ((array) config('catalog_labels.labels', []) as $src => $rows) {
                $locale = app()->getLocale();
                $translated = (string) $src;
                if ($locale === 'en' && ! empty($rows['en'])) {
                    $translated = (string) $rows['en'];
                } elseif ($locale === 'it') {
                    $translated = (string) ($rows['it'] ?? $rows['en'] ?? $src);
                }
                $storeCatalogLabels[(string) $src] = $translated;
                $storeCatalogLabels[mb_strtolower((string) $src, 'UTF-8')] = $translated;
            }
        @endphp
        <script>window.storeLocale = @json($storeLocaleBcp47);</script>
        <script>window.storeCatalogLabels = @json($storeCatalogLabels);</script>
        <script>window.storeProductUi = @json($storeProductUi);</script>
        <script>window.packagingCatalog = @json($packagingCatalog ?? []);</script>
        <script>window.deliveryCatalog = @json($deliveryCatalog ?? []);</script>
        <script>window.dimensionMultipliersByPrint = @json($dimensionMultipliersByPrint);</script>
        <script>window.printTechniqueSlugCanonical = @json($printTechniqueSlugCanonical);</script>
        <script>window.storeCurrencyConfig = @json($storeCurrencyConfig);</script>
        @push('head')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var PU = window.storeProductUi || {};
                    function optionDisplayLabel(el) {
                        if (!el) return '';
                        var label = (el.getAttribute('data-option-label') || '').trim();
                        if (label) return label;
                        return (el.getAttribute('data-option') || '').trim();
                    }
                    function catalogLabel(text) {
                        if (!text) return '';
                        var map = window.storeCatalogLabels || {};
                        if (Object.prototype.hasOwnProperty.call(map, text)) return map[text];
                        var key = String(text).toLocaleLowerCase('tr-TR');
                        if (Object.prototype.hasOwnProperty.call(map, key)) return map[key];
                        return text;
                    }

                    var variationInput = document.getElementById('variation-data-input');
                    var orderModeInput = document.getElementById('order-mode-input');
                    var orderModeTabs = document.querySelectorAll('.order-mode-tab');
                    var orderModePanels = document.querySelectorAll('.order-mode-panel');
                    var quickOrderNotesInput = document.getElementById('quick-order-notes');
                    var quickOrderImageInput = document.getElementById('quick-order-image');
                    if (!variationInput) return;

                    function getQuickOrderInputState() {
                        var notes = quickOrderNotesInput ? (quickOrderNotesInput.value || '').trim() : '';
                        var hasImage = !!(quickOrderImageInput && quickOrderImageInput.files && quickOrderImageInput.files.length);
                        var hasText = notes.length > 0;
                        return {
                            hasContent: hasText || hasImage,
                            hasText: hasText,
                            hasImage: hasImage,
                        };
                    }

                    function activateOrderMode(mode) {
                        if (!orderModeInput) return;
                        var normalizedMode = mode === 'quick' ? 'quick' : 'detailed';
                        orderModeInput.value = normalizedMode;
                        orderModeTabs.forEach(function(tab) {
                            var isActive = (tab.getAttribute('data-order-mode') || '') === normalizedMode;
                            tab.classList.toggle('bg-primary-600', isActive);
                            tab.classList.toggle('text-white', isActive);
                            tab.classList.toggle('hover:bg-white', !isActive);
                            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                        });
                        orderModePanels.forEach(function(panel) {
                            var panelMode = panel.id === 'order-mode-quick-panel' ? 'quick' : 'detailed';
                            panel.classList.toggle('hidden', panelMode !== normalizedMode);
                        });
                        if (typeof updateVariationSummaryAndButton === 'function') {
                            updateVariationSummaryAndButton();
                        }
                    }

                    if (quickOrderNotesInput) {
                        quickOrderNotesInput.addEventListener('input', function() {
                            if (typeof updateVariationSummaryAndButton === 'function') {
                                updateVariationSummaryAndButton();
                            }
                        });
                    }
                    if (quickOrderImageInput) {
                        quickOrderImageInput.addEventListener('change', function() {
                            if (typeof updateVariationSummaryAndButton === 'function') {
                                updateVariationSummaryAndButton();
                            }
                        });
                    }

                    orderModeTabs.forEach(function(tab) {
                        tab.addEventListener('click', function() {
                            activateOrderMode(tab.getAttribute('data-order-mode') || 'detailed');
                        });
                    });
                    activateOrderMode(orderModeInput.value || 'detailed');

                    function getVariationStepsMeta() {
                        var wrap = document.getElementById('product-variations');
                        if (!wrap) return { customizationIdx: -1, sizeIdx: -1, customizationEnabled: false };
                        var c = wrap.getAttribute('data-customization-step-index');
                        var s = wrap.getAttribute('data-size-step-index');
                        return {
                            customizationIdx: c !== null && c !== '' ? parseInt(c, 10) : -1,
                            sizeIdx: s !== null && s !== '' ? parseInt(s, 10) : -1,
                            customizationEnabled: (wrap.getAttribute('data-customization-enabled') || '') === '1'
                        };
                    }

                    var _orderedPanelsCache = null;
                    var _panelByNameCache = {};
                    var _applyDependencyChainTimer = null;

                    function invalidateVariationStepsCache() {
                        _orderedPanelsCache = null;
                        _panelByNameCache = {};
                    }

                    function rebuildVariationStepsCache() {
                        var list = [];
                        document.querySelectorAll('.variation-step-panel').forEach(function(panel) {
                            var idx = parseInt(panel.getAttribute('data-step-index'), 10);
                            if (!isNaN(idx)) list.push({ index: idx, panel: panel });
                        });
                        list.sort(function(a, b) { return a.index - b.index; });
                        _orderedPanelsCache = list;
                        _panelByNameCache = {};
                        list.forEach(function(item) {
                            var name = (item.panel.getAttribute('data-variation-name') || '').trim();
                            if (name) _panelByNameCache[name] = item.panel;
                        });
                    }

                    function getOrderedVariationStepPanels() {
                        if (!_orderedPanelsCache) rebuildVariationStepsCache();
                        return _orderedPanelsCache;
                    }

                    function getVariationPanelByName(name) {
                        if (!name) return null;
                        if (!_orderedPanelsCache) rebuildVariationStepsCache();
                        return _panelByNameCache[name] || null;
                    }

                    function getFinalVariationPanel() {
                        var ordered = getOrderedVariationStepPanels();
                        var lastVariationPanel = null;
                        for (var i = 0; i < ordered.length; i++) {
                            var panel = ordered[i].panel;
                            if ((panel.getAttribute('data-customization-panel') || '') === '1') continue;
                            if (!(panel.getAttribute('data-variation-name') || '').trim()) continue;
                            lastVariationPanel = panel;
                        }
                        return lastVariationPanel;
                    }

                    function isFinalVariationFlowTriggered() {
                        var finalPanel = getFinalVariationPanel();
                        if (!finalPanel) return false;
                        if ((finalPanel.getAttribute('data-step-unlocked') || '') !== '1') return false;
                        var finalIdx = parseInt(finalPanel.getAttribute('data-step-index'), 10);
                        if (!isNaN(finalIdx) && !prerequisitesMetForStepIndex(finalIdx)) return false;
                        return isProductVariationBlockComplete(finalPanel);
                    }

                    function hideVariationPanelsAfterFinalStep() {
                        var finalPanel = getFinalVariationPanel();
                        if (!finalPanel || !isFinalVariationFlowTriggered()) return;
                        var finalIdx = parseInt(finalPanel.getAttribute('data-step-index'), 10);
                        if (isNaN(finalIdx)) return;
                        getOrderedVariationStepPanels().forEach(function(item) {
                            if (item.index <= finalIdx) return;
                            if ((item.panel.getAttribute('data-customization-panel') || '') === '1') return;
                            item.panel.style.display = 'none';
                        });
                    }

                    function scheduleApplyDependencyChain() {
                        if (_applyDependencyChainTimer) clearTimeout(_applyDependencyChainTimer);
                        _applyDependencyChainTimer = setTimeout(function() {
                            _applyDependencyChainTimer = null;
                            applyDependencyChain();
                        }, 40);
                    }

                    function applyDependencyChainNow() {
                        if (_applyDependencyChainTimer) {
                            clearTimeout(_applyDependencyChainTimer);
                            _applyDependencyChainTimer = null;
                        }
                        applyDependencyChain();
                    }

                    function parseDependsOnOptionIdsFromBlock(block) {
                        if (!block) return [];
                        var json = block.getAttribute('data-depends-on-option-ids');
                        if (!json) return [];
                        try { return normalizeOptionIdList(JSON.parse(json) || []); } catch (e) { return []; }
                    }

                    function isParentVariationStepReady(parentPanel) {
                        if (!parentPanel) return true;
                        if ((parentPanel.getAttribute('data-customization-panel') || '') === '1') {
                            return isCustomizationStepComplete();
                        }
                        return isProductVariationBlockComplete(parentPanel);
                    }

                    function isVariationStepUnlocked(panel) {
                        if (!panel) return false;
                        var dependsOn = (panel.getAttribute('data-depends-on') || '').trim();
                        if (!dependsOn) return true;
                        if (isCustomizationDependsOn(dependsOn)) {
                            if (!isCustomizationStepComplete()) return false;
                            if (isCustomizationSkipSelected()) return true;
                            var custOptionIds = parseDependsOnOptionIdsFromBlock(panel);
                            if (custOptionIds.length === 0) return true;
                            var custIds = getSelectedCustomizationRowIds();
                            if (custIds.length === 0) return false;
                            return normalizeOptionIdList(custIds).some(function(id) {
                                return custOptionIds.indexOf(Number(id)) !== -1;
                            });
                        }
                        var parentPanel = getVariationPanelByName(dependsOn);
                        if (!parentPanel) return true;
                        if (!isParentVariationStepReady(parentPanel)) return false;
                        var dependsOnOptionIds = parseDependsOnOptionIdsFromBlock(panel);
                        if (dependsOnOptionIds.length === 0) return true;
                        var parentIds = getSelectedParentOptionIdsForVariation(dependsOn);
                        if (parentIds.length === 0) return false;
                        return normalizeOptionIdList(parentIds).some(function(id) {
                            return dependsOnOptionIds.indexOf(Number(id)) !== -1;
                        });
                    }

                    function syncVariationStepUnlockStates() {
                        document.querySelectorAll('.variation-step-panel').forEach(function(panel) {
                            var unlocked = isVariationStepUnlocked(panel);
                            panel.setAttribute('data-step-unlocked', unlocked ? '1' : '0');
                            panel.classList.toggle('variation-step-locked', !unlocked);
                            panel.style.display = '';
                        });
                    }

                    function isProductVariationBlockSelectionReady(block) {
                        if (!block) return true;
                        if (block.style.display === 'none') return true;
                        if ((block.getAttribute('data-variation-type') || '') === 'size_table') {
                            var target = resolveSizeTableTarget(block);
                            if (!target || (!target.optionVal && !target.slug)) return false;
                            return (block.getAttribute('data-size-table-confirmed') || '') === '1';
                        }
                        if ((block.getAttribute('data-variation-type') || '') === 'label_type') {
                            if (isLabelTypeMulti(block)) {
                                var labelSelections = getLabelTypeSelectedOptions(block);
                                if (!labelSelections.length) return false;
                                if ((block.getAttribute('data-multi-confirmed') || '') !== '1') return false;
                                return (block.getAttribute('data-label-options-confirmed') || '') === '1';
                            }
                            var labelSel = getVisibleSelectedProductOption(block);
                            if (!labelSel) return false;
                            if (!labelOptionNeedsSubOptions(labelSel)) return true;
                            return (block.getAttribute('data-label-options-confirmed') || '') === '1';
                        }
                        if ((block.getAttribute('data-variation-type') || '') === 'packaging_type') {
                            var packagingSel = getVisibleSelectedProductOption(block);
                            if (!packagingSel) return false;
                            return (block.getAttribute('data-packaging-options-confirmed') || '') === '1';
                        }
                        if ((block.getAttribute('data-variation-type') || '') === 'delivery_type') {
                            var deliverySel = getVisibleSelectedProductOption(block);
                            if (!deliverySel) return false;
                            return (block.getAttribute('data-delivery-options-confirmed') || '') === '1';
                        }
                        var isMulti = (block.getAttribute('data-allows-multiple') || '') === '1';
                        if (isMulti) {
                            if (countVisibleSelectedOptions(block) < 1) return false;
                            if ((block.getAttribute('data-variation-type') || '') === 'label_type' && isLabelTypeMulti(block)) {
                                return (block.getAttribute('data-label-options-confirmed') || '') === '1';
                            }
                            if ((block.getAttribute('data-multi-confirmed') || '') !== '1') return false;
                            return true;
                        }
                        var selected = getVisibleSelectedProductOption(block);
                        return !!selected;
                    }

                    function isProductVariationBlockComplete(block) {
                        if (!block) return true;
                        if (block.style.display === 'none') return true;
                        return isProductVariationBlockSelectionReady(block);
                    }

                    function finishVariationStepIfReady(block) {
                        block = resolveVariationStepPanel(block) || block;
                        if (!block) return false;
                        if (!isProductVariationBlockComplete(block)) {
                            return false;
                        }
                        var stepIdx = parseInt(block.getAttribute('data-step-index'), 10);
                        if (!isNaN(stepIdx)) {
                            currentVariationStep = stepIdx;
                        }
                        markVariationStepPanelComplete(block);
                        maybeAdvanceVariationStepAfterSelection(block);
                        return true;
                    }

                    function isVariationStepPanelComplete(panel) {
                        if (!panel) return true;
                        if ((panel.getAttribute('data-customization-panel') || '') === '1') {
                            return (panel.getAttribute('data-customization-confirmed') || '') === '1';
                        }
                        if (panel.getAttribute('data-variation-name')) {
                            return isProductVariationBlockComplete(panel);
                        }
                        return true;
                    }

                    function prerequisitesMetForStepIndex(targetIndex) {
                        var ordered = getOrderedVariationStepPanels();
                        for (var i = 0; i < ordered.length; i++) {
                            if (ordered[i].index >= targetIndex) break;
                            if ((ordered[i].panel.getAttribute('data-step-unlocked') || '') !== '1') continue;
                            if (!isVariationStepPanelComplete(ordered[i].panel)) return false;
                        }
                        return true;
                    }

                    function maxReachableVariationStepIndex() {
                        var ordered = getOrderedVariationStepPanels();
                        if (!ordered.length) return 0;
                        var max = ordered[0].index;
                        for (var i = 0; i < ordered.length; i++) {
                            var idx = ordered[i].index;
                            if (!prerequisitesMetForStepIndex(idx)) break;
                            if ((ordered[i].panel.getAttribute('data-step-unlocked') || '') !== '1') break;
                            max = idx;
                            if (!isVariationStepPanelComplete(ordered[i].panel)) break;
                        }
                        return max;
                    }

                    function computeCustomizationAreaCm2(enStr, boyStr) {
                        var en = parseFloat(String(enStr || '').replace(',', '.'));
                        var boy = parseFloat(String(boyStr || '').replace(',', '.'));
                        if (!isFinite(en) || !isFinite(boy) || en <= 0 || boy <= 0) {
                            return null;
                        }
                        return en * boy;
                    }

                    function parseCustomizationDimInputValue(inp) {
                        if (!inp) return null;
                        var raw = String(inp.value || '').trim().replace(',', '.');
                        if (raw === '') return null;
                        var n = parseFloat(raw);
                        return isFinite(n) ? n : null;
                    }

                    function isCustomizationRowDimensionsValid(card) {
                        if (!card) return true;
                        var check = card.querySelector('.customization-row-check');
                        if (!check || !check.checked) return true;
                        var en = parseCustomizationDimInputValue(card.querySelector('.customization-dim-en'));
                        var boy = parseCustomizationDimInputValue(card.querySelector('.customization-dim-boy'));
                        return en !== null && boy !== null && en > 0 && boy > 0;
                    }

                    function allCheckedCustomizationRowsHaveValidDimensions() {
                        var wrap = document.getElementById('product-customization-table');
                        if (!wrap) return true;
                        var checked = wrap.querySelectorAll('input.customization-row-check:checked');
                        if (!checked.length) return false;
                        for (var i = 0; i < checked.length; i++) {
                            var card = checked[i].closest('.customization-row-card');
                            if (!isCustomizationRowDimensionsValid(card)) return false;
                        }
                        return true;
                    }

                    function syncCustomizationDimensionFieldValidity(card) {
                        if (!card) return;
                        var check = card.querySelector('.customization-row-check');
                        var active = check && check.checked;
                        card.querySelectorAll('.customization-dim-en, .customization-dim-boy').forEach(function(inp) {
                            var invalid = false;
                            if (active) {
                                var n = parseCustomizationDimInputValue(inp);
                                invalid = n === null || n <= 0;
                            }
                            inp.classList.toggle('border-red-500', invalid);
                            inp.classList.toggle('ring-2', invalid);
                            inp.classList.toggle('ring-red-500/25', invalid);
                            inp.classList.toggle('border-slate-300/90', !invalid);
                        });
                    }

                    function syncAllCustomizationDimensionValidity() {
                        var tbl = document.getElementById('product-customization-table');
                        if (!tbl) return;
                        tbl.querySelectorAll('.customization-row-card').forEach(function(card) {
                            syncCustomizationDimensionFieldValidity(card);
                        });
                    }

                    function validateCustomizationDimensionsOrWarn() {
                        if (isCustomizationSkipSelected()) return true;
                        syncAllCustomizationDimensionValidity();
                        if (allCheckedCustomizationRowsHaveValidDimensions()) return true;
                        openProductWarningDialog(
                            PU.warn_customization_dims_title || 'Geçersiz ölçü',
                            PU.warn_customization_dims_desc || 'En ve boy 0 olamaz.'
                        );
                        return false;
                    }

                    function formatCustomizationAreaCm2(cm2) {
                        if (cm2 === null || !isFinite(cm2)) {
                            return null;
                        }
                        var isWhole = Math.abs(cm2 - Math.round(cm2)) < 1e-9;
                        return cm2.toLocaleString((window.storeLocale || 'tr-TR'), {
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

                    var COLOR_MULTIPLIER_PRINT_SLUG = 'emprime';

                    function normalizePrintTechniqueSlug(printSlug) {
                        var raw = (printSlug && String(printSlug).trim()) ? String(printSlug).trim() : 'emprime';
                        var map = window.printTechniqueSlugCanonical || {};
                        if (map[raw]) {
                            return map[raw];
                        }
                        var underscored = raw.toLowerCase().replace(/-/g, '_');
                        if (map[underscored]) {
                            return map[underscored];
                        }
                        var all = window.dimensionMultipliersByPrint || {};
                        if (all[underscored]) {
                            return underscored;
                        }
                        if (all[raw]) {
                            return raw;
                        }
                        return 'emprime';
                    }

                    function getDimensionMultipliersForPrint(printSlug) {
                        var all = window.dimensionMultipliersByPrint || {};
                        var slug = normalizePrintTechniqueSlug(printSlug);
                        if (all[slug]) {
                            return all[slug];
                        }
                        return all.emprime || { size: [], quantity: [], color: [] };
                    }

                    function printTechniqueUsesColorMultiplier(printSlug) {
                        return normalizePrintTechniqueSlug(printSlug) === COLOR_MULTIPLIER_PRINT_SLUG;
                    }

                    function syncCustomizationColorFieldForCard(card) {
                        if (!card) return;
                        var printSel = card.querySelector('.customization-print-tech');
                        var colorField = card.querySelector('.customization-color-field');
                        var colorSel = card.querySelector('.customization-color-count');
                        if (!printSel || !colorField) return;
                        var slug = String(printSel.value || '').trim() || 'emprime';
                        var showColor = printTechniqueUsesColorMultiplier(slug);
                        // Keep column width stable across rows (invisible, not display:none).
                        colorField.classList.toggle('invisible', !showColor);
                        colorField.classList.toggle('pointer-events-none', !showColor);
                        colorField.classList.remove('hidden');
                        colorField.setAttribute('aria-hidden', showColor ? 'false' : 'true');
                        if (colorSel) {
                            colorSel.disabled = !showColor;
                        }
                    }

                    function syncCustomizationColorsHeader() {
                        var tbl = document.getElementById('product-customization-table');
                        if (!tbl) return;
                        var anyVisible = false;
                        tbl.querySelectorAll('.customization-color-field').forEach(function(el) {
                            if (!el.classList.contains('invisible') && !el.classList.contains('hidden')) {
                                anyVisible = true;
                            }
                        });
                        tbl.querySelectorAll('.customization-colors-header').forEach(function(el) {
                            el.classList.toggle('invisible', !anyVisible);
                        });
                    }

                    function syncAllCustomizationColorFields() {
                        var tbl = document.getElementById('product-customization-table');
                        if (!tbl) return;
                        tbl.querySelectorAll('.customization-row-card').forEach(function(card) {
                            syncCustomizationColorFieldForCard(card);
                        });
                        syncCustomizationColorsHeader();
                    }

                    function findEbatRowForAreaCm2(cm2, printSlug) {
                        var list = getDimensionMultipliersForPrint(printSlug).size || [];
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

                    function findEbatLabelForAreaCm2(cm2, printSlug) {
                        var row = findEbatRowForAreaCm2(cm2, printSlug);
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
                        return n.toLocaleString((window.storeLocale || 'tr-TR'), {
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
                        var usesColor = printTechniqueUsesColorMultiplier(row.baski_slug_canonical || row.baski_slug);
                        var colorMult = usesColor
                            ? (row.color_multiplier_price != null && isFinite(row.color_multiplier_price) ? row.color_multiplier_price : null)
                            : 1;
                        if (qtyMult === null || colorMult === null) {
                            return { total_try: null, total_display: null, formula_display: null };
                        }
                        var totalTry = usesColor ? (baseTry * qtyMult * colorMult) : (baseTry * qtyMult);
                        var baseDisp = row.size_multiplier_price_display || formatStoreCurrencyAmount(baseTry) || '—';
                        var qtyDisp = qtyCtx.quantity_multiplier_price_display || formatDimensionMultiplierPrice(qtyMult);
                        var totalDisp = formatStoreCurrencyAmount(totalTry) || '—';
                        var formula;
                        if (usesColor) {
                            var colorDisp = row.color_multiplier_price_display || formatDimensionMultiplierPrice(colorMult);
                            var tplColor = (PU.customization_total_price_formula || ':base × :qty × :color = :total');
                            formula = tplColor
                                .replace(':base', baseDisp)
                                .replace(':qty', qtyDisp)
                                .replace(':color', colorDisp)
                                .replace(':total', totalDisp);
                        } else {
                            var tplNoColor = (PU.customization_total_price_formula_no_color || ':base × :qty = :total');
                            formula = tplNoColor
                                .replace(':base', baseDisp)
                                .replace(':qty', qtyDisp)
                                .replace(':total', totalDisp);
                        }
                        return {
                            total_try: totalTry,
                            total_display: totalDisp,
                            formula_display: formula
                        };
                    }

                    function getOrderQuantityForMultipliers() {
                        if (typeof getSizeQuantities === 'function') {
                            var info = getSizeQuantities();
                            return info && isFinite(info.total) ? info.total : 0;
                        }
                        return 0;
                    }

                    function buildQuantityContextForPrint(printSlug, orderQty) {
                        var matched = findQuantityMultiplierRowForQty(orderQty, printSlug);
                        return {
                            order_quantity: orderQty,
                            quantity_range_label: matched ? formatQuantityRangeLabel(matched) : null,
                            quantity_multiplier_price: matched ? parseFloat(matched.multiplier_price) : null,
                            quantity_multiplier_price_display: matched ? formatQuantityMultiplierPrice(parseFloat(matched.multiplier_price)) : null,
                            print_slug: printSlug || 'emprime'
                        };
                    }

                    function enrichCustomizationRowsWithTotals(rows) {
                        var orderQty = getOrderQuantityForMultipliers();
                        return (rows || []).map(function(row) {
                            var enriched = Object.assign({}, row);
                            var printSlug = normalizePrintTechniqueSlug(enriched.baski_slug || 'emprime');
                            enriched.baski_slug_canonical = printSlug;
                            var qtyCtx = buildQuantityContextForPrint(printSlug, orderQty);
                            if (!printTechniqueUsesColorMultiplier(printSlug)) {
                                enriched.color_multiplier_price = 1;
                                enriched.color_multiplier_price_display = formatDimensionMultiplierPrice(1);
                            }
                            var totals = computeCustomizationRowTotalPrice(enriched, qtyCtx);
                            enriched.quantity_multiplier_price = qtyCtx.quantity_multiplier_price;
                            enriched.quantity_multiplier_price_display = qtyCtx.quantity_multiplier_price_display;
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
                        var baski_slug = printSel ? String(printSel.value || '').trim() : '';
                        var baski_slug_canonical = normalizePrintTechniqueSlug(baski_slug);
                        var ebatRow = findEbatRowForAreaCm2(alanCm2, baski_slug_canonical);
                        var ebat = ebatRow ? (ebatRow.size_label || null) : null;
                        var multiplier = computeEbatMultiplierResult(ebatRow);
                        var renk_sayisi = renkSel ? String(renkSel.value || '').trim() : '';
                        var colorMultRow = findColorMultiplierRowForCount(parseInt(renk_sayisi, 10), baski_slug_canonical);
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
                            baski_slug_canonical: baski_slug_canonical,
                            baski_teknigi: baski_teknigi
                        };
                    }

                    function setCustomizationStepSummaryValue(el, html) {
                        if (!el) return;
                        if (html === null || html === undefined || html === '') {
                            el.innerHTML = '<span class="text-slate-500">—</span>';
                            return;
                        }
                        el.innerHTML = html;
                    }

                    function renderCustomizationMinimalRowHtml(row) {
                        var usesColor = printTechniqueUsesColorMultiplier(row.baski_slug_canonical || row.baski_slug);
                        var cfg = window.storeCurrencyConfig || {};
                        var parts = [];
                        if (row.konum) parts.push(String(row.konum));
                        if (row.en_boy_cm && row.en_boy_cm !== '—') parts.push(String(row.en_boy_cm));
                        if (row.baski_teknigi) parts.push(String(row.baski_teknigi));
                        if (usesColor && row.renk_sayisi) {
                            var colUnit = (PU.customization_colors_unit || '').trim();
                            parts.push(colUnit ? (String(row.renk_sayisi) + ' ' + colUnit) : String(row.renk_sayisi));
                        }
                        var label = parts.length ? parts.join(' · ') : '—';
                        var priceHtml = '';
                        if (cfg.canSeePrices && row.total_price_display) {
                            priceHtml = '<span class="shrink-0 font-medium text-slate-900">' + escapeHtml(row.total_price_display) + '</span>';
                        }
                        return '<li class="flex items-baseline justify-between gap-3 py-0.5">' +
                            '<span class="min-w-0">' + escapeHtml(label) + '</span>' +
                            priceHtml +
                            '</li>';
                    }

                    function customizationSummaryGrandTotalTry(rows) {
                        var sum = 0;
                        var hasAny = false;
                        (rows || []).forEach(function(r) {
                            if (r.total_price_try != null && isFinite(r.total_price_try)) {
                                sum += r.total_price_try;
                                hasAny = true;
                            }
                        });
                        return hasAny ? sum : null;
                    }

                    function customizationSummaryHtmlFromInputs() {
                        if (isCustomizationSkipSelected()) {
                            return '<span class="text-slate-600">' + escapeHtml(PU.skip_customization || '') + '</span>';
                        }
                        var p = getCustomizationTablePayload();
                        var ta = document.getElementById('product-customization-notes-field');
                        var txt = ta ? String(ta.value || '').trim() : '';
                        var rows = (p && p.rows && p.rows.length) ? p.rows : [];
                        if (!rows.length && !txt) {
                            return '<span class="text-slate-500">' + escapeHtml(PU.customization_summary_empty || '—') + '</span>';
                        }
                        var html = '<div class="customization-step-summary-minimal">';
                        if (rows.length) {
                            html += '<ul class="space-y-0.5">';
                            rows.forEach(function(r) {
                                html += renderCustomizationMinimalRowHtml(r);
                            });
                            html += '</ul>';
                            var cfg = window.storeCurrencyConfig || {};
                            var grandTry = customizationSummaryGrandTotalTry(rows);
                            if (cfg.canSeePrices && grandTry !== null && rows.length > 1) {
                                var grandLbl = PU.customization_section_grand_total || 'Baskı toplamı';
                                var grandDisp = formatStoreCurrencyAmount(grandTry) || '—';
                                html += '<p class="mt-1.5 text-xs font-medium text-primary-800">' +
                                    escapeHtml(grandLbl) + ': ' + escapeHtml(grandDisp) +
                                    '</p>';
                                var orderQty = Math.max(0, parseInt(getOrderQuantityForMultipliers(), 10) || 0);
                                if (orderQty > 0) {
                                    var lineDisp = formatStoreCurrencyAmount(grandTry * orderQty) || '—';
                                    var formulaTpl = PU.customization_print_order_formula || ':print × :qty adet = :total';
                                    var formula = formulaTpl
                                        .replace(':print', grandDisp)
                                        .replace(':qty', String(orderQty))
                                        .replace(':total', lineDisp);
                                    html += '<p class="mt-0.5 text-[11px] font-medium text-emerald-800 tabular-nums">' +
                                        escapeHtml(formula) + '</p>';
                                }
                            }
                        }
                        if (txt) {
                            var short = txt.length > 72 ? (txt.slice(0, 72) + '…') : txt;
                            html += '<p class="mt-1 text-xs text-slate-500 italic" title="' + escapeHtml(txt) + '">' + escapeHtml(short) + '</p>';
                        }
                        html += '</div>';
                        return html;
                    }

                    function customizationSummaryMetricDim(label, value) {
                        return '<div class="customization-summary-metric customization-summary-metric-dim min-w-0 bg-primary-50/45 px-3 py-2.5 sm:px-3.5 sm:py-3">' +
                            '<span class="block text-[10px] font-bold uppercase tracking-wider text-primary-700/90">' + escapeHtml(label) + '</span>' +
                            '<span class="customization-summary-dim-value mt-1 block leading-snug" title="' + escapeHtml(value || '—') + '">' + escapeHtml(value || '—') + '</span>' +
                            '</div>';
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
                        var usesColor = printTechniqueUsesColorMultiplier(row.baski_slug_canonical || row.baski_slug);
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
                        var gridCols = showPrice
                            ? (usesColor ? 'sm:grid-cols-7' : 'sm:grid-cols-5')
                            : (usesColor ? 'sm:grid-cols-6' : 'sm:grid-cols-4');
                        var colorMetricsHtml = usesColor
                            ? (customizationSummaryMetric(colorsLbl, renk) + customizationSummaryMetricColorMultiplier(colorMultLbl, colorMult))
                            : '';
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
                            customizationSummaryMetricDim(dimLbl, dim) +
                            customizationSummaryMetric(areaLbl, area) +
                            customizationSummaryMetric(ebatLbl, ebat) +
                            customizationSummaryMetric(multLbl, mult) +
                            (showPrice ? customizationSummaryMetricPrice(priceLbl, price) : '') +
                            colorMetricsHtml +
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
                        return price.toLocaleString((window.storeLocale || 'tr-TR'), {
                            minimumFractionDigits: isWhole ? 0 : 2,
                            maximumFractionDigits: isWhole ? 0 : 4
                        });
                    }

                    function findColorMultiplierRowForCount(colorCount, printSlug) {
                        if (!printTechniqueUsesColorMultiplier(printSlug)) {
                            return null;
                        }
                        var list = getDimensionMultipliersForPrint(printSlug).color || [];
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

                    function findQuantityMultiplierRowForQty(qty, printSlug) {
                        var list = getDimensionMultipliersForPrint(printSlug).quantity || [];
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

                    function getOrderQuantityContext(printSlug) {
                        var qty = getOrderQuantityForMultipliers();
                        return buildQuantityContextForPrint(printSlug || 'emprime', qty);
                    }

                    function renderQuantityMultiplierSummaryHtml(rows) {
                        var orderQty = getOrderQuantityForMultipliers();
                        var slugs = [];
                        (rows || []).forEach(function(row) {
                            var slug = row.baski_slug || 'emprime';
                            if (slugs.indexOf(slug) === -1) {
                                slugs.push(slug);
                            }
                        });
                        if (!slugs.length) {
                            slugs = ['emprime'];
                        }
                        var slugLabels = {};
                        (rows || []).forEach(function(row) {
                            var slug = row.baski_slug || 'emprime';
                            if (row.baski_teknigi && !slugLabels[slug]) {
                                slugLabels[slug] = row.baski_teknigi;
                            }
                        });
                        var qtyLbl = PU.customization_summary_order_qty || 'Sipariş adeti';
                        var rangeLbl = PU.customization_summary_qty_range || 'Adet aralığı';
                        var multLbl = PU.customization_summary_qty_multiplier || 'Çarpan fiyatı';
                        var metric = function(label, value, accent) {
                            return '<div class="min-w-0 rounded-lg border border-slate-200/90 bg-white px-3 py-2.5 shadow-sm">' +
                                '<p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">' + escapeHtml(label) + '</p>' +
                                '<p class="mt-0.5 text-sm font-semibold leading-snug ' + (accent || 'text-slate-900') + ' break-words">' + escapeHtml(value) + '</p>' +
                                '</div>';
                        };
                        var blocks = slugs.map(function(slug) {
                            var ctx = buildQuantityContextForPrint(slug, orderQty);
                            var qtyVal = ctx.order_quantity > 0
                                ? (ctx.order_quantity.toLocaleString((window.storeLocale || 'tr-TR')) + ' ' + (PU.units_suffix || 'adet'))
                                : (PU.customization_qty_not_set || '—');
                            var rangeVal = ctx.quantity_range_label || '—';
                            var multVal = ctx.quantity_multiplier_price_display || '—';
                            var title = slugLabels[slug] || slug;
                            var heading = slugs.length > 1
                                ? '<p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-600">' + escapeHtml(title) + '</p>'
                                : '';
                            return '<div class="rounded-lg border border-slate-200/70 bg-white/80 p-2.5">' + heading +
                                '<div class="grid grid-cols-1 gap-2 sm:grid-cols-3">' +
                                metric(qtyLbl, qtyVal) +
                                metric(rangeLbl, rangeVal) +
                                metric(multLbl, multVal, 'text-emerald-800') +
                                '</div></div>';
                        }).join('');
                        return '<div class="quantity-multiplier-summary border-b border-slate-200/80 bg-slate-50/60 px-3.5 py-3 sm:px-4 space-y-2">' + blocks + '</div>';
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
                        var orderQty = Math.max(0, parseInt(getOrderQuantityForMultipliers(), 10) || 0);
                        var lineTry = sumTry * orderQty;
                        var lineDisplay = formatStoreCurrencyAmount(lineTry) || '—';
                        var formulaHtml = '';
                        if (orderQty > 0) {
                            var formulaTpl = PU.customization_print_order_formula || ':print × :qty adet = :total';
                            var formula = formulaTpl
                                .replace(':print', display)
                                .replace(':qty', String(orderQty))
                                .replace(':total', lineDisplay);
                            var hint = PU.customization_print_order_hint || '';
                            formulaHtml =
                                '<p class="mt-2 text-xs font-medium text-emerald-900 tabular-nums">' + escapeHtml(formula) + '</p>' +
                                (hint ? '<p class="mt-0.5 text-[11px] text-slate-500">' + escapeHtml(hint) + '</p>' : '');
                        }
                        return '<div class="border-t border-slate-200/90 px-3.5 py-3 sm:px-4">' +
                            '<div class="rounded-xl border border-primary-200/80 bg-gradient-to-r from-primary-50 to-emerald-50/70 px-4 py-3.5 shadow-sm">' +
                            '<div class="flex items-center justify-between gap-3">' +
                            '<span class="text-sm font-semibold text-slate-800">' + escapeHtml(label) + '</span>' +
                            '<span class="text-xl font-bold text-primary-900">' + escapeHtml(display) + '</span>' +
                            '</div>' +
                            formulaHtml +
                            '</div></div>';
                    }

                    function renderCustomizationSummarySectionHtml(rows) {
                        if (!rows || !rows.length) return '';
                        var sec = (PU.customization_summary_section_label || '').trim();
                        var enrichedRows = enrichCustomizationRowsWithTotals(rows);
                        var cards = enrichedRows.map(function(r, i) { return renderCustomizationSummaryCardHtml(r, i); }).join('');
                        var qtySummary = renderQuantityMultiplierSummaryHtml(enrichedRows);
                        var grandTotal = renderCustomizationGrandTotalHtml(enrichedRows);

                        return '<section class="customization-summary-section overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">' +
                            (sec ? '<header class="flex items-center gap-2.5 border-b border-slate-100 bg-slate-50/90 px-3.5 py-2.5 sm:px-4">' +
                            '<span class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary-600 text-white shadow-sm">' +
                            '<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>' +
                            '</span>' +
                            '<h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">' + escapeHtml(sec) + '</h3></header>' : '') +
                            qtySummary +
                            '<div class="space-y-2.5 bg-slate-50/40 p-3 sm:p-4">' + cards + '</div>' +
                            grandTotal +
                            '</section>';
                    }

                    function getCustomizationTablePayload() {
                        var wrap = document.getElementById('product-customization-table');
                        if (!wrap) return null;
                        var checked = wrap.querySelectorAll('input.customization-row-check:checked');
                        if (!checked.length) return null;
                        var rows = [];
                        checked.forEach(function(cb) {
                            var row = cb.closest('.customization-row-card');
                            if (!isCustomizationRowDimensionsValid(row)) return;
                            var payload = customizationRowPayloadFromCard(row, cb);
                            if (payload) rows.push(payload);
                        });
                        var enrichedRows = enrichCustomizationRowsWithTotals(rows);
                        var printTotalTry = 0;
                        var hasPrintTotal = false;
                        enrichedRows.forEach(function(r) {
                            if (r.total_price_try != null && isFinite(r.total_price_try)) {
                                printTotalTry += r.total_price_try;
                                hasPrintTotal = true;
                            }
                        });
                        var orderQty = getOrderQuantityForMultipliers();
                        return enrichedRows.length ? {
                            rows: enrichedRows,
                            order_quantity: orderQty,
                            print_total_try: hasPrintTotal ? printTotalTry : null,
                            print_line_total_try: hasPrintTotal && orderQty > 0 ? (printTotalTry * orderQty) : null
                        } : null;
                    }

                    function getCustomizationPrintTotalTry() {
                        if (typeof isCustomizationSkipSelected === 'function' && isCustomizationSkipSelected()) {
                            return 0;
                        }
                        if (!document.querySelector('[data-customization-panel="1"]')) {
                            return 0;
                        }
                        var p = getCustomizationTablePayload();
                        if (!p || !p.rows || !p.rows.length) {
                            return 0;
                        }
                        if (p.print_total_try != null && isFinite(p.print_total_try)) {
                            return Math.max(0, p.print_total_try);
                        }
                        var sum = 0;
                        var hasAny = false;
                        p.rows.forEach(function(r) {
                            if (r.total_price_try != null && isFinite(r.total_price_try)) {
                                sum += r.total_price_try;
                                hasAny = true;
                            }
                        });
                        return hasAny ? Math.max(0, sum) : 0;
                    }

                    /** Birim baskı toplamı × sipariş adedi (sipariş tutarına eklenen tutar). */
                    function getCustomizationPrintLineTotalTry(orderQty) {
                        var unitTry = getCustomizationPrintTotalTry();
                        var qty = Math.max(0, parseInt(orderQty, 10) || 0);
                        if (unitTry <= 0 || qty < 1) {
                            return 0;
                        }
                        return unitTry * qty;
                    }

                    function isCustomizationSkipSelected() {
                        var skip = document.getElementById('customization-skip-checkbox');
                        return !!(skip && skip.checked);
                    }

                    function syncCustomizationFieldsDisabledState() {
                        var wrap = document.getElementById('customization-fields-wrap');
                        var skipped = isCustomizationSkipSelected();
                        if (wrap) {
                            wrap.classList.toggle('opacity-50', skipped);
                            wrap.classList.toggle('pointer-events-none', skipped);
                            wrap.setAttribute('aria-hidden', skipped ? 'true' : 'false');
                        }
                        var tbl = document.getElementById('product-customization-table');
                        if (tbl) {
                            tbl.querySelectorAll('input, select, textarea, button').forEach(function(el) {
                                if (el.id === 'customization-continue-btn') return;
                                el.disabled = skipped;
                            });
                        }
                        var notes = document.getElementById('product-customization-notes-field');
                        if (notes) notes.disabled = skipped;
                    }

                    function setCustomizationSkipSelected(on) {
                        var skip = document.getElementById('customization-skip-checkbox');
                        if (skip) skip.checked = !!on;
                        syncCustomizationFieldsDisabledState();
                        updateCustomizationContinueEnabled();
                    }

                    function updateCustomizationContinueEnabled() {
                        var btn = document.getElementById('customization-continue-btn');
                        if (!btn) return;
                        syncAllCustomizationDimensionValidity();
                        var hasRow = document.querySelectorAll('#product-customization-table input.customization-row-check:checked').length > 0;
                        var ok = isCustomizationSkipSelected() || (hasRow && allCheckedCustomizationRowsHaveValidDimensions());
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
                            syncCustomizationColorFieldForCard(card);
                        });
                        syncCustomizationColorsHeader();
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
                            if (isCustomizationSkipSelected()) {
                                vd.product_customization = 'skipped';
                                vd.product_customization_notes = '';
                            } else {
                                vd.product_customization = 'completed';
                                var ta = document.getElementById('product-customization-notes-field');
                                vd.product_customization_notes = ta ? String(ta.value || '').trim() : '';
                                var tp = getCustomizationTablePayload();
                                if (tp) vd.product_customization_table = tp;
                            }
                        }
                        variationInput.value = JSON.stringify(vd);
                    }

                    function resetProductCustomizationUi() {
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
                            if (sumVal) setCustomizationStepSummaryValue(sumVal, null);
                            var full = custPanel.querySelector('.customization-step-full');
                            if (full) full.classList.add('hidden');
                        }
                        if (ta) ta.value = '';
                        var custTbl = document.getElementById('product-customization-table');
                        if (custTbl) custTbl.querySelectorAll('.customization-row-check').forEach(function(cb) { cb.checked = false; });
                        setCustomizationSkipSelected(false);
                        resetCustomizationTableFields();
                        updateCustomizationContinueEnabled();
                        if (variationInput) {
                            variationInput.value = JSON.stringify(getSelectedOptions());
                        }
                    }

                    var priceEl = document.getElementById('product-price');
                    var fallbackBaseTry = priceEl ? parseFloat(priceEl.getAttribute('data-base-try')) || 0 : 0;
                    var baseTry = fallbackBaseTry;
                    var rate = priceEl ? parseFloat(priceEl.getAttribute('data-exchange-rate')) || 1 : 1;
                    var symbol = priceEl ? priceEl.getAttribute('data-currency-symbol') || '₺' : '₺';
                    var code = priceEl ? priceEl.getAttribute('data-currency-code') || 'TRY' : 'TRY';
                    var priceTiers = [];
                    try {
                        var tiersRaw = priceEl ? (priceEl.getAttribute('data-price-tiers') || '[]') : '[]';
                        if (tiersRaw.indexOf('&quot;') !== -1 || tiersRaw.indexOf('&#') !== -1) {
                            var ta = document.createElement('textarea');
                            ta.innerHTML = tiersRaw;
                            tiersRaw = ta.value;
                        }
                        priceTiers = JSON.parse(tiersRaw);
                        if (!Array.isArray(priceTiers)) priceTiers = [];
                    } catch (e) {
                        priceTiers = [];
                    }

                    function resolveQuantityPriceMultiplier(qty) {
                        var q = Math.max(0, parseInt(qty, 10) || 0);
                        for (var i = 0; i < priceTiers.length; i++) {
                            var t = priceTiers[i] || {};
                            var min = parseInt(t.min, 10);
                            if (!isFinite(min)) min = 1;
                            var max = t.max === null || t.max === undefined || t.max === '' ? null : parseInt(t.max, 10);
                            if (q < min) continue;
                            if (max !== null && (!isFinite(max) || q > max)) continue;
                            var m = parseFloat(t.multiplier != null ? t.multiplier : t.unit_try);
                            if (isFinite(m) && m > 0) return m;
                        }
                        return 1;
                    }

                    function resolveUnitBaseTryForQty(qty) {
                        return fallbackBaseTry * resolveQuantityPriceMultiplier(qty);
                    }

                    function convertFromTry(tryAmount) {
                        var n = parseFloat(tryAmount);
                        if (!isFinite(n)) n = 0;
                        if (code === 'TRY') return n;
                        var r = parseFloat(rate);
                        if (!isFinite(r) || r <= 0) return n;
                        return n / r;
                    }

                    function formatPrice(num) {
                        if (code === 'TRY') return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num) + ' ' + symbol;
                        return symbol + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
                    }

                    function formatVariationMultiplier(mult) {
                        var n = parseFloat(mult);
                        if (!isFinite(n) || n <= 0) n = 1;
                        var fmt = code === 'TRY'
                            ? new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 0, maximumFractionDigits: 4 })
                            : new Intl.NumberFormat('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 4 });
                        return '×' + fmt.format(n);
                    }

                    function formatPlainNumber(num, maxFrac) {
                        var n = parseFloat(num);
                        if (!isFinite(n)) n = 0;
                        var mf = typeof maxFrac === 'number' ? maxFrac : 4;
                        var fmt = code === 'TRY'
                            ? new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 0, maximumFractionDigits: mf })
                            : new Intl.NumberFormat('en-US', { minimumFractionDigits: 0, maximumFractionDigits: mf });
                        return fmt.format(n);
                    }

                    function summaryKvRowHtml(label, value, note, opts) {
                        opts = opts || {};
                        var isTotal = !!opts.isTotal;
                        var isMuted = !!opts.isMuted;
                        var rowClass = isTotal
                            ? 'flex items-start justify-between gap-3 border-t border-emerald-200/70 bg-emerald-50/80 px-3.5 py-3.5 sm:px-4'
                            : 'flex items-start justify-between gap-3 border-b border-slate-100/90 px-3.5 py-2.5 last:border-b-0 sm:px-4';
                        var labelClass = isTotal
                            ? 'text-sm font-bold text-emerald-900'
                            : (isMuted ? 'text-xs font-medium text-slate-500' : 'text-sm font-medium text-slate-600');
                        var valueClass = isTotal
                            ? 'text-right text-base font-bold tabular-nums text-emerald-900'
                            : 'text-right text-sm font-semibold tabular-nums text-slate-900';
                        var noteHtml = note
                            ? '<p class="mt-0.5 text-[11px] font-normal leading-snug text-slate-500 tabular-nums">' + escapeHtml(note) + '</p>'
                            : '';
                        return '<div class="' + rowClass + '">' +
                            '<div class="min-w-0 flex-1 pr-2"><p class="' + labelClass + '">' + escapeHtml(label) + '</p>' + noteHtml + '</div>' +
                            '<div class="shrink-0 ' + valueClass + '">' + escapeHtml(value) + '</div>' +
                            '</div>';
                    }

                    function buildPricingBreakdownSectionHtml(sizeInfo) {
                        if (!priceEl || typeof resolveUnitBaseTryForQty !== 'function') {
                            return '';
                        }
                        var qty = Math.max(0, parseInt(sizeInfo.total, 10) || 0);
                        var pricingWeight = typeof sizeInfo.pricingWeight === 'number' && !isNaN(sizeInfo.pricingWeight)
                            ? Math.max(0, sizeInfo.pricingWeight)
                            : qty;
                        var hasSizeMultDetail = !!(sizeInfo.sizeQuantities && sizeInfo.sizeMultipliers
                            && Object.keys(sizeInfo.sizeQuantities).some(function(size) {
                                return (sizeInfo.sizeQuantities[size] || 0) > 0
                                    && Math.abs((sizeInfo.sizeMultipliers[size] || 1) - 1) > 0.0001;
                            }));

                        var qtyMult = resolveQuantityPriceMultiplier(qty > 0 ? qty : 1);
                        var unitBase = resolveUnitBaseTryForQty(qty > 0 ? qty : 1);
                        var varMult = getCombinedVariationMultiplier();
                        var packExtra = typeof getPackagingAdditiveExtraTry === 'function' ? getPackagingAdditiveExtraTry() : 0;
                        var printUnitTry = typeof getCustomizationPrintTotalTry === 'function' ? getCustomizationPrintTotalTry() : 0;
                        var printLineTry = typeof getCustomizationPrintLineTotalTry === 'function'
                            ? getCustomizationPrintLineTotalTry(qty)
                            : (printUnitTry * qty);
                        var unitWithVar = unitBase * varMult + packExtra;
                        var garmentLineTry = unitWithVar * pricingWeight;
                        var lineTry = garmentLineTry + printLineTry;
                        var baseDisp = convertFromTry(fallbackBaseTry);
                        var unitDisp = convertFromTry(unitBase);
                        var lineDisp = convertFromTry(lineTry);
                        var garmentDisp = convertFromTry(garmentLineTry);

                        var items = [];
                        if (sizeInfo.sizeQuantities && Object.keys(sizeInfo.sizeQuantities).length) {
                            items.push(summaryKvRowHtml(
                                PU.summary_pricing_weight || 'Fiyatlandırma ağırlığı',
                                formatPlainNumber(pricingWeight, 4),
                                hasSizeMultDetail ? 'Σ qty×çarpan' : null
                            ));
                        }
                        items.push(summaryKvRowHtml(PU.summary_product_base_price || 'Ürün fiyatı', formatPrice(baseDisp)));
                        items.push(summaryKvRowHtml(
                            PU.summary_qty_multiplier || 'Sipariş miktarı çarpanı',
                            formatVariationMultiplier(qtyMult),
                            qty > 0 ? ((PU.summary_qty_for_tier || 'Adet') + ': ' + qty) : null
                        ));
                        items.push(summaryKvRowHtml(
                            PU.summary_qty_unit_price || 'Miktara göre birim fiyat',
                            formatPrice(unitDisp),
                            formatPrice(baseDisp) + ' × ' + formatPlainNumber(qtyMult, 4)
                        ));
                        if (Math.abs(varMult - 1) > 0.0001) {
                            items.push(summaryKvRowHtml(
                                PU.summary_variation_mult_total || 'Varyasyon çarpanları',
                                formatVariationMultiplier(varMult)
                            ));
                        }
                        if (packExtra > 0) {
                            var packDisp = convertFromTry(packExtra);
                            items.push(summaryKvRowHtml(PU.summary_packaging_extra || 'Ambalaj ek ücreti', formatPrice(packDisp)));
                        }
                        var formulaTpl = PU.summary_price_formula || ':base × :qtymult × :varmult × :weight = :total';
                        var formula = formulaTpl
                            .replace(':base', formatPrice(baseDisp))
                            .replace(':unit', formatPrice(unitDisp))
                            .replace(':qtymult', formatPlainNumber(qtyMult, 4))
                            .replace(':varmult', formatPlainNumber(varMult, 4))
                            .replace(':weight', formatPlainNumber(pricingWeight, 4))
                            .replace(':total', formatPrice(garmentDisp));
                        if (packExtra > 0) {
                            formulaTpl = PU.summary_price_formula_with_pack || '(:base × :qtymult × :varmult + :pack) × :weight = :total';
                            var packDisp2 = convertFromTry(packExtra);
                            formula = formulaTpl
                                .replace(':base', formatPrice(baseDisp))
                                .replace(':unit', formatPrice(unitDisp))
                                .replace(':qtymult', formatPlainNumber(qtyMult, 4))
                                .replace(':varmult', formatPlainNumber(varMult, 4))
                                .replace(':pack', formatPrice(packDisp2))
                                .replace(':weight', formatPlainNumber(pricingWeight, 4))
                                .replace(':total', formatPrice(garmentDisp));
                        }
                        items.push(summaryKvRowHtml(PU.summary_price_calc || 'Fiyat hesabı', formatPrice(garmentDisp), formula, { isMuted: true }));
                        if (printUnitTry > 0) {
                            var printUnitDisp = convertFromTry(printUnitTry);
                            var printLineDisp = convertFromTry(printLineTry);
                            items.push(summaryKvRowHtml(
                                PU.summary_print_total || PU.customization_section_grand_total || 'Baskı toplamı',
                                formatPrice(printUnitDisp)
                            ));
                            if (qty > 0) {
                                var timesTpl = PU.summary_print_times_qty || ':print × :qty adet';
                                var timesTxt = timesTpl
                                    .replace(':print', formatPrice(printUnitDisp))
                                    .replace(':qty', String(qty));
                                items.push(summaryKvRowHtml(
                                    PU.summary_print_order_total || 'Baskı sipariş tutarı',
                                    formatPrice(printLineDisp),
                                    timesTxt
                                ));
                            }
                        }
                        if (qty > 0) {
                            var perPieceTry = lineTry / qty;
                            var perPieceDisp = convertFromTry(perPieceTry);
                            var perPieceNoteTpl = PU.summary_unit_from_total_note || ':total ÷ :qty adet';
                            var perPieceNote = perPieceNoteTpl
                                .replace(':total', formatPrice(lineDisp))
                                .replace(':qty', String(qty));
                            items.push(summaryKvRowHtml(
                                PU.summary_unit_from_total || PU.summary_product_base_price || 'Ürün fiyatı',
                                formatPrice(perPieceDisp),
                                perPieceNote
                            ));
                        }
                        items.push(summaryKvRowHtml(PU.summary_line_total || 'Satır toplamı', formatPrice(lineDisp), null, { isTotal: true }));

                        var title = PU.summary_section_pricing || 'Fiyat özeti';
                        return '<section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">' +
                            '<header class="border-b border-slate-100 bg-slate-50/90 px-3.5 py-2.5 sm:px-4">' +
                            '<h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">' + escapeHtml(title) + '</h3>' +
                            '</header>' +
                            '<div>' + items.join('') + '</div>' +
                            '</section>';
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
                        document.querySelectorAll('.variation-step-panel[data-variation-name][data-step-unlocked="1"]').forEach(function(panel) {
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
                        return product * getDeliverySubOptionMultiplier();
                    }

                    function getDeliverySubOptionMultiplier() {
                        var mult = 1;
                        document.querySelectorAll('.variation-step-panel[data-variation-type="delivery_type"][data-step-unlocked="1"]').forEach(function(block) {
                            if ((block.getAttribute('data-delivery-options-confirmed') || '') !== '1') return;
                            var wrap = block.querySelector('.delivery-type-suboptions-wrap');
                            if (!wrap) return;
                            var subBtn = wrap.querySelector('.delivery-type-suboption-btn[data-selected="1"]');
                            if (!subBtn) return;
                            var m = parseFloat(subBtn.getAttribute('data-price-multiplier'));
                            if (isFinite(m) && m > 0) mult *= m;
                        });
                        return mult;
                    }

                    function getPackagingAdditiveExtraTry() {
                        var extra = 0;
                        document.querySelectorAll('.variation-step-panel[data-variation-type="packaging_type"][data-step-unlocked="1"]').forEach(function(block) {
                            if ((block.getAttribute('data-packaging-options-confirmed') || '') !== '1') return;
                            var payload = buildPackagingTypeVariationPayload(block);
                            if (payload && typeof payload.extra_price_try === 'number' && isFinite(payload.extra_price_try)) {
                                extra += Math.max(0, payload.extra_price_try);
                            }
                        });
                        return extra;
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
                            if (item.payload !== undefined) {
                                selected[item.name] = item.payload;
                            } else if (item.isMulti && item.values && item.values.length) {
                                // values used for display may be labeled; keep raw data-option if available
                                selected[item.name] = item.rawValues || item.values;
                            } else {
                                selected[item.name] = item.rawValue || item.value;
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
                            if ((panel.getAttribute('data-step-unlocked') || '') !== '1') return;
                            if ((panel.getAttribute('data-customization-panel') || '') === '1') return;
                            var name = (panel.getAttribute('data-variation-name') || '').trim();
                            if (!name) return;
                            var isMulti = (panel.getAttribute('data-allows-multiple') || '') === '1';
                            var confirmed = (panel.getAttribute('data-multi-confirmed') || '') === '1';
                            var summary = panel.querySelector('.variation-step-summary');
                            var summaryVal = panel.querySelector('.variation-step-summary-value');
                            if (isMulti) {
                                if (!confirmed) return;
                                var values = [];
                                var rawValues = [];
                                var delta = 1;
                                panel.querySelectorAll('.product-option.option-selected').forEach(function(sel) {
                                    if (sel.style.display === 'none') return;
                                    values.push(optionDisplayLabel(sel));
                                    rawValues.push((sel.getAttribute('data-option') || '').trim());
                                    delta *= variationMultiplierFromAttr(sel);
                                });
                                var displayVal = summary && summaryVal && !summary.classList.contains('hidden')
                                    ? (summaryVal.textContent || '').trim()
                                    : values.join(', ');
                                if (values.length && displayVal && displayVal !== '—') {
                                    list.push({ name: name, value: displayVal, values: values, rawValues: rawValues, priceDelta: delta, isMulti: true });
                                }
                            } else {
                                if (!isProductVariationBlockComplete(panel)) return;
                                var value = '';
                                var delta = 1;
                                var sel = getVisibleSelectedProductOption(panel);
                                if (sel) {
                                    delta = variationMultiplierFromAttr(sel);
                                }
                                if ((panel.getAttribute('data-variation-type') || '') === 'label_type') {
                                    var labelPayload = buildLabelTypeVariationPayload(panel);
                                    if (labelPayload) {
                                        var labelDisplay = Array.isArray(labelPayload)
                                            ? formatLabelTypeMultiSummary(panel)
                                            : formatLabelTypeSelectionDisplay(labelPayload);
                                        list.push({ name: name, value: labelDisplay, payload: labelPayload, priceDelta: delta, isMulti: Array.isArray(labelPayload) });
                                    }
                                } else if ((panel.getAttribute('data-variation-type') || '') === 'packaging_type') {
                                    var packagingPayload = buildPackagingTypeVariationPayload(panel);
                                    if (packagingPayload) {
                                        var packagingDisplay = formatPackagingTypeSelectionDisplay(packagingPayload);
                                        list.push({ name: name, value: packagingDisplay, payload: packagingPayload, priceDelta: delta, isMulti: false });
                                    }
                                } else if ((panel.getAttribute('data-variation-type') || '') === 'delivery_type') {
                                    var deliveryPayload = buildDeliveryTypeVariationPayload(panel);
                                    if (deliveryPayload) {
                                        var deliveryDisplay = formatDeliveryTypeSelectionDisplay(deliveryPayload);
                                        list.push({ name: name, value: deliveryDisplay, payload: deliveryPayload, priceDelta: delta, isMulti: false });
                                    }
                                } else if (summary && summaryVal && !summary.classList.contains('hidden')) {
                                    value = (summaryVal.textContent || '').trim();
                                    if (value && value !== '—') list.push({ name: name, value: value, priceDelta: delta, isMulti: false });
                                } else if (sel && sel.style.display !== 'none') {
                                    value = optionDisplayLabel(sel);
                                    if (value && value !== '—') list.push({ name: name, value: value, priceDelta: delta, isMulti: false, rawValue: (sel.getAttribute('data-option') || '').trim() });
                                }
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
                        var sizeInfo = getSizeQuantities();
                        var qty = Math.max(0, parseInt(sizeInfo.total, 10) || 0);
                        baseTry = resolveUnitBaseTryForQty(qty > 0 ? qty : 1);
                        var unitTry = baseTry * mult + getPackagingAdditiveExtraTry();
                        var pricingWeight = typeof sizeInfo.pricingWeight === 'number' && !isNaN(sizeInfo.pricingWeight)
                            ? Math.max(0, sizeInfo.pricingWeight)
                            : qty;
                        var printLineTry = typeof getCustomizationPrintLineTotalTry === 'function'
                            ? getCustomizationPrintLineTotalTry(qty)
                            : 0;
                        var lineTry = (unitTry * pricingWeight) + printLineTry;
                        var baseConverted = convertFromTry(baseTry);
                        var lineConverted = convertFromTry(lineTry);
                        priceEl.textContent = formatPrice(lineConverted);
                        var basePriceEl = document.getElementById('product-base-price');
                        if (basePriceEl) {
                            basePriceEl.textContent = formatPrice(baseConverted);
                        }
                        var perPieceEl = document.getElementById('product-per-piece-price');
                        var perPieceNoteEl = document.getElementById('product-per-piece-price-note');
                        if (perPieceEl) {
                            if (qty > 0) {
                                var perPieceTry = lineTry / qty;
                                var perPieceConverted = convertFromTry(perPieceTry);
                                perPieceEl.textContent = formatPrice(perPieceConverted);
                                if (perPieceNoteEl) {
                                    var noteTpl = PU.summary_unit_from_total_note || ':total ÷ :qty adet';
                                    perPieceNoteEl.textContent = noteTpl
                                        .replace(':total', formatPrice(lineConverted))
                                        .replace(':qty', String(qty));
                                    perPieceNoteEl.classList.remove('hidden');
                                }
                            } else {
                                perPieceEl.textContent = '—';
                                if (perPieceNoteEl) {
                                    perPieceNoteEl.textContent = '';
                                    perPieceNoteEl.classList.add('hidden');
                                }
                            }
                        }
                        var strikeEl = document.getElementById('product-price-strike');
                        if (strikeEl) {
                            var normalAttr = priceEl.getAttribute('data-normal-try');
                            var normalTry = normalAttr !== null && normalAttr !== '' ? parseFloat(normalAttr) : NaN;
                            if (qty > 0 && !isNaN(normalTry) && normalTry > baseTry) {
                                var oldLineTry = (normalTry * mult * pricingWeight) + printLineTry;
                                var oldConverted = convertFromTry(oldLineTry);
                                strikeEl.textContent = formatPrice(oldConverted);
                                strikeEl.classList.remove('hidden');
                            } else {
                                strikeEl.textContent = '';
                                strikeEl.classList.add('hidden');
                            }
                        }
                    }

                    function customizationDependsOnKey() {
                        var wrap = document.getElementById('product-variations');
                        return wrap ? (wrap.getAttribute('data-customization-depends-key') || '__product_customization__') : '__product_customization__';
                    }

                    function isCustomizationDependsOn(name) {
                        return (name || '').trim() === customizationDependsOnKey();
                    }

                    function isCustomizationStepComplete() {
                        var panel = document.querySelector('[data-customization-panel="1"]');
                        if (!panel) return false;
                        return (panel.getAttribute('data-customization-confirmed') || '') === '1';
                    }

                    function getSelectedCustomizationRowIds() {
                        var ids = [];
                        document.querySelectorAll('#product-customization-table input.customization-row-check:checked').forEach(function(cb) {
                            var v = (cb.value || '').trim();
                            if (v !== '') ids.push(Number(v));
                        });
                        return ids;
                    }

                    function getSelectedParentOptionIdsForVariation(variationName) {
                        if (isCustomizationDependsOn(variationName)) {
                            if (!isCustomizationStepComplete()) return [];
                            if (isCustomizationSkipSelected()) return [];
                            return getSelectedCustomizationRowIds();
                        }
                        var block = getVariationPanelByName(variationName)
                            || document.querySelector('.variation-step-panel[data-variation-name="' + variationName + '"]');
                        if (!block) return [];
                        var variationType = block.getAttribute('data-variation-type') || '';
                        if (variationType === 'label_type') {
                            if (!isProductVariationBlockComplete(block)) return [];
                            if (isLabelTypeMulti(block)) {
                                var multiIds = [];
                                getLabelTypeSelectedOptions(block).forEach(function(b) {
                                    var id = b.getAttribute('data-option-id');
                                    if (id) multiIds.push(Number(id));
                                });
                                return multiIds;
                            }
                        }
                        if (variationType === 'packaging_type') {
                            var packagingSel = getVisibleSelectedProductOption(block);
                            if (packagingSel && (block.getAttribute('data-packaging-options-confirmed') || '') !== '1') {
                                return [];
                            }
                        }
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
                        var selected = getVisibleSelectedProductOption(block);
                        if (!selected) return [];
                        var sid = selected.getAttribute('data-option-id');
                        return sid ? [Number(sid)] : [];
                    }

                    function normalizeOptionIdList(raw) {
                        if (!raw) return [];
                        var arr = Array.isArray(raw) ? raw : [raw];
                        return arr.map(function(x) { return Number(x); }).filter(function(x) { return !isNaN(x); });
                    }

                    function parseParentOptionIdsFromElement(el) {
                        if (!el) return [];
                        var parentIdsJson = el.getAttribute('data-parent-option-ids');
                        if (parentIdsJson) {
                            try { return normalizeOptionIdList(JSON.parse(parentIdsJson)); } catch (e) {}
                        }
                        var parentIdSingle = (el.getAttribute('data-parent-option-id') || '').trim();
                        return parentIdSingle ? [Number(parentIdSingle)] : [];
                    }

                    function listsOverlap(a, b) {
                        if (!a.length || !b.length) return false;
                        return a.some(function(id) { return b.indexOf(Number(id)) !== -1; });
                    }

                    function syncProductOptionWrapVisibility(opt) {
                        if (!opt) return;
                        var wrap = opt.closest('.variation-option-wrap');
                        if (!wrap) return;
                        wrap.style.display = opt.style.display === 'none' ? 'none' : '';
                    }

                    function setProductOptionVisibility(opt, show) {
                        if (!opt) return;
                        opt.style.display = show ? '' : 'none';
                        syncProductOptionWrapVisibility(opt);
                    }

                    function filterDependentVariation(block, parentOptionIds) {
                        var ids = normalizeOptionIdList(parentOptionIds);
                        var options = block.querySelectorAll('.product-option');
                        var visible = [];
                        options.forEach(function(opt) {
                            var parentIds = parseParentOptionIdsFromElement(opt);
                            var show;
                            if (ids.length === 0) {
                                show = parentIds.length === 0;
                            } else {
                                show = listsOverlap(parentIds, ids) || parentIds.length === 0;
                            }
                            setProductOptionVisibility(opt, show);
                            if (show) visible.push(opt);
                        });
                        if (visible.length === 0) {
                            var isSizeTable = (block.getAttribute('data-variation-type') || '') === 'size_table';
                            if (!isSizeTable) {
                                options.forEach(function(opt) { setProductOptionVisibility(opt, true); });
                                visible = Array.from(options);
                            }
                        }
                        options.forEach(function(opt) {
                            if (opt.style.display !== 'none') return;
                            setProductOptionVisual(opt, false);
                        });
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
                     * Tip Renk: yalnızca Kumaş Türü Varyasyonları’nda atanan renkler (pivot).
                     * Kumaş adımı varken: kumaş seçilmeden renk yok; seçilince yalnız o kumaşa bağlı renkler.
                     */
                    function colorOptionFabricGroupIds(opt) {
                        var raw = opt.getAttribute('data-color-fabric-group-ids') || '[]';
                        try {
                            var parsed = JSON.parse(raw);
                            if (Array.isArray(parsed)) {
                                return parsed.map(function(id) { return String(id); }).filter(function(id) { return id !== ''; });
                            }
                        } catch (e) {}
                        return [];
                    }

                    function updateColorVariationStepCounts() {
                        document.querySelectorAll('.variation-step-panel[data-variation-type="color"]').forEach(function(colorBlock) {
                            var badge = colorBlock.querySelector('.variation-step-option-count');
                            if (!badge) return;
                            var visibleCount = 0;
                            colorBlock.querySelectorAll('.product-option').forEach(function(opt) {
                                if (opt.style.display !== 'none') visibleCount++;
                            });
                            badge.textContent = (PU.color_count_badge || ':count colors').replace(':count', String(visibleCount));
                        });
                    }

                    function filterColorOptionsByFabric() {
                        if (!document.querySelector('.product-variation-block[data-variation-type="color"]')) return;
                        var fabricStepExists = !!document.querySelector('.product-variation-block[data-variation-type="fabric"]');
                        var preset = fabricStepExists ? getSelectedFabricPresetId() : null;

                        document.querySelectorAll('.product-variation-block[data-variation-type="color"]').forEach(function(colorBlock) {
                            if (colorBlock.style.display === 'none') return;
                            var options = colorBlock.querySelectorAll('.product-option');
                            options.forEach(function(opt) {
                                var groupIds = colorOptionFabricGroupIds(opt);
                                if (!fabricStepExists) {
                                    setProductOptionVisibility(opt, true);
                                    return;
                                }
                                if (preset === null) {
                                    setProductOptionVisibility(opt, false);
                                    return;
                                }
                                setProductOptionVisibility(opt, groupIds.indexOf(String(preset)) !== -1);
                            });

                            var currentSelected = colorBlock.querySelector('.product-option.option-selected');
                            if (currentSelected && currentSelected.style.display === 'none') {
                                var isMulti = (colorBlock.getAttribute('data-allows-multiple') || '') === '1';
                                setProductOptionVisual(currentSelected, false);
                                if (isMulti) {
                                    colorBlock.setAttribute('data-multi-confirmed', '0');
                                    updatePanelSummaryMulti(colorBlock);
                                } else {
                                    updatePanelSummary(colorBlock, '—');
                                }
                            }
                        });

                        updateColorVariationStepCounts();
                    }

                    function allProductVariationBlocksComplete() {
                        var finalPanel = getFinalVariationPanel();
                        var ordered = getOrderedVariationStepPanels();
                        for (var i = 0; i < ordered.length; i++) {
                            var panel = ordered[i].panel;
                            if ((panel.getAttribute('data-customization-panel') || '') === '1') continue;
                            if ((panel.getAttribute('data-step-unlocked') || '') !== '1') continue;
                            if (!isProductVariationBlockComplete(panel)) return false;
                            if (finalPanel && panel === finalPanel) {
                                return true;
                            }
                        }
                        return true;
                    }

                    /** Özelleştirme sıfırlama: yalnızca özelleştirme adımında veya öncesindeyken, üst akış adımlarında asla. */
                    function shouldResetCustomizationUi() {
                        var meta = getVariationStepsMeta();
                        var custIdx = meta.customizationIdx;
                        if (custIdx < 0 || !meta.customizationEnabled) return false;
                        if (currentVariationStep > custIdx) return false;
                        var ordered = getOrderedVariationStepPanels();
                        for (var i = 0; i < ordered.length; i++) {
                            if (ordered[i].index >= currentVariationStep) break;
                            if (ordered[i].index === custIdx) continue;
                            if ((ordered[i].panel.getAttribute('data-customization-panel') || '') === '1') continue;
                            if ((ordered[i].panel.getAttribute('data-step-unlocked') || '') !== '1') continue;
                            if (!isVariationStepPanelComplete(ordered[i].panel)) return true;
                        }
                        return false;
                    }

                    function allVisibleVariationsSelected() {
                        if (!allProductVariationBlocksComplete()) return false;
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
                        var isLabelMulti = (container.getAttribute('data-variation-type') || '') === 'label_type' && isLabelTypeMulti(container);
                        var labelSubActive = isLabelMulti && (container.getAttribute('data-label-sub-flow-active') || '') === '1';
                        var labelWrap = container.querySelector('.label-type-suboptions-wrap');
                        var labelSubVisible = isLabelMulti && labelWrap && !labelWrap.classList.contains('hidden');
                        var hideWrap = labelSubActive || labelSubVisible || (confirmed && !isLabelMulti);
                        if (wrap) wrap.classList.toggle('hidden', hideWrap);
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
                        var sizeMultipliers = {};
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
                                    sizeMultipliers[size] = mult;
                                    total += val;
                                    pricingWeight += val * mult;
                                }
                            });
                            return { sizeQuantities: sizeQuantities, sizeMultipliers: sizeMultipliers, total: total, pricingWeight: pricingWeight };
                        }
                        var simpleWrap = document.getElementById('quantity-simple-wrap');
                        if (simpleWrap && simpleWrap.classList.contains('hidden')) {
                            return { sizeQuantities: null, sizeMultipliers: null, total: 0, pricingWeight: 0, simple: false };
                        }
                        var quantityInput = document.getElementById('quantity-input');
                        if (quantityInput) {
                            total = parseInt(quantityInput.value, 10) || 0;
                            return { sizeQuantities: null, sizeMultipliers: null, total: total, pricingWeight: total, simple: true };
                        }
                        return { sizeQuantities: null, sizeMultipliers: null, total: 0, pricingWeight: 0, simple: true };
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

                    function labelSelectionRequiresCustomerArtworkNotice() {
                        var needsNotice = false;
                        document.querySelectorAll('.variation-step-panel[data-variation-type="label_type"]').forEach(function(block) {
                            if (block.style.display === 'none') return;
                            if ((block.getAttribute('data-step-unlocked') || '') !== '1') return;
                            var payloads = parseLabelSubPayloads(block);
                            Object.keys(payloads).forEach(function(key) {
                                var p = payloads[key];
                                if (p && typeof p === 'object' && p.custom_print_artwork === 'customer_send') {
                                    needsNotice = true;
                                }
                            });
                            var wrap = block.querySelector('.label-type-suboptions-wrap');
                            if (wrap && !wrap.classList.contains('hidden')) {
                                if (wrap.querySelector('.label-type-custom-print-artwork-btn[data-artwork="customer_send"][data-selected="1"]')) {
                                    needsNotice = true;
                                }
                            }
                            if (!isLabelTypeMulti(block) && (block.getAttribute('data-label-options-confirmed') || '') === '1') {
                                var sel = getLabelTypeSelectedOption(block);
                                var payload = sel ? buildLabelTypePayloadFromWrap(block, sel) : null;
                                if (payload && typeof payload === 'object' && payload.custom_print_artwork === 'customer_send') {
                                    needsNotice = true;
                                }
                            }
                        });
                        return needsNotice;
                    }

                    function syncLabelArtworkCustomerNotice() {
                        var notice = document.getElementById('variation-label-artwork-notice');
                        if (!notice) return;
                        notice.classList.toggle('hidden', !labelSelectionRequiresCustomerArtworkNotice());
                    }

                    function getDeliverySelectedEstimateText() {
                        var panels = document.querySelectorAll('.variation-step-panel[data-variation-type="delivery_type"][data-step-unlocked="1"]');
                        for (var i = 0; i < panels.length; i++) {
                            if (panels[i].style.display === 'none') continue;
                            var sel = getVisibleSelectedProductOption(panels[i]);
                            if (!sel) continue;
                            var est = (sel.getAttribute('data-estimated-delivery-time') || '').trim();
                            if (est) return est;
                        }
                        return '';
                    }

                    function syncDeliveryEstimateNotice() {
                        var notice = document.getElementById('variation-delivery-estimate-notice');
                        var textEl = document.getElementById('variation-delivery-estimate-notice-text');
                        if (!notice || !textEl) return;
                        var est = getDeliverySelectedEstimateText();
                        if (est) {
                            textEl.textContent = est;
                            notice.classList.remove('hidden');
                        } else {
                            textEl.textContent = '';
                            notice.classList.add('hidden');
                        }
                    }

                    function updateDeliverySubPanelEstimate(wrap, sel) {
                        if (!wrap) return;
                        var estEl = wrap.querySelector('.delivery-type-estimated-time');
                        if (!estEl) return;
                        var est = sel ? (sel.getAttribute('data-estimated-delivery-time') || '').trim() : '';
                        if (est) {
                            var prefix = PU.delivery_estimated_time_panel_prefix || 'Tahmini teslimat süresi:';
                            estEl.textContent = prefix + ' ' + est;
                            estEl.classList.remove('hidden');
                        } else {
                            estEl.textContent = '';
                            estEl.classList.add('hidden');
                        }
                    }

                    function updateVariationSummaryAndButton() {
                        updateSizeTotalDisplays();
                        if (typeof updateSizeTableVisibility === 'function') updateSizeTableVisibility();
                        updatePriceAndInput();
                        var summaryBody = document.getElementById('variation-summary-body');
                        var warningEl = document.getElementById('variation-summary-warning');
                        var btn = document.getElementById('add-to-cart-btn');
                        var confirmCheckbox = document.getElementById('variation-confirm-checkbox');
                        var ordered = getSelectedOptionsInStepOrder();
                        var sizeInfo = getSizeQuantities();
                        if (!summaryBody) {
                            syncMainGalleryFromVariations();
                            return;
                        }

                        var custPayload = getCustomizationTablePayload();
                        var hasCustRows = !!(custPayload && custPayload.rows && custPayload.rows.length);
                        var emptyPrompt = '<p class="rounded-xl border border-dashed border-slate-200 bg-slate-50/60 px-4 py-8 text-center text-sm text-slate-400">' +
                            escapeHtml(PU.summary_select_prompt) + '</p>';

                        if (ordered.length === 0 && !hasCustRows) {
                            summaryBody.innerHTML = emptyPrompt;
                        } else {
                            var parts = [];

                            if (ordered.length > 0) {
                                var choiceItems = ordered.map(function(item) {
                                    var mult = parseFloat(item.priceDelta);
                                    if (!isFinite(mult) || mult <= 0) mult = 1;
                                    var showMult = Math.abs(mult - 1) > 0.0001;
                                    var multBadge = showMult
                                        ? '<span class="mt-1 inline-flex rounded-md bg-amber-50 px-1.5 py-0.5 text-[11px] font-semibold tabular-nums text-amber-800 ring-1 ring-inset ring-amber-200/80">' +
                                            escapeHtml(formatVariationMultiplier(mult)) + '</span>'
                                        : '';
                                    return '<li class="flex items-start justify-between gap-3 px-3.5 py-2.5 sm:px-4">' +
                                        '<span class="min-w-0 flex-1 text-sm text-slate-500">' + escapeHtml(catalogLabel(item.name)) + '</span>' +
                                        '<div class="max-w-[62%] shrink-0 text-right sm:max-w-[70%]">' +
                                        '<p class="text-sm font-semibold leading-snug text-slate-900 break-words">' + escapeHtml(item.value) + '</p>' +
                                        multBadge +
                                        '</div></li>';
                                }).join('');
                                parts.push(
                                    '<section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">' +
                                    '<header class="border-b border-slate-100 bg-slate-50/90 px-3.5 py-2.5 sm:px-4">' +
                                    '<h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">' +
                                    escapeHtml(PU.summary_section_choices || 'Seçimler') + '</h3></header>' +
                                    '<ul class="divide-y divide-slate-100">' + choiceItems + '</ul></section>'
                                );
                            }

                            if (hasCustRows) {
                                parts.push(renderCustomizationSummarySectionHtml(custPayload.rows));
                            }

                            if (ordered.length > 0) {
                                var qtyRows = [];
                                if (sizeInfo.sizeQuantities && Object.keys(sizeInfo.sizeQuantities).length) {
                                    Object.keys(sizeInfo.sizeQuantities).forEach(function(size) {
                                        var qty = sizeInfo.sizeQuantities[size];
                                        if (qty > 0) {
                                            var sizeMult = 1;
                                            if (sizeInfo.sizeMultipliers && sizeInfo.sizeMultipliers[size] != null) {
                                                sizeMult = parseFloat(sizeInfo.sizeMultipliers[size]);
                                                if (!isFinite(sizeMult) || sizeMult < 0) sizeMult = 1;
                                            }
                                            var sizeWeight = qty * sizeMult;
                                            var note = null;
                                            if (Math.abs(sizeMult - 1) > 0.0001) {
                                                var sizeFormulaTpl = PU.summary_size_weight_formula || ':qty × :mult = :weight';
                                                note = sizeFormulaTpl
                                                    .replace(':qty', formatPlainNumber(qty, 0))
                                                    .replace(':mult', formatPlainNumber(sizeMult, 4))
                                                    .replace(':weight', formatPlainNumber(sizeWeight, 4));
                                            }
                                            qtyRows.push(summaryKvRowHtml(
                                                (PU.size_row_prefix || 'Beden') + ' ' + size,
                                                qty + ' ' + (PU.units_suffix || 'adet'),
                                                note
                                            ));
                                        }
                                    });
                                    qtyRows.push(summaryKvRowHtml(
                                        PU.summary_total_qty || 'Toplam adet',
                                        sizeInfo.total + ' ' + (PU.units_suffix || 'adet'),
                                        null,
                                        { isTotal: true }
                                    ));
                                } else {
                                    qtyRows.push(summaryKvRowHtml(
                                        PU.qty_row_label || 'Adet',
                                        sizeInfo.total + ' ' + (PU.units_suffix || 'adet'),
                                        null,
                                        { isTotal: true }
                                    ));
                                }
                                parts.push(
                                    '<section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">' +
                                    '<header class="border-b border-slate-100 bg-slate-50/90 px-3.5 py-2.5 sm:px-4">' +
                                    '<h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-500">' +
                                    escapeHtml(PU.summary_section_quantities || 'Adet dağılımı') + '</h3></header>' +
                                    '<div>' + qtyRows.join('') + '</div></section>'
                                );
                                var pricingHtml = buildPricingBreakdownSectionHtml(sizeInfo);
                                if (pricingHtml) {
                                    parts.push(pricingHtml);
                                }
                            }

                            summaryBody.innerHTML = parts.join('');
                        }

                        var confirmWrap = document.getElementById('variation-confirm-wrap');
                        if (confirmWrap) {
                            confirmWrap.classList.toggle('hidden', ordered.length === 0);
                        }
                        if (confirmCheckbox && ordered.length === 0) {
                            confirmCheckbox.checked = false;
                        }
                        var orderMode = (orderModeInput && orderModeInput.value) || 'detailed';
                        if (orderMode === 'quick') {
                            var quickState = getQuickOrderInputState();
                            var canSubmit = quickState.hasContent;
                            var btnLabel = document.getElementById('add-to-cart-btn-label');
                            if (warningEl) {
                                warningEl.classList.add('hidden');
                            }
                            if (btn) {
                                btn.disabled = !canSubmit;
                                if (btnLabel) {
                                    btnLabel.textContent = canSubmit ? PU.add_to_cart : PU.add_to_cart_hint;
                                }
                            }
                            return;
                        }
                        var allSelected = allVisibleVariationsSelected();
                        if (!isFinalVariationFlowTriggered() && shouldResetCustomizationUi()) {
                            resetProductCustomizationUi();
                            var meta = getVariationStepsMeta();
                            if (totalVariationSteps > 0 && meta.customizationIdx >= 0) {
                                showVariationStep(meta.customizationIdx);
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
                        syncLabelArtworkCustomerNotice();
                        syncDeliveryEstimateNotice();
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
                        syncVariationStepUnlockStates();
                        document.querySelectorAll('.variation-step-panel[data-step-unlocked="1"]').forEach(function(block) {
                            var dependsOn = (block.getAttribute('data-depends-on') || '').trim();
                            if (!dependsOn) return;
                            var parentIds = isCustomizationDependsOn(dependsOn)
                                ? getSelectedCustomizationRowIds()
                                : getSelectedParentOptionIdsForVariation(dependsOn);
                            if ((block.getAttribute('data-variation-type') || '') !== 'size_table') {
                                filterDependentVariation(block, parentIds);
                            }
                        });
                        filterColorOptionsByFabric();
                        document.querySelectorAll('.product-variation-block[data-variation-type="size_table"]').forEach(function(block) {
                            filterSizeTableVariationOptions(block);
                            syncSizeTableVariationBlock(block);
                        });
                        document.querySelectorAll('.variation-step-panel[data-variation-type="label_type"][data-step-unlocked="1"]').forEach(function(block) {
                            if ((block.getAttribute('data-label-sub-flow-active') || '') === '1') {
                                updateLabelTypePanelSummary(block);
                                updateLabelTypeContinueButton(block);
                            } else {
                                syncLabelTypeSubOptionsPanel(block);
                                updateLabelTypePanelSummary(block);
                            }
                        });
                        document.querySelectorAll('.variation-step-panel[data-variation-type="packaging_type"][data-step-unlocked="1"]').forEach(function(block) {
                            if ((block.getAttribute('data-packaging-options-confirmed') || '') === '1') {
                                updatePackagingTypePanelSummary(block);
                                return;
                            }
                            syncPackagingTypeSubOptionsPanel(block);
                            updatePackagingTypePanelSummary(block);
                        });
                        document.querySelectorAll('.variation-step-panel[data-variation-type="delivery_type"][data-step-unlocked="1"]').forEach(function(block) {
                            if ((block.getAttribute('data-delivery-options-confirmed') || '') === '1') {
                                updateDeliveryTypePanelSummary(block);
                                return;
                            }
                            syncDeliveryTypeSubOptionsPanel(block);
                            updateDeliveryTypePanelSummary(block);
                        });
                        updateSizeTableVisibility();
                        updateVariationSummaryAndButton();
                    }

                    var totalVariationSteps = document.querySelectorAll('.variation-step-dot').length;
                    var currentVariationStep = 0;

                    function updatePanelSummary(panel, selectedValue) {
                        var valEl = panel.querySelector('.variation-step-summary-value');
                        if (valEl) valEl.textContent = selectedValue || '—';
                    }

                    function labelOptionNeedsSubOptions(btn) {
                        if (!btn) return false;
                        return (btn.getAttribute('data-label-custom-print') || '') === '1'
                            || (btn.getAttribute('data-label-position-front') || '') === '1'
                            || (btn.getAttribute('data-label-position-back') || '') === '1'
                            || (btn.getAttribute('data-label-ask-description') || '') === '1';
                    }

                    function labelOptionAskDescription(btn) {
                        return !!btn && (btn.getAttribute('data-label-ask-description') || '') === '1';
                    }

                    function getLabelDescriptionTitle(sel) {
                        if (!sel) return PU.label_description_default_title || 'Açıklama';
                        var title = (sel.getAttribute('data-label-description-title') || '').trim();
                        return title || (PU.label_description_default_title || 'Açıklama');
                    }

                    function syncLabelTypeDescriptionSection(wrap, sel) {
                        if (!wrap) return;
                        var section = wrap.querySelector('.label-type-description-section');
                        if (!section) return;
                        var needsDesc = labelOptionAskDescription(sel);
                        section.classList.toggle('hidden', !needsDesc);
                        var heading = section.querySelector('.label-type-description-heading');
                        if (heading) heading.textContent = needsDesc ? getLabelDescriptionTitle(sel) : '';
                    }

                    function getVisibleSelectedProductOption(container) {
                        if (!container) return null;
                        var sel = null;
                        container.querySelectorAll('.product-option.option-selected').forEach(function(b) {
                            if (b.style.display === 'none') return;
                            if (!sel) sel = b;
                        });
                        return sel;
                    }

                    function isLabelTypeMulti(block) {
                        return !!block
                            && (block.getAttribute('data-variation-type') || '') === 'label_type'
                            && (block.getAttribute('data-allows-multiple') || '') === '1';
                    }

                    function parseLabelSubPayloads(block) {
                        if (!block) return {};
                        try {
                            var raw = block.getAttribute('data-label-sub-payloads') || '{}';
                            var obj = JSON.parse(raw);
                            return (obj && typeof obj === 'object' && !Array.isArray(obj)) ? obj : {};
                        } catch (e) {
                            return {};
                        }
                    }

                    function saveLabelSubPayloads(block, map) {
                        if (!block) return;
                        block.setAttribute('data-label-sub-payloads', JSON.stringify(map || {}));
                    }

                    function getLabelSubPayloadKey(optionEl) {
                        if (!optionEl) return '';
                        return String(optionEl.getAttribute('data-option-id') || optionEl.getAttribute('data-option') || '').trim();
                    }

                    function getLabelTypeSelectedOptions(block) {
                        var out = [];
                        if (!block) return out;
                        block.querySelectorAll('.product-option.option-selected').forEach(function(b) {
                            if (b.style.display === 'none') return;
                            out.push(b);
                        });
                        return out;
                    }

                    function getLabelTypeSubFlowQueue(block) {
                        var queue = [];
                        getLabelTypeSelectedOptions(block).forEach(function(opt) {
                            if (labelOptionNeedsSubOptions(opt)) queue.push(opt);
                        });
                        return queue;
                    }

                    function getLabelTypeCurrentSubFlowOption(block) {
                        var idx = parseInt(block.getAttribute('data-label-queue-index') || '0', 10);
                        var queue = getLabelTypeSubFlowQueue(block);
                        return queue[idx] || null;
                    }

                    function getLabelTypeActiveSubFlowOption(block) {
                        if (isLabelTypeMulti(block) && (block.getAttribute('data-label-sub-flow-active') || '') === '1') {
                            return getLabelTypeCurrentSubFlowOption(block);
                        }
                        return getLabelTypeSelectedOption(block);
                    }

                    function resetLabelTypeMultiSubFlow(block) {
                        if (!block) return;
                        block.setAttribute('data-label-sub-flow-active', '0');
                        block.setAttribute('data-label-queue-index', '0');
                        saveLabelSubPayloads(block, {});
                    }

                    function getLabelTypeSelectedOption(block) {
                        return getVisibleSelectedProductOption(block);
                    }

                    function clearLabelTypeChoiceButtons(wrap) {
                        if (!wrap) return;
                        wrap.querySelectorAll('.label-type-custom-print-btn, .label-type-custom-print-artwork-btn, .label-type-position-btn').forEach(function(b) {
                            setProductOptionVisual(b, false);
                            b.removeAttribute('data-selected');
                        });
                        var descInput = wrap.querySelector('.label-type-description-input');
                        if (descInput) descInput.value = '';
                    }

                    function applyLabelSubPayloadToWrap(wrap, sel, payload) {
                        if (!wrap || !sel || !payload || typeof payload !== 'object') return;
                        if ((sel.getAttribute('data-label-custom-print') || '') === '1') {
                            if (payload.custom_print === true) {
                                wrap.querySelectorAll('.label-type-custom-print-btn').forEach(function(b) {
                                    var on = (b.getAttribute('data-value') || '') === '1';
                                    setProductOptionVisual(b, on);
                                    if (on) b.setAttribute('data-selected', '1');
                                    else b.removeAttribute('data-selected');
                                });
                                syncLabelTypeArtworkSection(wrap);
                                if (payload.custom_print_artwork) {
                                    wrap.querySelectorAll('.label-type-custom-print-artwork-btn').forEach(function(b) {
                                        var on = (b.getAttribute('data-artwork') || '') === String(payload.custom_print_artwork);
                                        setProductOptionVisual(b, on);
                                        if (on) b.setAttribute('data-selected', '1');
                                        else b.removeAttribute('data-selected');
                                    });
                                }
                            } else if (payload.custom_print === false) {
                                wrap.querySelectorAll('.label-type-custom-print-btn').forEach(function(b) {
                                    var on = (b.getAttribute('data-value') || '') === '0';
                                    setProductOptionVisual(b, on);
                                    if (on) b.setAttribute('data-selected', '1');
                                    else b.removeAttribute('data-selected');
                                });
                                syncLabelTypeArtworkSection(wrap);
                            }
                        }
                        if (payload.positions && payload.positions.length) {
                            wrap.querySelectorAll('.label-type-position-btn').forEach(function(b) {
                                var p = b.getAttribute('data-position');
                                var on = payload.positions.indexOf(p) !== -1;
                                setProductOptionVisual(b, on);
                                if (on) b.setAttribute('data-selected', '1');
                                else b.removeAttribute('data-selected');
                            });
                        }
                        if (payload.description && typeof payload.description === 'string') {
                            var descInput = wrap.querySelector('.label-type-description-input');
                            if (descInput) descInput.value = payload.description;
                        }
                    }

                    function syncLabelTypeArtworkSection(wrap) {
                        if (!wrap) return;
                        var section = wrap.querySelector('.label-type-custom-print-artwork-section');
                        var yesSelected = wrap.querySelector('.label-type-custom-print-btn[data-value="1"][data-selected="1"]');
                        var noSelected = wrap.querySelector('.label-type-custom-print-btn[data-value="0"][data-selected="1"]');
                        var standardInfo = wrap.querySelector('.label-type-standard-wash-info');
                        if (standardInfo) {
                            var showStandard = !!noSelected;
                            standardInfo.classList.toggle('hidden', !showStandard);
                            if (showStandard) {
                                requestAnimationFrame(function() {
                                    standardInfo.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                });
                            }
                        }
                        if (!section) return;
                        var show = !!yesSelected;
                        section.classList.toggle('hidden', !show);
                        if (!show) {
                            section.querySelectorAll('.label-type-custom-print-artwork-btn').forEach(function(b) {
                                setProductOptionVisual(b, false);
                                b.removeAttribute('data-selected');
                            });
                        }
                    }

                    function labelTypeArtworkSummaryLabel(artworkKey) {
                        if (artworkKey === 'customer_send') {
                            return PU.label_custom_print_artwork_summary_customer || 'Görsel: Ben göndereceğim';
                        }
                        if (artworkKey === 'company_prepare') {
                            return PU.label_custom_print_artwork_summary_company || 'Görsel: Siz hazırlayın';
                        }
                        return '';
                    }

                    function resetLabelTypeSubOptions(block) {
                        if (!block) return;
                        block.setAttribute('data-label-options-confirmed', '0');
                        resetLabelTypeMultiSubFlow(block);
                        var wrap = block.querySelector('.label-type-suboptions-wrap');
                        if (!wrap) return;
                        wrap.classList.add('hidden');
                        wrap.removeAttribute('data-label-sync-option-key');
                        clearLabelTypeChoiceButtons(wrap);
                        var heading = wrap.querySelector('.label-type-suboptions-heading');
                        if (heading) {
                            heading.textContent = '';
                            heading.classList.add('hidden');
                        }
                        var cont = wrap.querySelector('.label-type-continue-btn');
                        if (cont) cont.disabled = true;
                    }

                    function formatLabelTypeSelectionDisplay(payload) {
                        if (!payload || typeof payload !== 'object') return '';
                        if (typeof payload === 'string') return payload;
                        var parts = [String(payload.option || '').trim()];
                        if (payload.custom_print === true) {
                            parts.push(PU.label_custom_print_summary_yes || 'Özel baskı: Evet');
                            if (payload.custom_print_artwork) {
                                var artworkLabel = labelTypeArtworkSummaryLabel(payload.custom_print_artwork);
                                if (artworkLabel) parts.push(artworkLabel);
                            }
                        } else if (payload.custom_print === false) {
                            parts.push(PU.label_custom_print_summary_no || 'Özel baskı: Hayır');
                        }
                        if (payload.positions && payload.positions.length) {
                            var posLabels = payload.positions.map(function(p) {
                                if (p === 'front') return PU.label_position_front || 'Ön';
                                if (p === 'back') return PU.label_position_back || 'Arka';
                                return '';
                            }).filter(Boolean);
                            if (posLabels.length) {
                                var tpl = PU.label_position_summary || 'Konum: :positions';
                                parts.push(tpl.replace(':positions', posLabels.join(', ')));
                            }
                        }
                        if (payload.description && String(payload.description).trim()) {
                            var descTpl = PU.label_description_summary || 'Açıklama: :text';
                            parts.push(descTpl.replace(':text', String(payload.description).trim()));
                        }
                        return parts.filter(Boolean).join(' · ');
                    }

                    function buildLabelTypePayloadFromWrap(block, sel, wrap) {
                        wrap = wrap || (block ? block.querySelector('.label-type-suboptions-wrap') : null);
                        if (!sel) return null;
                        var optionVal = (sel.getAttribute('data-option') || '').trim();
                        if (!optionVal) return null;
                        if (!labelOptionNeedsSubOptions(sel)) return optionVal;
                        if (!wrap) return null;
                        var payload = { option: optionVal };
                        if ((sel.getAttribute('data-label-custom-print') || '') === '1') {
                            var yesBtn = wrap.querySelector('.label-type-custom-print-btn[data-value="1"][data-selected="1"]');
                            var noBtn = wrap.querySelector('.label-type-custom-print-btn[data-value="0"][data-selected="1"]');
                            if (yesBtn) {
                                payload.custom_print = true;
                                var artworkBtn = wrap.querySelector('.label-type-custom-print-artwork-btn[data-selected="1"]');
                                if (artworkBtn) {
                                    payload.custom_print_artwork = artworkBtn.getAttribute('data-artwork') || '';
                                }
                            } else if (noBtn) {
                                payload.custom_print = false;
                            }
                        }
                        var positions = [];
                        wrap.querySelectorAll('.label-type-position-btn:not(.hidden)[data-selected="1"]').forEach(function(b) {
                            var pos = b.getAttribute('data-position');
                            if (pos) positions.push(pos);
                        });
                        if (positions.length) payload.positions = positions;
                        if (labelOptionAskDescription(sel)) {
                            var descInput = wrap.querySelector('.label-type-description-input');
                            var descVal = descInput ? String(descInput.value || '').trim() : '';
                            if (descVal) payload.description = descVal;
                        }
                        return payload;
                    }

                    function formatLabelTypeMultiSummary(block) {
                        var payloads = parseLabelSubPayloads(block);
                        var parts = [];
                        getLabelTypeSelectedOptions(block).forEach(function(opt) {
                            var key = getLabelSubPayloadKey(opt);
                            if (!key || payloads[key] === undefined) return;
                            var p = payloads[key];
                            parts.push(typeof p === 'string' ? p : formatLabelTypeSelectionDisplay(p));
                        });
                        return parts.filter(Boolean).join(' · ');
                    }

                    function buildLabelTypeVariationPayload(block) {
                        if (isLabelTypeMulti(block)) {
                            if ((block.getAttribute('data-label-options-confirmed') || '') !== '1') return null;
                            var payloads = parseLabelSubPayloads(block);
                            var result = [];
                            getLabelTypeSelectedOptions(block).forEach(function(opt) {
                                var key = getLabelSubPayloadKey(opt);
                                if (key && payloads[key] !== undefined) result.push(payloads[key]);
                            });
                            return result.length ? result : null;
                        }
                        var sel = getLabelTypeSelectedOption(block);
                        if (!sel) return null;
                        var optionVal = (sel.getAttribute('data-option') || '').trim();
                        if (!optionVal) return null;
                        if (!labelOptionNeedsSubOptions(sel)) return optionVal;
                        if ((block.getAttribute('data-label-options-confirmed') || '') !== '1') return null;
                        return buildLabelTypePayloadFromWrap(block, sel);
                    }

                    function isLabelTypeSubOptionsValid(block) {
                        var sel = getLabelTypeActiveSubFlowOption(block);
                        if (!sel) return false;
                        if (!labelOptionNeedsSubOptions(sel)) return true;
                        var wrap = block.querySelector('.label-type-suboptions-wrap');
                        if (!wrap) return false;
                        if ((sel.getAttribute('data-label-custom-print') || '') === '1') {
                            var customPicked = wrap.querySelector('.label-type-custom-print-btn[data-selected="1"]');
                            if (!customPicked) return false;
                            var yesPrint = wrap.querySelector('.label-type-custom-print-btn[data-value="1"][data-selected="1"]');
                            if (yesPrint && !wrap.querySelector('.label-type-custom-print-artwork-btn[data-selected="1"]')) return false;
                        }
                        var front = (sel.getAttribute('data-label-position-front') || '') === '1';
                        var back = (sel.getAttribute('data-label-position-back') || '') === '1';
                        if (front || back) {
                            var picked = wrap.querySelector('.label-type-position-btn:not(.hidden)[data-selected="1"]');
                            if (!picked) return false;
                        }
                        if (labelOptionAskDescription(sel)) {
                            var descInput = wrap.querySelector('.label-type-description-input');
                            if (!descInput || !String(descInput.value || '').trim()) return false;
                        }
                        return true;
                    }

                    function updateLabelTypeSuboptionsHeading(block, sel, wrap) {
                        if (!wrap || !sel) return;
                        var heading = wrap.querySelector('.label-type-suboptions-heading');
                        if (!heading) return;
                        var optionName = optionDisplayLabel(sel);
                        if (!optionName || !labelOptionNeedsSubOptions(sel)) {
                            heading.textContent = '';
                            return;
                        }
                        if (isLabelTypeMulti(block) && (block.getAttribute('data-label-sub-flow-active') || '') === '1') {
                            var queue = getLabelTypeSubFlowQueue(block);
                            var idx = parseInt(block.getAttribute('data-label-queue-index') || '0', 10);
                            var tpl = PU.label_subflow_heading || ':name (:current/:total)';
                            heading.textContent = tpl
                                .replace(':name', optionName)
                                .replace(':current', String(idx + 1))
                                .replace(':total', String(queue.length));
                            return;
                        }
                        heading.textContent = optionName;
                    }

                    function scrollLabelSuboptionsIntoView(wrap) {
                        if (!wrap || wrap.classList.contains('hidden')) return;
                        requestAnimationFrame(function() {
                            var panel = wrap.querySelector('.label-type-suboptions-panel') || wrap;
                            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        });
                    }

                    function updateLabelTypePanelSummary(block) {
                        if (isLabelTypeMulti(block)) {
                            if ((block.getAttribute('data-label-options-confirmed') || '') === '1') {
                                updatePanelSummary(block, formatLabelTypeMultiSummary(block) || '—');
                                return;
                            }
                            var names = getLabelTypeSelectedOptions(block).map(function(opt) {
                                return (opt.getAttribute('data-option') || '').trim();
                            }).filter(Boolean);
                            updatePanelSummary(block, names.join(', ') || '—');
                            return;
                        }
                        var sel = getLabelTypeSelectedOption(block);
                        if (!sel) {
                            updatePanelSummary(block, '—');
                            return;
                        }
                        var optionVal = (sel.getAttribute('data-option') || '').trim();
                        if (!labelOptionNeedsSubOptions(sel) || (block.getAttribute('data-label-options-confirmed') || '') !== '1') {
                            updatePanelSummary(block, optionVal);
                            return;
                        }
                        var payload = buildLabelTypeVariationPayload(block);
                        updatePanelSummary(block, formatLabelTypeSelectionDisplay(payload) || optionVal);
                    }

                    function syncLabelTypeSubOptionsPanel(block) {
                        var wrap = block.querySelector('.label-type-suboptions-wrap');
                        if (!wrap) return;
                        var sel = getLabelTypeActiveSubFlowOption(block);
                        if (!sel) {
                            resetLabelTypeSubOptions(block);
                            return;
                        }
                        if (!labelOptionNeedsSubOptions(sel)) {
                            if (!isLabelTypeMulti(block)) {
                                block.setAttribute('data-label-options-confirmed', '1');
                                wrap.classList.add('hidden');
                                updateLabelTypePanelSummary(block);
                            }
                            return;
                        }
                        if (!isLabelTypeMulti(block) || (block.getAttribute('data-label-sub-flow-active') || '') === '1') {
                            block.setAttribute('data-label-options-confirmed', '0');
                            wrap.classList.remove('hidden');
                            var syncKey = getLabelSubPayloadKey(sel);
                            var prevKey = wrap.getAttribute('data-label-sync-option-key') || '';
                            if (syncKey !== prevKey) {
                                clearLabelTypeChoiceButtons(wrap);
                                wrap.setAttribute('data-label-sync-option-key', syncKey);
                                var savedPayloads = parseLabelSubPayloads(block);
                                if (syncKey && savedPayloads[syncKey]) {
                                    applyLabelSubPayloadToWrap(wrap, sel, savedPayloads[syncKey]);
                                }
                            }
                            var customSection = wrap.querySelector('.label-type-custom-print-section');
                            var needsCustom = (sel.getAttribute('data-label-custom-print') || '') === '1';
                            if (customSection) customSection.classList.toggle('hidden', !needsCustom);
                            syncLabelTypeArtworkSection(wrap);
                            var posSection = wrap.querySelector('.label-type-position-section');
                            var front = (sel.getAttribute('data-label-position-front') || '') === '1';
                            var back = (sel.getAttribute('data-label-position-back') || '') === '1';
                            var needsPos = front || back;
                            if (posSection) posSection.classList.toggle('hidden', !needsPos);
                            wrap.querySelectorAll('.label-type-position-btn').forEach(function(b) {
                                var p = b.getAttribute('data-position');
                                var show = (p === 'front' && front) || (p === 'back' && back);
                                b.classList.toggle('hidden', !show);
                            });
                            syncLabelTypeDescriptionSection(wrap, sel);
                            updateLabelTypeSuboptionsHeading(block, sel, wrap);
                            updateLabelTypeContinueButton(block);
                            scrollLabelSuboptionsIntoView(wrap);
                            if (isLabelTypeMulti(block)) updateMultiContinueUi(block);
                        }
                    }

                    function startLabelTypeMultiSubFlow(block) {
                        if (!isLabelTypeMulti(block)) return false;
                        block.setAttribute('data-multi-confirmed', '1');
                        var payloads = {};
                        getLabelTypeSelectedOptions(block).forEach(function(opt) {
                            if (!labelOptionNeedsSubOptions(opt)) {
                                var key = getLabelSubPayloadKey(opt);
                                var val = (opt.getAttribute('data-option') || '').trim();
                                if (key && val) payloads[key] = val;
                            }
                        });
                        saveLabelSubPayloads(block, payloads);
                        var queue = getLabelTypeSubFlowQueue(block);
                        if (queue.length === 0) {
                            block.setAttribute('data-label-sub-flow-active', '0');
                            block.setAttribute('data-label-options-confirmed', '1');
                            updateLabelTypePanelSummary(block);
                            syncVariationJsonFromSelections();
                            advanceFromLabelStepToPackaging(block);
                            return true;
                        }
                        block.setAttribute('data-label-sub-flow-active', '1');
                        block.setAttribute('data-label-queue-index', '0');
                        block.setAttribute('data-label-options-confirmed', '0');
                        syncLabelTypeSubOptionsPanel(block);
                        updateLabelTypeContinueButton(block);
                        updateLabelTypePanelSummary(block);
                        updateMultiContinueUi(block);
                        var subWrap = block.querySelector('.label-type-suboptions-wrap');
                        if (subWrap) scrollLabelSuboptionsIntoView(subWrap);
                        return true;
                    }

                    function advanceLabelTypeMultiSubFlow(block) {
                        var sel = getLabelTypeCurrentSubFlowOption(block);
                        if (!sel || !isLabelTypeSubOptionsValid(block)) return false;
                        var key = getLabelSubPayloadKey(sel);
                        var payloads = parseLabelSubPayloads(block);
                        payloads[key] = buildLabelTypePayloadFromWrap(block, sel);
                        saveLabelSubPayloads(block, payloads);
                        var idx = parseInt(block.getAttribute('data-label-queue-index') || '0', 10);
                        var queue = getLabelTypeSubFlowQueue(block);
                        if (idx + 1 < queue.length) {
                            block.setAttribute('data-label-queue-index', String(idx + 1));
                            syncLabelTypeSubOptionsPanel(block);
                            updateLabelTypeContinueButton(block);
                            updateLabelTypePanelSummary(block);
                            updateMultiContinueUi(block);
                            return true;
                        }
                        block.setAttribute('data-label-sub-flow-active', '0');
                        block.setAttribute('data-label-options-confirmed', '1');
                        var wrap = block.querySelector('.label-type-suboptions-wrap');
                        if (wrap) wrap.classList.add('hidden');
                        updateLabelTypePanelSummary(block);
                        syncVariationJsonFromSelections();
                        advanceFromLabelStepToPackaging(block);
                        return true;
                    }

                    function updateLabelTypeContinueButton(block) {
                        var wrap = block ? block.querySelector('.label-type-suboptions-wrap') : null;
                        if (!wrap) return;
                        var cont = wrap.querySelector('.label-type-continue-btn');
                        if (cont) cont.disabled = !isLabelTypeSubOptionsValid(block);
                    }

                    function getPackagingTypeSelectedOption(block) {
                        return getVisibleSelectedProductOption(block);
                    }

                    function packagingOptionRequiresMaterial(btn) {
                        return (btn.getAttribute('data-packaging-requires-material') || '') === '1';
                    }

                    function packagingHasStickerDesignSection(wrap) {
                        return !!(wrap && wrap.querySelector('.packaging-type-sticker-design-section'));
                    }

                    function resetPackagingTypeSubOptions(block) {
                        if (!block) return;
                        block.setAttribute('data-packaging-options-confirmed', '0');
                        var wrap = block.querySelector('.packaging-type-suboptions-wrap');
                        if (!wrap) return;
                        wrap.classList.add('hidden');
                        wrap.querySelectorAll('.packaging-type-material-btn, .packaging-type-customization-btn, .packaging-type-sticker-design-btn').forEach(function(b) {
                            setProductOptionVisual(b, false);
                            b.removeAttribute('data-selected');
                        });
                        var cont = wrap.querySelector('.packaging-type-continue-btn');
                        if (cont) cont.disabled = true;
                    }

                    function selectDefaultPackagingCustomization(wrap) {
                        if (!wrap) return;
                        var defaultBtn = wrap.querySelector('.packaging-type-customization-btn[data-is-default="1"]')
                            || wrap.querySelector('.packaging-type-customization-btn');
                        if (!defaultBtn) return;
                        wrap.querySelectorAll('.packaging-type-customization-btn').forEach(function(b) {
                            var on = b === defaultBtn;
                            setProductOptionVisual(b, on);
                            if (on) b.setAttribute('data-selected', '1');
                            else b.removeAttribute('data-selected');
                        });
                    }

                    function syncPackagingTypeSubOptionsPanel(block) {
                        var wrap = block.querySelector('.packaging-type-suboptions-wrap');
                        if (!wrap) return;
                        var sel = getPackagingTypeSelectedOption(block);
                        if (!sel) {
                            resetPackagingTypeSubOptions(block);
                            return;
                        }
                        if ((block.getAttribute('data-packaging-options-confirmed') || '') === '1') {
                            updatePackagingTypeContinueButton(block);
                            return;
                        }
                        block.setAttribute('data-packaging-options-confirmed', '0');
                        wrap.classList.remove('hidden');
                        var materialSection = wrap.querySelector('.packaging-type-material-section');
                        var needsMaterial = packagingOptionRequiresMaterial(sel);
                        if (materialSection) materialSection.classList.toggle('hidden', !needsMaterial);
                        if (needsMaterial) {
                            wrap.querySelectorAll('.packaging-type-material-btn').forEach(function(b) {
                                setProductOptionVisual(b, false);
                                b.removeAttribute('data-selected');
                            });
                        }
                        wrap.querySelectorAll('.packaging-type-customization-btn').forEach(function(b) {
                            setProductOptionVisual(b, false);
                            b.removeAttribute('data-selected');
                        });
                        wrap.querySelectorAll('.packaging-type-sticker-design-btn').forEach(function(b) {
                            setProductOptionVisual(b, false);
                            b.removeAttribute('data-selected');
                        });
                        updatePackagingTypeContinueButton(block);
                    }

                    function isPackagingTypeSubOptionsValid(block) {
                        var sel = getPackagingTypeSelectedOption(block);
                        if (!sel) return false;
                        var wrap = block.querySelector('.packaging-type-suboptions-wrap');
                        if (!wrap) return false;
                        if (packagingOptionRequiresMaterial(sel)) {
                            if (!wrap.querySelector('.packaging-type-material-btn[data-selected="1"]')) return false;
                        }
                        var customizationBtns = wrap.querySelectorAll('.packaging-type-customization-btn');
                        if (customizationBtns.length > 0 && !wrap.querySelector('.packaging-type-customization-btn[data-selected="1"]')) {
                            return false;
                        }
                        if (packagingHasStickerDesignSection(wrap) && !wrap.querySelector('.packaging-type-sticker-design-btn[data-selected="1"]')) {
                            return false;
                        }
                        return true;
                    }

                    function updatePackagingTypeContinueButton(block) {
                        var wrap = block ? block.querySelector('.packaging-type-suboptions-wrap') : null;
                        if (!wrap) return;
                        var cont = wrap.querySelector('.packaging-type-continue-btn');
                        if (cont) cont.disabled = !isPackagingTypeSubOptionsValid(block);
                    }

                    function buildPackagingTypeVariationPayload(block) {
                        var sel = getPackagingTypeSelectedOption(block);
                        if (!sel) return null;
                        var optionVal = (sel.getAttribute('data-option') || '').trim();
                        if (!optionVal) return null;
                        if ((block.getAttribute('data-packaging-options-confirmed') || '') !== '1') return null;
                        var wrap = block.querySelector('.packaging-type-suboptions-wrap');
                        if (!wrap) return { option: optionVal };
                        var payload = {
                            option: optionVal,
                            packaging_slug: sel.getAttribute('data-packaging-slug') || '',
                            extra_price_try: 0
                        };
                        var materialBtn = wrap.querySelector('.packaging-type-material-btn[data-selected="1"]');
                        if (materialBtn) {
                            payload.material = materialBtn.getAttribute('data-material-name') || '';
                            payload.material_slug = materialBtn.getAttribute('data-material-slug') || '';
                        }
                        var customizationBtn = wrap.querySelector('.packaging-type-customization-btn[data-selected="1"]');
                        if (customizationBtn) {
                            payload.customization = customizationBtn.getAttribute('data-customization-slug') || '';
                            payload.customization_label = customizationBtn.getAttribute('data-customization-name') || '';
                            payload.extra_price_try += parseFloat(customizationBtn.getAttribute('data-extra-price') || '0') || 0;
                        }
                        var stickerBtn = wrap.querySelector('.packaging-type-sticker-design-btn[data-selected="1"]');
                        if (stickerBtn) {
                            payload.barcode_area = true;
                            var catalog = window.packagingCatalog || {};
                            var barcodeCfg = catalog.barcode || {};
                            payload.extra_price_try += parseFloat(barcodeCfg.extra_price || 0) || 0;
                            payload.sticker_design = stickerBtn.getAttribute('data-sticker-design') || '';
                            payload.sticker_design_label = stickerBtn.getAttribute('data-sticker-label') || '';
                        } else {
                            payload.barcode_area = false;
                        }
                        if (!isFinite(payload.extra_price_try)) payload.extra_price_try = 0;
                        payload.extra_price_try = Math.max(0, payload.extra_price_try);
                        return payload;
                    }

                    function formatPackagingTypeSelectionDisplay(payload) {
                        if (!payload || typeof payload !== 'object') return '';
                        if (typeof payload === 'string') return payload;
                        var parts = [String(payload.option || '').trim()];
                        if (payload.material) {
                            var matTpl = PU.packaging_material_summary || 'Malzeme: :material';
                            parts.push(matTpl.replace(':material', payload.material));
                        }
                        if (payload.customization_label) {
                            parts.push(String(payload.customization_label));
                        }
                        if (payload.barcode_area) {
                            if (payload.sticker_design_label) {
                                parts.push(String(payload.sticker_design_label));
                            } else {
                                parts.push(PU.packaging_barcode_summary_yes || 'Barkod / etiket alanı talep edildi');
                            }
                        }
                        return parts.filter(Boolean).join(' · ');
                    }

                    function updatePackagingTypePanelSummary(block) {
                        var sel = getPackagingTypeSelectedOption(block);
                        if (!sel) {
                            updatePanelSummary(block, '—');
                            return;
                        }
                        var optionVal = (sel.getAttribute('data-option') || '').trim();
                        if ((block.getAttribute('data-packaging-options-confirmed') || '') !== '1') {
                            updatePanelSummary(block, optionVal);
                            return;
                        }
                        var payload = buildPackagingTypeVariationPayload(block);
                        updatePanelSummary(block, formatPackagingTypeSelectionDisplay(payload) || optionVal);
                    }

                    function getDeliverySubOptionsForPreset(presetId) {
                        var catalog = window.deliveryCatalog || {};
                        var map = catalog.sub_options || {};
                        var key = String(presetId || '');
                        return Array.isArray(map[key]) ? map[key] : [];
                    }

                    function resetDeliveryTypeSubOptions(block) {
                        if (!block) return;
                        block.setAttribute('data-delivery-options-confirmed', '0');
                        var wrap = block.querySelector('.delivery-type-suboptions-wrap');
                        if (!wrap) return;
                        wrap.classList.add('hidden');
                        var list = wrap.querySelector('.delivery-type-suboptions-list');
                        if (list) list.innerHTML = '';
                        var info = wrap.querySelector('.delivery-type-suboption-info');
                        if (info) info.classList.add('hidden');
                        var infoText = wrap.querySelector('.delivery-type-suboption-info-text');
                        if (infoText) infoText.textContent = '';
                        var cont = wrap.querySelector('.delivery-type-continue-btn');
                        if (cont) cont.disabled = true;
                    }

                    function updateDeliverySubOptionInfo(wrap) {
                        if (!wrap) return;
                        var selected = wrap.querySelector('.delivery-type-suboption-btn[data-selected="1"]');
                        var info = wrap.querySelector('.delivery-type-suboption-info');
                        var infoText = wrap.querySelector('.delivery-type-suboption-info-text');
                        if (!info || !infoText) return;
                        var desc = selected ? (selected.getAttribute('data-suboption-description') || '').trim() : '';
                        if (desc) {
                            infoText.textContent = desc;
                            info.classList.remove('hidden');
                        } else {
                            infoText.textContent = '';
                            info.classList.add('hidden');
                        }
                    }

                    function selectDefaultDeliverySubOption(wrap) {
                        if (!wrap) return;
                        var list = wrap.querySelector('.delivery-type-suboptions-list');
                        if (!list) return;
                        var defaultBtn = list.querySelector('.delivery-type-suboption-btn[data-is-default="1"]')
                            || list.querySelector('.delivery-type-suboption-btn');
                        if (!defaultBtn) return;
                        list.querySelectorAll('.delivery-type-suboption-btn').forEach(function(b) {
                            var on = b === defaultBtn;
                            setProductOptionVisual(b, on);
                            if (on) b.setAttribute('data-selected', '1');
                            else b.removeAttribute('data-selected');
                        });
                        updateDeliverySubOptionInfo(wrap);
                    }

                    function syncDeliveryTypeSubOptionsPanel(block) {
                        var wrap = block.querySelector('.delivery-type-suboptions-wrap');
                        if (!wrap) return;
                        var sel = getVisibleSelectedProductOption(block);
                        if (!sel) {
                            resetDeliveryTypeSubOptions(block);
                            return;
                        }
                        if ((block.getAttribute('data-delivery-options-confirmed') || '') === '1') {
                            updateDeliveryTypeContinueButton(block);
                            return;
                        }
                        var presetId = sel.getAttribute('data-delivery-preset-id') || '';
                        var subOptions = getDeliverySubOptionsForPreset(presetId);
                        if (!subOptions.length) {
                            block.setAttribute('data-delivery-options-confirmed', '1');
                            wrap.classList.add('hidden');
                            updateDeliveryTypeContinueButton(block);
                            return;
                        }
                        block.setAttribute('data-delivery-options-confirmed', '0');
                        wrap.classList.remove('hidden');
                        var heading = wrap.querySelector('.delivery-type-suboptions-heading');
                        if (heading) heading.textContent = optionDisplayLabel(sel);
                        updateDeliverySubPanelEstimate(wrap, sel);
                        var list = wrap.querySelector('.delivery-type-suboptions-list');
                        if (list) {
                            list.innerHTML = '';
                            subOptions.forEach(function(opt) {
                                var btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'delivery-type-suboption-btn w-full text-left px-4 py-3 rounded-xl border-2 border-slate-300 text-sm sm:text-base font-semibold text-slate-700 hover:bg-slate-50 min-h-[3rem] flex items-center justify-between gap-3';
                                btn.setAttribute('data-suboption-id', String(opt.id || ''));
                                btn.setAttribute('data-suboption-name', String(opt.name || ''));
                                btn.setAttribute('data-suboption-description', String(opt.description || ''));
                                btn.setAttribute('data-is-default', opt.is_default ? '1' : '0');
                                var subMult = parseFloat(opt.price_multiplier);
                                if (!isFinite(subMult) || subMult <= 0) subMult = 1;
                                btn.setAttribute('data-price-multiplier', String(subMult));
                                var multHtml = subMult > 0 && Math.abs(subMult - 1) > 0.0001
                                    ? '<span class="shrink-0 text-xs font-semibold text-slate-500">' + escapeHtml(formatVariationMultiplier(subMult)) + '</span>'
                                    : '';
                                btn.innerHTML = '<span class="min-w-0">' + escapeHtml(String(opt.label || opt.name || '')) + '</span>' + multHtml;
                                list.appendChild(btn);
                            });
                            selectDefaultDeliverySubOption(wrap);
                        }
                        updateDeliveryTypeContinueButton(block);
                    }

                    function isDeliveryTypeSubOptionsValid(block) {
                        var sel = getVisibleSelectedProductOption(block);
                        if (!sel) return false;
                        var presetId = sel.getAttribute('data-delivery-preset-id') || '';
                        var subOptions = getDeliverySubOptionsForPreset(presetId);
                        if (!subOptions.length) return true;
                        var wrap = block.querySelector('.delivery-type-suboptions-wrap');
                        if (!wrap) return false;
                        return !!wrap.querySelector('.delivery-type-suboption-btn[data-selected="1"]');
                    }

                    function updateDeliveryTypeContinueButton(block) {
                        var wrap = block ? block.querySelector('.delivery-type-suboptions-wrap') : null;
                        if (!wrap) return;
                        var cont = wrap.querySelector('.delivery-type-continue-btn');
                        if (cont) cont.disabled = !isDeliveryTypeSubOptionsValid(block);
                    }

                    function buildDeliveryTypeVariationPayload(block) {
                        var sel = getVisibleSelectedProductOption(block);
                        if (!sel) return null;
                        var optionVal = (sel.getAttribute('data-option') || '').trim();
                        if (!optionVal) return null;
                        if ((block.getAttribute('data-delivery-options-confirmed') || '') !== '1') return null;
                        var payload = {
                            option: optionVal,
                            delivery_preset_id: sel.getAttribute('data-delivery-preset-id') || ''
                        };
                        var est = (sel.getAttribute('data-estimated-delivery-time') || '').trim();
                        if (est) payload.estimated_delivery_time = est;
                        var subOptions = getDeliverySubOptionsForPreset(payload.delivery_preset_id);
                        if (subOptions.length) {
                            var wrap = block.querySelector('.delivery-type-suboptions-wrap');
                            var subBtn = wrap ? wrap.querySelector('.delivery-type-suboption-btn[data-selected="1"]') : null;
                            if (subBtn) {
                                payload.sub_option = subBtn.getAttribute('data-suboption-name') || '';
                                payload.sub_option_id = subBtn.getAttribute('data-suboption-id') || '';
                                var subMult = parseFloat(subBtn.getAttribute('data-price-multiplier'));
                                if (isFinite(subMult) && subMult > 0) payload.sub_option_multiplier = subMult;
                                var desc = (subBtn.getAttribute('data-suboption-description') || '').trim();
                                if (desc) payload.sub_option_description = desc;
                            }
                        }
                        return payload;
                    }

                    function formatDeliveryTypeSelectionDisplay(payload) {
                        if (!payload || typeof payload !== 'object') return '';
                        if (typeof payload === 'string') return payload;
                        var parts = [String(payload.option || '').trim()];
                        if (payload.sub_option) {
                            var tpl = PU.delivery_suboption_summary || 'Alt teslim: :suboption';
                            parts.push(tpl.replace(':suboption', payload.sub_option));
                        }
                        if (payload.estimated_delivery_time) {
                            var estTpl = PU.delivery_estimated_time_panel_prefix || 'Tahmini teslimat süresi:';
                            parts.push(estTpl + ' ' + payload.estimated_delivery_time);
                        }
                        return parts.filter(Boolean).join(' · ');
                    }

                    function updateDeliveryTypePanelSummary(block) {
                        var sel = getVisibleSelectedProductOption(block);
                        if (!sel) {
                            updatePanelSummary(block, '—');
                            return;
                        }
                        var optionVal = (sel.getAttribute('data-option') || '').trim();
                        if ((block.getAttribute('data-delivery-options-confirmed') || '') !== '1') {
                            updatePanelSummary(block, optionVal);
                            return;
                        }
                        var payload = buildDeliveryTypeVariationPayload(block);
                        updatePanelSummary(block, formatDeliveryTypeSelectionDisplay(payload) || optionVal);
                    }

                    function clampVariationStepIndex(requested) {
                        var n = parseInt(requested, 10);
                        if (isNaN(n)) n = 0;
                        var finalPanel = getFinalVariationPanel();
                        if (finalPanel) {
                            var finalIdx = parseInt(finalPanel.getAttribute('data-step-index'), 10);
                            if (!isNaN(finalIdx) && n >= finalIdx && isProductVariationBlockComplete(finalPanel)) {
                                return n < 0 ? 0 : n;
                            }
                        }
                        var max = maxReachableVariationStepIndex();
                        if (n > max) n = max;
                        if (n < 0) n = 0;
                        return n;
                    }

                    function isVariationStepDotLocked(dotIndex) {
                        var stepIndex = dotIndex;
                        var panel = document.querySelector('.variation-step-panel[data-step-index="' + dotIndex + '"]');
                        if (!panel) {
                            var dot = document.querySelector('.variation-step-dot[data-step="' + dotIndex + '"]');
                            if (dot) panel = dot.closest('.variation-step-panel');
                        }
                        if (panel) {
                            var panelIdx = parseInt(panel.getAttribute('data-step-index'), 10);
                            if (!isNaN(panelIdx)) stepIndex = panelIdx;
                        }
                        if (!isVariationStepUnlocked(panel)) return true;
                        return !prerequisitesMetForStepIndex(stepIndex);
                    }

                    function resolveVariationStepPanel(block) {
                        if (!block) return null;
                        if (block.classList && block.classList.contains('variation-step-panel')) return block;
                        return block.closest ? block.closest('.variation-step-panel') : null;
                    }

                    function syncActiveVariationStepPanelLayout(panel) {
                        panel = resolveVariationStepPanel(panel);
                        if (!panel) return;
                        var idx = parseInt(panel.getAttribute('data-step-index'), 10);
                        if (isNaN(idx) || idx !== currentVariationStep) return;
                        var summary = panel.querySelector('.variation-step-summary');
                        var full = panel.querySelector('.variation-step-full');
                        var stepComplete = isVariationStepPanelComplete(panel);
                        if (stepComplete) {
                            if (summary) {
                                summary.classList.remove('hidden');
                                summary.classList.add('flex');
                            }
                            if (full) full.classList.add('hidden');
                        } else {
                            if (summary) {
                                summary.classList.add('hidden');
                                summary.classList.remove('flex');
                            }
                            if (full) {
                                full.classList.remove('hidden');
                                full.style.display = '';
                            }
                        }
                    }

                    function markVariationStepPanelComplete(panel) {
                        panel = resolveVariationStepPanel(panel);
                        if (!panel || !isVariationStepPanelComplete(panel)) return;
                        var summary = panel.querySelector('.variation-step-summary');
                        var full = panel.querySelector('.variation-step-full');
                        var card = panel.querySelector('.variation-step-card');
                        if (summary) {
                            summary.classList.remove('hidden');
                            summary.classList.add('flex');
                        }
                        if (full && !full.classList.contains('size-table-step-content')) {
                            full.classList.add('hidden');
                        }
                        if (card) {
                            card.classList.remove('border-primary-400');
                            card.classList.add('border-slate-200');
                        }
                    }

                    function showVariationSelectionCompleteState() {
                        hideVariationPanelsAfterFinalStep();
                        var finalPanel = getFinalVariationPanel();
                        var finalIdx = finalPanel ? parseInt(finalPanel.getAttribute('data-step-index'), 10) : NaN;
                        if (!isNaN(finalIdx)) {
                            currentVariationStep = finalIdx;
                        } else {
                            currentVariationStep = Math.max(0, totalVariationSteps - 1);
                        }
                        document.querySelectorAll('.variation-step-panel').forEach(function(panel) {
                            panel.style.display = '';
                            if (!isNaN(finalIdx)) {
                                var panelIdx = parseInt(panel.getAttribute('data-step-index'), 10);
                                if (!isNaN(panelIdx) && panelIdx > finalIdx) return;
                            }
                            if (isVariationStepPanelComplete(panel)) {
                                markVariationStepPanelComplete(panel);
                            }
                        });
                        document.querySelectorAll('.variation-step-dot').forEach(function(dot) {
                            var panel = dot.closest('.variation-step-panel');
                            var num = (panel ? panel.querySelector('.variation-step-num') : null) || dot.querySelector('.variation-step-num');
                            var check = dot.querySelector('.variation-step-check');
                            dot.classList.remove('bg-primary-50/80', 'border-primary-100', 'opacity-60', 'cursor-not-allowed', 'pointer-events-none');
                            dot.classList.add('bg-emerald-50/80');
                            if (num) {
                                num.classList.add('bg-emerald-500', 'text-white');
                                num.classList.remove('bg-primary-500', 'bg-slate-200', 'text-slate-600');
                            }
                            if (check) check.classList.remove('hidden');
                            dot.removeAttribute('aria-disabled');
                        });
                        updateVariationSummaryAndButton();
                        var scrollTarget = document.getElementById('variation-summary-wrap') || document.getElementById('variation-confirm-wrap');
                        if (scrollTarget) {
                            scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    }

                    function showVariationStep(stepIndex, opts) {
                        opts = opts || {};
                        var forceEdit = !!opts.forceEdit;
                        var si = clampVariationStepIndex(stepIndex);
                        currentVariationStep = si;
                        document.querySelectorAll('.variation-step-panel').forEach(function(panel) {
                            var idx = parseInt(panel.getAttribute('data-step-index'), 10);
                            var summary = panel.querySelector('.variation-step-summary');
                            var full = panel.querySelector('.variation-step-full');
                            var card = panel.querySelector('.variation-step-card');
                            var isCustPanel = (panel.getAttribute('data-customization-panel') || '') === '1';
                            var custDone = isCustPanel && (panel.getAttribute('data-customization-confirmed') || '') === '1';
                            var stepComplete = isVariationStepPanelComplete(panel);
                            var showSummaryOnly = !forceEdit && ((isCustPanel && custDone) || (!isCustPanel && stepComplete));
                            panel.style.display = '';
                            if (idx === si) {
                                if (showSummaryOnly) {
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
                        document.querySelectorAll('.variation-step-dot').forEach(function(dot) {
                            var panel = dot.closest('.variation-step-panel');
                            var panelStepIdx = parseInt(dot.getAttribute('data-step'), 10);
                            if (isNaN(panelStepIdx) && panel) {
                                panelStepIdx = parseInt(panel.getAttribute('data-step-index'), 10);
                            }
                            if (isNaN(panelStepIdx)) panelStepIdx = 0;
                            var num = (panel ? panel.querySelector('.variation-step-num') : null) || dot.querySelector('.variation-step-num');
                            var check = dot.querySelector('.variation-step-check');
                            dot.classList.remove('bg-primary-50/80', 'border-primary-100', 'bg-emerald-50/80', 'bg-slate-50/80', 'opacity-60', 'cursor-not-allowed', 'pointer-events-none');
                            var locked = isVariationStepDotLocked(panelStepIdx);
                            if (locked) {
                                dot.classList.add('bg-slate-50/80', 'opacity-60', 'cursor-not-allowed', 'pointer-events-none');
                                if (num) { num.classList.add('bg-slate-200', 'text-slate-600'); num.classList.remove('bg-primary-500', 'bg-emerald-500', 'text-white'); }
                                if (check) check.classList.add('hidden');
                                dot.setAttribute('aria-disabled', 'true');
                            } else if (panelStepIdx === si) {
                                dot.classList.add('bg-primary-50/80', 'border-primary-100');
                                if (num) { num.classList.add('bg-primary-500', 'text-white'); num.classList.remove('bg-slate-200', 'text-slate-600', 'bg-emerald-500'); }
                                if (check) check.classList.add('hidden');
                                dot.removeAttribute('aria-disabled');
                            } else if (panelStepIdx < si) {
                                dot.classList.add('bg-emerald-50/80');
                                if (num) { num.classList.add('bg-emerald-500', 'text-white'); num.classList.remove('bg-primary-500', 'bg-slate-200', 'text-slate-600'); }
                                if (check) check.classList.remove('hidden');
                                dot.removeAttribute('aria-disabled');
                            } else if (isVariationStepDotLocked(panelStepIdx)) {
                                dot.classList.add('bg-slate-50/80', 'opacity-60', 'cursor-not-allowed', 'pointer-events-none');
                                if (num) { num.classList.add('bg-slate-200', 'text-slate-600'); num.classList.remove('bg-primary-500', 'bg-emerald-500', 'text-white'); }
                                if (check) check.classList.add('hidden');
                                dot.setAttribute('aria-disabled', 'true');
                            } else {
                                dot.classList.add('bg-slate-50/80', 'hover:bg-slate-100/80');
                                if (num) { num.classList.add('bg-slate-200', 'text-slate-600'); num.classList.remove('bg-primary-500', 'bg-emerald-500', 'text-white'); }
                                if (check) check.classList.add('hidden');
                                dot.removeAttribute('aria-disabled');
                            }
                        });
                        updateCustomizationContinueEnabled();
                        var activePanel = document.querySelector('.variation-step-panel[data-step-index="' + si + '"]');
                        if (activePanel && (activePanel.getAttribute('data-variation-type') || '') === 'size_table') {
                            filterSizeTableVariationOptions(activePanel);
                            syncSizeTableVariationBlock(activePanel);
                        }
                        scheduleApplyDependencyChain();
                    }

                    function invalidateDownstreamStepsAfterVariationEdit(editedBlock) {
                        if (!editedBlock) return;
                        var editedPanel = resolveVariationStepPanel(editedBlock) || editedBlock;
                        var editedIdx = parseInt(editedPanel.getAttribute('data-step-index'), 10);
                        if (isNaN(editedIdx)) return;
                        var meta = getVariationStepsMeta();
                        var custIdx = meta.customizationIdx;

                        document.querySelectorAll('.variation-step-panel').forEach(function(panel) {
                            var panelIdx = parseInt(panel.getAttribute('data-step-index'), 10);
                            if (isNaN(panelIdx)) return;
                            if (panelIdx <= editedIdx) return;
                            if (custIdx >= 0 && panelIdx === custIdx && editedIdx > custIdx) return;

                            if ((panel.getAttribute('data-customization-panel') || '') === '1') {
                                if ((panel.getAttribute('data-customization-confirmed') || '') === '1') {
                                    panel.setAttribute('data-customization-confirmed', '0');
                                    var sum = panel.querySelector('.variation-step-summary');
                                    var sumVal = panel.querySelector('.variation-step-summary-value');
                                    if (sum) {
                                        sum.classList.add('hidden');
                                        sum.classList.remove('flex');
                                    }
                                    if (sumVal) setCustomizationStepSummaryValue(sumVal, null);
                                    var ctbl = document.getElementById('product-customization-table');
                                    if (ctbl) ctbl.querySelectorAll('.customization-row-check').forEach(function(cb) { cb.checked = false; });
                                    setCustomizationSkipSelected(false);
                                    resetCustomizationTableFields();
                                    applyCustomizationChoiceToVariationInput();
                                }
                                return;
                            }

                            if ((panel.getAttribute('data-variation-type') || '') === 'size_table') {
                                panel.setAttribute('data-size-table-confirmed', '0');
                                syncSizeTableVariationBlock(panel);
                            }

                            if ((panel.getAttribute('data-variation-type') || '') === 'label_type') {
                                panel.querySelectorAll('.product-option.option-selected').forEach(function(b) {
                                    setProductOptionVisual(b, false);
                                });
                                resetLabelTypeSubOptions(panel);
                                updatePanelSummary(panel, '—');
                            }

                            if ((panel.getAttribute('data-variation-type') || '') === 'packaging_type') {
                                panel.querySelectorAll('.product-option.option-selected').forEach(function(b) {
                                    setProductOptionVisual(b, false);
                                });
                                resetPackagingTypeSubOptions(panel);
                                updatePanelSummary(panel, '—');
                            }

                            if ((panel.getAttribute('data-variation-type') || '') === 'delivery_type') {
                                panel.querySelectorAll('.product-option.option-selected').forEach(function(b) {
                                    setProductOptionVisual(b, false);
                                });
                                resetDeliveryTypeSubOptions(panel);
                                updatePanelSummary(panel, '—');
                            }

                            if ((panel.getAttribute('data-allows-multiple') || '') === '1') {
                                panel.setAttribute('data-multi-confirmed', '0');
                            }
                        });
                        document.querySelectorAll('.variation-step-panel[data-variation-name]').forEach(function(panel) {
                            panel.style.display = '';
                        });
                        window._variationAllSelectedScrolled = false;
                        scheduleApplyDependencyChain();
                    }

                    function findNextVariationStepIndexAfter(fromIndex) {
                        var finalPanel = getFinalVariationPanel();
                        var finalIdx = NaN;
                        if (finalPanel) {
                            finalIdx = parseInt(finalPanel.getAttribute('data-step-index'), 10);
                        }
                        var ordered = getOrderedVariationStepPanels();
                        for (var i = 0; i < ordered.length; i++) {
                            if (ordered[i].index <= fromIndex) continue;
                            if (!isNaN(finalIdx) && ordered[i].index > finalIdx) continue;
                            if (!prerequisitesMetForStepIndex(ordered[i].index)) return -1;
                            if ((ordered[i].panel.getAttribute('data-step-unlocked') || '') !== '1') continue;
                            if (!isVariationStepPanelComplete(ordered[i].panel)) return ordered[i].index;
                        }
                        return -1;
                    }

                    function maybeAdvanceVariationStepAfterSelection(completedPanel) {
                        if (totalVariationSteps <= 0) return;
                        syncVariationStepUnlockStates();
                        var finalPanel = getFinalVariationPanel();
                        if (completedPanel) {
                            completedPanel = resolveVariationStepPanel(completedPanel) || completedPanel;
                            var completedIdx = parseInt(completedPanel.getAttribute('data-step-index'), 10);
                            if (!isNaN(completedIdx)) {
                                currentVariationStep = completedIdx;
                            }
                            if (finalPanel && completedPanel === finalPanel && isProductVariationBlockComplete(finalPanel)) {
                                showVariationSelectionCompleteState();
                                return;
                            }
                        }
                        if (isFinalVariationFlowTriggered()) {
                            showVariationSelectionCompleteState();
                            return;
                        }
                        var nextIdx = findNextVariationStepIndexAfter(currentVariationStep);
                        if (nextIdx >= 0) {
                            showVariationStep(nextIdx);
                        } else if (allVisibleVariationsSelected()) {
                            showVariationSelectionCompleteState();
                        } else {
                            showVariationStep(currentVariationStep);
                        }
                    }

                    function advanceFromLabelStepToPackaging(labelBlock) {
                        var rootPanel = resolveVariationStepPanel(labelBlock) || labelBlock;
                        if (!rootPanel) return;
                        syncVariationStepUnlockStates();
                        var packagingPanel = document.querySelector('.variation-step-panel[data-variation-type="packaging_type"]');
                        if (packagingPanel && isParentVariationStepReady(rootPanel)) {
                            packagingPanel.setAttribute('data-step-unlocked', '1');
                            packagingPanel.classList.remove('variation-step-locked');
                        }
                        var labelIdx = parseInt(rootPanel.getAttribute('data-step-index'), 10);
                        if (!isNaN(labelIdx)) currentVariationStep = labelIdx;
                        finishVariationStepIfReady(rootPanel);
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
                        invalidateDownstreamStepsAfterVariationEdit(container);
                        var isMulti = (container.getAttribute('data-allows-multiple') || '') === '1';

                        if (isMulti) {
                            var isSolo = (btn.getAttribute('data-option-solo') || '') === '1';
                            if (isSolo) {
                                if (btn.classList.contains('option-selected') && countVisibleSelectedOptions(container) === 1) {
                                    setProductOptionVisual(btn, false);
                                    container.setAttribute('data-multi-confirmed', '0');
                                    updatePanelSummaryMulti(container);
                                    scheduleApplyDependencyChain();
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
                                scheduleApplyDependencyChain();
                                finishVariationStepIfReady(container);
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
                            if ((container.getAttribute('data-variation-type') || '') === 'label_type') {
                                container.setAttribute('data-label-options-confirmed', '0');
                                resetLabelTypeMultiSubFlow(container);
                                var labelWrap = container.querySelector('.label-type-suboptions-wrap');
                                if (labelWrap) labelWrap.classList.add('hidden');
                            }
                            scheduleApplyDependencyChain();
                            updateVariationSummaryAndButton();
                            return;
                        }

                        container.querySelectorAll('.product-option').forEach(function(b) {
                            setProductOptionVisual(b, b === btn);
                        });
                        updatePanelSummary(container, optionValue);
                        if ((container.getAttribute('data-variation-type') || '') === 'size_table') {
                            container.setAttribute('data-size-table-confirmed', '0');
                            syncSizeTableVariationBlock(container);
                        }
                        if ((container.getAttribute('data-variation-type') || '') === 'label_type') {
                            syncLabelTypeSubOptionsPanel(container);
                            updateLabelTypePanelSummary(container);
                            var labelNeedsSub = labelOptionNeedsSubOptions(btn);
                            var labelConfirmed = (container.getAttribute('data-label-options-confirmed') || '') === '1';
                            updateSizeTableVisibility();
                            scheduleApplyDependencyChain();
                            updateVariationSummaryAndButton();
                            return;
                        }
                        if ((container.getAttribute('data-variation-type') || '') === 'packaging_type') {
                            syncPackagingTypeSubOptionsPanel(container);
                            updatePackagingTypePanelSummary(container);
                            updateSizeTableVisibility();
                            scheduleApplyDependencyChain();
                            updateVariationSummaryAndButton();
                            return;
                        }
                        if ((container.getAttribute('data-variation-type') || '') === 'delivery_type') {
                            syncDeliveryTypeSubOptionsPanel(container);
                            updateDeliveryTypePanelSummary(container);
                            if ((container.getAttribute('data-delivery-options-confirmed') || '') === '1') {
                                syncVariationJsonFromSelections();
                                finishVariationStepIfReady(container);
                            }
                            updateSizeTableVisibility();
                            scheduleApplyDependencyChain();
                            updateVariationSummaryAndButton();
                            return;
                        }
                        updateSizeTableVisibility();
                        syncVariationJsonFromSelections();
                        finishVariationStepIfReady(container);
                        scheduleApplyDependencyChain();
                        updateVariationSummaryAndButton();
                    }

                    document.querySelectorAll('.product-option').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            if (this.style.display === 'none') return;
                            selectOption(this);
                        });
                    });

                    document.querySelectorAll('.label-type-custom-print-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var block = btn.closest('.product-variation-block');
                            var wrap = btn.closest('.label-type-suboptions-wrap');
                            if (!wrap || !block) return;
                            wrap.querySelectorAll('.label-type-custom-print-btn').forEach(function(b) {
                                var on = b === btn;
                                setProductOptionVisual(b, on);
                                if (on) b.setAttribute('data-selected', '1');
                                else b.removeAttribute('data-selected');
                            });
                            syncLabelTypeArtworkSection(wrap);
                            updateLabelTypeContinueButton(block);
                            updateVariationSummaryAndButton();
                        });
                    });

                    document.querySelectorAll('.label-type-custom-print-artwork-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var block = btn.closest('.product-variation-block');
                            var wrap = btn.closest('.label-type-suboptions-wrap');
                            if (!wrap || !block) return;
                            wrap.querySelectorAll('.label-type-custom-print-artwork-btn').forEach(function(b) {
                                var on = b === btn;
                                setProductOptionVisual(b, on);
                                if (on) b.setAttribute('data-selected', '1');
                                else b.removeAttribute('data-selected');
                            });
                            updateLabelTypeContinueButton(block);
                            updateVariationSummaryAndButton();
                        });
                    });

                    document.querySelectorAll('.label-type-position-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var block = btn.closest('.product-variation-block');
                            if (!block) return;
                            var turnOn = !btn.classList.contains('option-selected');
                            setProductOptionVisual(btn, turnOn);
                            if (turnOn) btn.setAttribute('data-selected', '1');
                            else btn.removeAttribute('data-selected');
                            updateLabelTypeContinueButton(block);
                        });
                    });

                    document.querySelectorAll('.label-type-description-input').forEach(function(input) {
                        input.addEventListener('input', function() {
                            var block = input.closest('.product-variation-block');
                            if (!block) return;
                            updateLabelTypeContinueButton(block);
                        });
                    });

                    document.querySelectorAll('.label-type-continue-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var block = btn.closest('.product-variation-block');
                            if (!block || !isLabelTypeSubOptionsValid(block)) return;
                            if (isLabelTypeMulti(block) && (block.getAttribute('data-label-sub-flow-active') || '') === '1') {
                                if (!advanceLabelTypeMultiSubFlow(block)) return;
                                scheduleApplyDependencyChain();
                                updateMultiContinueUi(block);
                                updateVariationSummaryAndButton();
                                return;
                            }
                            block.setAttribute('data-label-options-confirmed', '1');
                            updateLabelTypePanelSummary(block);
                            syncVariationJsonFromSelections();
                            advanceFromLabelStepToPackaging(block);
                            scheduleApplyDependencyChain();
                            updateVariationSummaryAndButton();
                        });
                    });

                    document.querySelectorAll('.packaging-type-material-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var block = btn.closest('.product-variation-block');
                            var wrap = btn.closest('.packaging-type-suboptions-wrap');
                            if (!wrap || !block) return;
                            wrap.querySelectorAll('.packaging-type-material-btn').forEach(function(b) {
                                var on = b === btn;
                                setProductOptionVisual(b, on);
                                if (on) b.setAttribute('data-selected', '1');
                                else b.removeAttribute('data-selected');
                            });
                            updatePackagingTypeContinueButton(block);
                        });
                    });

                    document.querySelectorAll('.packaging-type-customization-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var block = btn.closest('.product-variation-block');
                            var wrap = btn.closest('.packaging-type-suboptions-wrap');
                            if (!wrap || !block) return;
                            wrap.querySelectorAll('.packaging-type-customization-btn').forEach(function(b) {
                                var on = b === btn;
                                setProductOptionVisual(b, on);
                                if (on) b.setAttribute('data-selected', '1');
                                else b.removeAttribute('data-selected');
                            });
                            updatePackagingTypeContinueButton(block);
                        });
                    });

                    document.querySelectorAll('.packaging-type-sticker-design-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var block = btn.closest('.product-variation-block');
                            var wrap = btn.closest('.packaging-type-suboptions-wrap');
                            if (!wrap || !block) return;
                            wrap.querySelectorAll('.packaging-type-sticker-design-btn').forEach(function(b) {
                                var on = b === btn;
                                setProductOptionVisual(b, on);
                                if (on) b.setAttribute('data-selected', '1');
                                else b.removeAttribute('data-selected');
                            });
                            updatePackagingTypeContinueButton(block);
                        });
                    });

                    document.querySelectorAll('.packaging-type-continue-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var block = btn.closest('.product-variation-block');
                            if (!block || !isPackagingTypeSubOptionsValid(block)) return;
                            block.setAttribute('data-packaging-options-confirmed', '1');
                            updatePackagingTypePanelSummary(block);
                            syncVariationJsonFromSelections();
                            updatePriceAndInput();
                            finishVariationStepIfReady(block);
                            updateVariationSummaryAndButton();
                        });
                    });

                    document.addEventListener('click', function(e) {
                        var deliverySubBtn = e.target.closest('.delivery-type-suboption-btn');
                        if (deliverySubBtn) {
                            var block = deliverySubBtn.closest('.product-variation-block');
                            var wrap = deliverySubBtn.closest('.delivery-type-suboptions-wrap');
                            if (!wrap || !block) return;
                            wrap.querySelectorAll('.delivery-type-suboption-btn').forEach(function(b) {
                                var on = b === deliverySubBtn;
                                setProductOptionVisual(b, on);
                                if (on) b.setAttribute('data-selected', '1');
                                else b.removeAttribute('data-selected');
                            });
                            updateDeliverySubOptionInfo(wrap);
                            updateDeliveryTypeContinueButton(block);
                            updateVariationSummaryAndButton();
                            return;
                        }
                        var deliveryContinueBtn = e.target.closest('.delivery-type-continue-btn');
                        if (deliveryContinueBtn) {
                            var deliveryBlock = deliveryContinueBtn.closest('.product-variation-block');
                            if (!deliveryBlock || !isDeliveryTypeSubOptionsValid(deliveryBlock)) return;
                            deliveryBlock.setAttribute('data-delivery-options-confirmed', '1');
                            updateDeliveryTypePanelSummary(deliveryBlock);
                            syncVariationJsonFromSelections();
                            updatePriceAndInput();
                            finishVariationStepIfReady(deliveryBlock);
                            updateVariationSummaryAndButton();
                        }
                    });

                    document.querySelectorAll('.variation-multi-continue-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var container = btn.closest('.product-variation-block');
                            if (!container) return;
                            if (countVisibleSelectedOptions(container) < 1) return;
                            if ((container.getAttribute('data-variation-type') || '') === 'label_type' && isLabelTypeMulti(container)) {
                                startLabelTypeMultiSubFlow(container);
                                scheduleApplyDependencyChain();
                                updateMultiContinueUi(container);
                                updateVariationSummaryAndButton();
                                return;
                            }
                            container.setAttribute('data-multi-confirmed', '1');
                            updatePanelSummaryMulti(container);
                            updateSizeTableVisibility();
                            scheduleApplyDependencyChain();
                            finishVariationStepIfReady(container);
                            updateVariationSummaryAndButton();
                        });
                    });

                    document.querySelectorAll('.variation-step-dot').forEach(function(dot) {
                        dot.addEventListener('click', function() {
                            if (this.getAttribute('aria-disabled') === 'true') return;
                            var step = parseInt(this.getAttribute('data-step'), 10);
                            if (isNaN(step)) return;
                            if (isVariationStepDotLocked(step)) return;
                            showVariationStep(step);
                            requestAnimationFrame(function() {
                                if (typeof updateSizeTableVisibility === 'function') updateSizeTableVisibility();
                            });
                        });
                    });

                    document.querySelectorAll('.variation-step-change-btn').forEach(function(btn) {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            var panel = this.closest('.variation-step-panel');
                            if (!panel) return;
                            if ((panel.getAttribute('data-customization-panel') || '') === '1') {
                                panel.setAttribute('data-customization-confirmed', '0');
                                applyCustomizationChoiceToVariationInput();
                            }
                            if ((panel.getAttribute('data-allows-multiple') || '') === '1') {
                                panel.setAttribute('data-multi-confirmed', '0');
                            }
                            var vType = panel.getAttribute('data-variation-type') || '';
                            if (vType === 'label_type') {
                                panel.setAttribute('data-label-options-confirmed', '0');
                                resetLabelTypeSubOptions(panel);
                                syncLabelTypeSubOptionsPanel(panel);
                            }
                            if (vType === 'packaging_type') {
                                panel.setAttribute('data-packaging-options-confirmed', '0');
                                syncPackagingTypeSubOptionsPanel(panel);
                            }
                            if (vType === 'size_table') {
                                panel.setAttribute('data-size-table-confirmed', '0');
                                syncSizeTableVariationBlock(panel);
                            }
                            var step = parseInt(panel.getAttribute('data-step-index'), 10);
                            showVariationStep(step, { forceEdit: true });
                            scheduleApplyDependencyChain();
                            updateMultiContinueUi(panel);
                            updateVariationSummaryAndButton();
                        });
                    });

                    document.addEventListener('click', function(e) {
                        var detailBtn = e.target.closest('.variation-detail-info-btn, .fabric-detail-info-btn');
                        if (detailBtn) {
                            e.preventDefault();
                            e.stopPropagation();
                            var modal = document.getElementById('fabric-detail-modal');
                            var titleEl = document.getElementById('fabric-detail-modal-title');
                            var bodyEl = document.getElementById('fabric-detail-modal-body');
                            if (modal && titleEl && bodyEl) {
                                titleEl.textContent = detailBtn.getAttribute('data-detail-title') || detailBtn.getAttribute('data-fabric-detail-title') || '';
                                bodyEl.textContent = detailBtn.getAttribute('data-detail-text') || detailBtn.getAttribute('data-fabric-detail-text') || '';
                                modal.classList.remove('hidden');
                                modal.classList.add('flex');
                                document.body.style.overflow = 'hidden';
                            }
                            return;
                        }
                        var previewBtn = e.target.closest('.customization-position-preview-btn');
                        if (previewBtn) {
                            e.preventDefault();
                            e.stopPropagation();
                            var previewUrl = previewBtn.getAttribute('data-image-url');
                            var previewTitle = previewBtn.getAttribute('data-image-title') || previewBtn.getAttribute('data-image-alt') || '';
                            var previewAlt = previewBtn.getAttribute('data-image-alt') || previewTitle;
                            var posModal = document.getElementById('customization-position-modal');
                            var posModalTitle = document.getElementById('customization-position-modal-title');
                            var posModalImg = document.getElementById('customization-position-modal-image');
                            if (posModal && posModalTitle && posModalImg && previewUrl) {
                                posModalTitle.textContent = previewTitle;
                                posModalImg.src = previewUrl;
                                posModalImg.alt = previewAlt;
                                posModal.classList.remove('hidden');
                                posModal.classList.add('flex');
                                document.body.style.overflow = 'hidden';
                            }
                            return;
                        }
                        var moldSizeTableBtn = e.target.closest('.mold-model-size-table-btn');
                        if (moldSizeTableBtn) {
                            e.preventDefault();
                            e.stopPropagation();
                            var sizeTableUrl = moldSizeTableBtn.getAttribute('data-image-url');
                            var sizeTableTitle = moldSizeTableBtn.getAttribute('data-image-title') || moldSizeTableBtn.getAttribute('data-image-alt') || '';
                            var sizeTableAlt = moldSizeTableBtn.getAttribute('data-image-alt') || sizeTableTitle;
                            var sizeTableModal = document.getElementById('mold-model-size-table-modal');
                            var sizeTableModalTitle = document.getElementById('mold-model-size-table-modal-title');
                            var sizeTableModalImg = document.getElementById('mold-model-size-table-modal-image');
                            if (sizeTableModal && sizeTableModalTitle && sizeTableModalImg && sizeTableUrl) {
                                sizeTableModalTitle.textContent = sizeTableTitle;
                                sizeTableModalImg.src = sizeTableUrl;
                                sizeTableModalImg.alt = sizeTableAlt;
                                if (typeof window.resetMoldSizeTableZoom === 'function') {
                                    window.resetMoldSizeTableZoom();
                                }
                                sizeTableModal.classList.remove('hidden');
                                sizeTableModal.classList.add('flex');
                                document.body.style.overflow = 'hidden';
                            }
                            return;
                        }
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

                    function closeFabricDetailModal() {
                        var modal = document.getElementById('fabric-detail-modal');
                        if (modal) {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                            document.body.style.overflow = '';
                        }
                    }

                    function closeCustomizationPositionModal() {
                        var modal = document.getElementById('customization-position-modal');
                        if (modal) {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                            document.body.style.overflow = '';
                        }
                    }

                    function closeMoldModelSizeTableModal() {
                        var modal = document.getElementById('mold-model-size-table-modal');
                        if (modal) {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                            document.body.style.overflow = '';
                            if (typeof window.resetMoldSizeTableZoom === 'function') {
                                window.resetMoldSizeTableZoom();
                            }
                        }
                    }

                    (function initMoldSizeTableZoom() {
                        var viewport = document.getElementById('mold-model-size-table-zoom-viewport');
                        var img = document.getElementById('mold-model-size-table-modal-image');
                        var zoomIn = document.getElementById('mold-model-size-table-zoom-in');
                        var zoomOut = document.getElementById('mold-model-size-table-zoom-out');
                        var zoomReset = document.getElementById('mold-model-size-table-zoom-reset');
                        var zoomLabel = document.getElementById('mold-model-size-table-zoom-level');
                        if (!viewport || !img) {
                            return;
                        }

                        var state = { scale: 1, tx: 0, ty: 0, min: 1, max: 4, step: 0.25 };
                        var dragging = false;
                        var lastX = 0;
                        var lastY = 0;

                        function clampPan() {
                            if (state.scale <= 1) {
                                state.tx = 0;
                                state.ty = 0;

                                return;
                            }
                            var rect = viewport.getBoundingClientRect();
                            var maxX = (rect.width * (state.scale - 1)) / 2;
                            var maxY = (rect.height * (state.scale - 1)) / 2;
                            state.tx = Math.max(-maxX, Math.min(maxX, state.tx));
                            state.ty = Math.max(-maxY, Math.min(maxY, state.ty));
                        }

                        function applyTransform() {
                            clampPan();
                            img.style.transform = 'translate(' + state.tx + 'px, ' + state.ty + 'px) scale(' + state.scale + ')';
                            viewport.classList.toggle('is-zoomed', state.scale > 1);
                            if (zoomLabel) {
                                zoomLabel.textContent = Math.round(state.scale * 100) + '%';
                            }
                        }

                        window.resetMoldSizeTableZoom = function () {
                            state.scale = 1;
                            state.tx = 0;
                            state.ty = 0;
                            applyTransform();
                        };

                        function setScale(next) {
                            state.scale = Math.max(state.min, Math.min(state.max, next));
                            if (state.scale <= 1) {
                                state.tx = 0;
                                state.ty = 0;
                            }
                            applyTransform();
                        }

                        if (zoomIn) {
                            zoomIn.addEventListener('click', function (e) {
                                e.stopPropagation();
                                setScale(state.scale + state.step);
                            });
                        }
                        if (zoomOut) {
                            zoomOut.addEventListener('click', function (e) {
                                e.stopPropagation();
                                setScale(state.scale - state.step);
                            });
                        }
                        if (zoomReset) {
                            zoomReset.addEventListener('click', function (e) {
                                e.stopPropagation();
                                window.resetMoldSizeTableZoom();
                            });
                        }

                        viewport.addEventListener('wheel', function (e) {
                            e.preventDefault();
                            setScale(state.scale + (e.deltaY < 0 ? state.step : -state.step));
                        }, { passive: false });

                        viewport.addEventListener('dblclick', function (e) {
                            e.preventDefault();
                            if (state.scale > 1) {
                                window.resetMoldSizeTableZoom();
                            } else {
                                setScale(2);
                            }
                        });

                        viewport.addEventListener('pointerdown', function (e) {
                            if (state.scale <= 1 || e.button !== 0) {
                                return;
                            }
                            dragging = true;
                            lastX = e.clientX;
                            lastY = e.clientY;
                            viewport.setPointerCapture(e.pointerId);
                            viewport.classList.add('is-dragging');
                        });

                        viewport.addEventListener('pointermove', function (e) {
                            if (!dragging) {
                                return;
                            }
                            state.tx += e.clientX - lastX;
                            state.ty += e.clientY - lastY;
                            lastX = e.clientX;
                            lastY = e.clientY;
                            applyTransform();
                        });

                        function endDrag(e) {
                            if (!dragging) {
                                return;
                            }
                            dragging = false;
                            viewport.classList.remove('is-dragging');
                            if (e && e.pointerId !== undefined) {
                                try {
                                    viewport.releasePointerCapture(e.pointerId);
                                } catch (_) {}
                            }
                        }

                        viewport.addEventListener('pointerup', endDrag);
                        viewport.addEventListener('pointercancel', endDrag);

                        applyTransform();
                    })();

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
                    /** Erkek beden tablosu: Erkek, Unisex ve Erkek/Unisex aynı grubu paylaşır. */
                    function isErkekUnisexFamily(s) {
                        var n = normalizeForMatch(s)
                            .replace(/[\/|_-]+/g, ' ')
                            .replace(/\s+/g, ' ')
                            .trim();
                        if (!n) return false;
                        if (n === 'erkek' || n === 'unisex' || n === 'erkek unisex') return true;
                        if (n === 'men' || n === 'male' || n === 'men unisex') return true;
                        // "erkek unisex beden tablosu" gibi etiketler
                        if (n.indexOf('erkek') !== -1 && n.indexOf('unisex') !== -1) return true;
                        if (n === 'unisex' || (n.indexOf('unisex') !== -1 && n.indexOf('kadin') === -1 && n.indexOf('cocuk') === -1)) {
                            // pure unisex labels still count
                            if (n === 'unisex' || n.indexOf('unisex') === 0) return true;
                        }
                        return false;
                    }
                    function valueMatchesTrigger(selectedValue, triggerValue) {
                        if (!triggerValue) return false;
                        var tRaw = (triggerValue || '').trim();
                        // Admin: "Erkek|Unisex" veya "Erkek,Unisex"
                        if (/[|,]/.test(tRaw)) {
                            return tRaw.split(/[|,]/).some(function(part) {
                                return valueMatchesTrigger(selectedValue, (part || '').trim());
                            });
                        }
                        var v = (selectedValue || '').trim();
                        var t = tRaw;
                        if (!v || !t) return false;
                        if (v === t) return true;
                        if (v.toLowerCase() === t.toLowerCase()) return true;
                        if (normalizeForMatch(v) === normalizeForMatch(t)) return true;
                        if (isErkekUnisexFamily(v) && isErkekUnisexFamily(t)) return true;
                        var kadinAliases = ['kadın', 'kadin'];
                        if (kadinAliases.indexOf(v.toLowerCase()) !== -1 && kadinAliases.indexOf(t.toLowerCase()) !== -1) return true;
                        var cocukAliases = ['çocuk', 'cocuk', 'çoçuk', 'coçuk'];
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
                    function findVariationPanelByName(variationName) {
                        var target = (variationName || '').trim();
                        if (!target) return null;
                        var tLower = target.toLowerCase();
                        var panel = null;
                        document.querySelectorAll('.variation-step-panel[data-variation-name]').forEach(function(p) {
                            if (panel) return;
                            var n = (p.getAttribute('data-variation-name') || '').trim();
                            var nLower = n.toLowerCase();
                            if (nLower === tLower) { panel = p; return; }
                            if (nLower.indexOf(tLower) === 0) { panel = p; return; }
                            if (tLower.indexOf(nLower) === 0) { panel = p; return; }
                            // "Cinsiyet" ↔ "Cinsiyet Seçiniz"
                            var firstWord = nLower.split(/\s+/)[0] || '';
                            var tFirst = tLower.split(/\s+/)[0] || '';
                            if (firstWord && firstWord === tFirst) { panel = p; }
                        });
                        return panel;
                    }
                    /**
                     * Çoklu seçimde "Devam et" öncesi veya özet nesnesinde eksik anahtar olsa bile,
                     * varyasyon panelindeki gerçek seçimi döndürür (beden önkoşulu / tetikleyici için).
                     * Panel gizlenmiş olsa bile seçim korunur (sadece stil.display yüzünden boş dönme).
                     */
                    function getDomSelectionValuesForVariation(variationName) {
                        var panel = findVariationPanelByName(variationName);
                        if (!panel) return [];
                        var isMulti = (panel.getAttribute('data-allows-multiple') || '') === '1';
                        var confirmed = (panel.getAttribute('data-multi-confirmed') || '') === '1';
                        if (isMulti && !confirmed) return [];
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

                    /** Beden tablosu admin tetikleyicisi veya parent_option_ids ile eşleşme (null = parent_option_ids kullan). */
                    function sizeTableElementMatchesSelections(el) {
                        if (!el) return null;
                        var triggerVar = (el.getAttribute('data-trigger-variation') || '').trim();
                        if (triggerVar) {
                            var triggerVal = (el.getAttribute('data-trigger-value') || '').trim();
                            var selectedVals = getDomSelectionValuesForVariation(triggerVar);
                            if (!selectedVals.length) return false;
                            if (!triggerVal) return true;
                            return selectedVals.some(function(v) { return valueMatchesTrigger(v, triggerVal); });
                        }
                        return null;
                    }

                    function filterSizeTableVariationOptions(block) {
                        if (!block || (block.getAttribute('data-variation-type') || '') !== 'size_table') return;
                        var dependsOn = (block.getAttribute('data-depends-on') || '').trim();
                        var parentIds = dependsOn ? normalizeOptionIdList(getSelectedParentOptionIdsForVariation(dependsOn)) : [];
                        var options = block.querySelectorAll('.product-variation-options .product-option');
                        var anyTriggerAttr = false;
                        options.forEach(function(opt) {
                            if ((opt.getAttribute('data-trigger-variation') || '').trim()) anyTriggerAttr = true;
                        });
                        options.forEach(function(opt) {
                            var triggerMatch = sizeTableElementMatchesSelections(opt);
                            if (triggerMatch !== null) {
                                setProductOptionVisibility(opt, triggerMatch);
                                return;
                            }
                            if (parentIds.length) {
                                var optParents = parseParentOptionIdsFromElement(opt);
                                setProductOptionVisibility(opt, optParents.length === 0 || listsOverlap(optParents, parentIds));
                            } else {
                                setProductOptionVisibility(opt, true);
                            }
                        });
                        // Cinsiyet tetikleyicisi varken eşleşme yoksa tüm tabloları tekrar açma (yeniden "cinsiyet" seçimi gibi görünür).
                        if (options.length && parentIds.length && !anyTriggerAttr) {
                            var visible = [];
                            options.forEach(function(opt) {
                                if (opt.style.display !== 'none') visible.push(opt);
                            });
                            if (visible.length === 0) {
                                options.forEach(function(opt) {
                                    var optParents = parseParentOptionIdsFromElement(opt);
                                    if (optParents.length === 0) setProductOptionVisibility(opt, true);
                                });
                            }
                        }
                    }

                    function targetFromSizeTableWrap(wrap, source) {
                        return {
                            optionVal: (wrap.getAttribute('data-size-table-option') || '').trim(),
                            slug: (wrap.getAttribute('data-size-table-slug') || '').trim(),
                            source: source || 'wrap'
                        };
                    }

                    function targetFromSizeTableOption(opt, source) {
                        return {
                            optionVal: (opt.getAttribute('data-option') || '').trim(),
                            slug: (opt.getAttribute('data-size-table-slug') || '').trim(),
                            source: source || 'auto'
                        };
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
                    function resolveSizeTableTarget(block) {
                        if (!block) return null;

                        var selected = block.querySelector('.product-option.option-selected');
                        if (selected && selected.style.display === 'none') {
                            setProductOptionVisual(selected, false);
                            selected = null;
                        }
                        if (selected && selected.style.display !== 'none') {
                            return targetFromSizeTableOption(selected, 'selected');
                        }

                        var wraps = block.querySelectorAll('.size-table-variation-grids .size-table-wrap-in-variation');
                        var triggerMatches = [];
                        wraps.forEach(function(wrap) {
                            if (sizeTableElementMatchesSelections(wrap) === true) {
                                triggerMatches.push(wrap);
                            }
                        });
                        if (triggerMatches.length === 1) {
                            return targetFromSizeTableWrap(triggerMatches[0], 'trigger');
                        }
                        if (triggerMatches.length > 1) {
                            var withValue = triggerMatches.filter(function(w) {
                                return (w.getAttribute('data-trigger-value') || '').trim() !== '';
                            });
                            if (withValue.length === 1) {
                                return targetFromSizeTableWrap(withValue[0], 'trigger');
                            }
                            // Birden fazla eşleşme (örn. Erkek + Unisex ailesi) → ilk erkek/unisex tablosu
                            return targetFromSizeTableWrap(triggerMatches[0], 'trigger');
                        }

                        // Buton tarafında tetikleyici eşleşmesi (wrap attribute yoksa)
                        var optionTriggerMatches = [];
                        block.querySelectorAll('.product-variation-options .product-option').forEach(function(opt) {
                            if (opt.style.display === 'none') return;
                            if (sizeTableElementMatchesSelections(opt) === true) {
                                optionTriggerMatches.push(opt);
                            }
                        });
                        if (optionTriggerMatches.length === 1) {
                            return targetFromSizeTableOption(optionTriggerMatches[0], 'trigger');
                        }
                        if (optionTriggerMatches.length > 1) {
                            return targetFromSizeTableOption(optionTriggerMatches[0], 'trigger');
                        }

                        var visible = [];
                        block.querySelectorAll('.product-variation-options .product-option').forEach(function(opt) {
                            if (opt.style.display === 'none') return;
                            visible.push(opt);
                        });
                        if (visible.length === 1) {
                            return targetFromSizeTableOption(visible[0], 'auto');
                        }

                        if (wraps.length === 1) {
                            var onlyWrap = wraps[0];
                            var onlyMatch = sizeTableElementMatchesSelections(onlyWrap);
                            if (onlyMatch === false) return null;
                            if (onlyMatch === true || onlyMatch === null) {
                                return targetFromSizeTableWrap(onlyWrap, 'single-wrap');
                            }
                        }

                        var dependsOn = (block.getAttribute('data-depends-on') || '').trim();
                        var parentIds = dependsOn ? normalizeOptionIdList(getSelectedParentOptionIdsForVariation(dependsOn)) : [];
                        if (visible.length > 1 && parentIds.length) {
                            var linked = visible.filter(function(opt) {
                                var optParents = parseParentOptionIdsFromElement(opt);
                                return optParents.length > 0 && listsOverlap(optParents, parentIds);
                            });
                            if (linked.length >= 1) {
                                return targetFromSizeTableOption(linked[0], 'auto');
                            }
                        }

                        if (wraps.length > 1 && parentIds.length) {
                            var matchingWraps = [];
                            wraps.forEach(function(wrap) {
                                var wrapParents = parseParentOptionIdsFromElement(wrap);
                                if (wrapParents.length === 0 || listsOverlap(wrapParents, parentIds)) {
                                    matchingWraps.push(wrap);
                                }
                            });
                            var linkedWraps = matchingWraps.filter(function(wrap) {
                                return parseParentOptionIdsFromElement(wrap).length > 0;
                            });
                            var targetWrap = linkedWraps.length === 1 ? linkedWraps[0]
                                : (matchingWraps.length === 1 ? matchingWraps[0] : null);
                            if (targetWrap) {
                                return targetFromSizeTableWrap(targetWrap, 'wrap');
                            }
                        }

                        return null;
                    }

                    function syncSizeTableVariationBlock(block) {
                        if (!block || (block.getAttribute('data-variation-type') || '') !== 'size_table') return;
                        var grids = block.querySelector('.size-table-variation-grids');
                        var continueWrap = block.querySelector('.size-table-variation-continue-wrap');
                        var optionButtons = block.querySelectorAll('.product-variation-options .product-option');
                        var optionCount = optionButtons.length;
                        var selected = block.querySelector('.product-option.option-selected');
                        if (selected && selected.style.display === 'none') {
                            setProductOptionVisual(selected, false);
                            selected = null;
                        }

                        var target = null;
                        if (selected) {
                            target = targetFromSizeTableOption(selected, 'selected');
                        } else {
                            var resolved = resolveSizeTableTarget(block);
                            // Upstream trigger/parent match: show grid without auto-clicking option buttons.
                            if (resolved && resolved.source !== 'auto') {
                                target = resolved;
                            }
                        }

                        var optionVal = target ? target.optionVal : '';
                        var slug = target ? target.slug : '';

                        var visibleOptions = [];
                        optionButtons.forEach(function(opt) {
                            if (opt.style.display === 'none') return;
                            visibleOptions.push(opt);
                        });
                        var singleVisible = visibleOptions.length === 1;
                        var hasTarget = !!(target && (optionVal || slug));

                        if (optionCount > 0) {
                            optionButtons.forEach(function(b) {
                                setProductOptionVisual(b, !!selected && b === selected && b.style.display !== 'none');
                            });
                        }

                        if (grids) {
                            grids.classList.toggle('hidden', !hasTarget && (optionCount > 1 || visibleOptions.length > 1));
                        }
                        if (grids) {
                            grids.querySelectorAll('.size-table-wrap-in-variation').forEach(function(wrap) {
                                var wOpt = (wrap.getAttribute('data-size-table-option') || '').trim();
                                var wSlug = (wrap.getAttribute('data-size-table-slug') || '').trim();
                                var match = hasTarget && ((optionVal !== '' && wOpt === optionVal) || (slug !== '' && wSlug === slug));
                                wrap.classList.toggle('hidden', !match);
                            });
                        }

                        var picker = block.querySelector('.product-variation-options');
                        var pickerHint = picker ? picker.previousElementSibling : null;
                        if (picker) {
                            picker.classList.toggle('hidden', hasTarget && (singleVisible || visibleOptions.length <= 1));
                        }
                        if (pickerHint && pickerHint.tagName === 'P') {
                            pickerHint.classList.toggle('hidden', hasTarget && (singleVisible || visibleOptions.length <= 1));
                        }
                        if (continueWrap) {
                            continueWrap.classList.toggle('hidden', !hasTarget);
                        }
                        if (hasTarget && (block.getAttribute('data-size-table-confirmed') || '') !== '1') {
                            block.setAttribute('data-size-table-confirmed', '0');
                        }
                        if (hasTarget && selected) {
                            updatePanelSummary(block, optionVal || slug || '—');
                        }
                    }

                    function initSizeTableVariationBlocks() {
                        document.querySelectorAll('.product-variation-block[data-variation-type="size_table"]').forEach(function(block) {
                            filterSizeTableVariationOptions(block);
                            syncSizeTableVariationBlock(block);
                        });
                    }

                    function updateSizeTableVisibility() {
                        var simpleWrap = document.getElementById('quantity-simple-wrap');
                        var quantityInput = document.getElementById('quantity-input');
                        var hasVariations = document.querySelectorAll('.product-variation-block').length > 0;
                        var variationBlocksDone = allProductVariationBlocksComplete();
                        var hasSizeTableVariation = document.querySelectorAll('.product-variation-block[data-variation-type="size_table"]').length > 0;

                        document.querySelectorAll('.product-variation-block[data-variation-type="size_table"]').forEach(function(block) {
                            syncSizeTableVariationBlock(block);
                        });

                        var anyTableVisible = false;
                        if (variationBlocksDone && hasSizeTableVariation) {
                            document.querySelectorAll('.size-table-wrap-in-variation:not(.hidden)').forEach(function() {
                                anyTableVisible = true;
                            });
                        }
                        if (simpleWrap) {
                            simpleWrap.classList.toggle('hidden', anyTableVisible || (hasVariations && !variationBlocksDone));
                        }
                        if (quantityInput) quantityInput.setAttribute('name', anyTableVisible ? 'quantity_placeholder' : 'quantity');
                    }

                    applyDependencyChainNow();
                    initSizeTableVariationBlocks();
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

                    function confirmCustomizationStepAndAdvance() {
                        var custPanel = document.querySelector('[data-customization-panel="1"]');
                        if (!custPanel) return false;
                        var hasRow = document.querySelectorAll('#product-customization-table input.customization-row-check:checked').length > 0;
                        if (!isCustomizationSkipSelected() && !hasRow) return false;
                        if (!validateCustomizationDimensionsOrWarn()) return false;
                        custPanel.setAttribute('data-customization-confirmed', '1');
                        var sumVal = custPanel.querySelector('.variation-step-summary-value');
                        if (sumVal) setCustomizationStepSummaryValue(sumVal, customizationSummaryHtmlFromInputs());
                        markVariationStepPanelComplete(custPanel);
                        applyCustomizationChoiceToVariationInput();
                        applyDependencyChainNow();
                        updateSizeTableVisibility();
                        updateVariationSummaryAndButton();
                        maybeAdvanceVariationStepAfterSelection(custPanel);
                        requestAnimationFrame(function() {
                            if (typeof updateSizeTableVisibility === 'function') updateSizeTableVisibility();
                        });
                        return true;
                    }

                    var customizationContinueBtn = document.getElementById('customization-continue-btn');
                    if (customizationContinueBtn) {
                        customizationContinueBtn.addEventListener('click', function() {
                            confirmCustomizationStepAndAdvance();
                        });
                    }

                    (function bindCustomizationTableUi() {
                        var tbl = document.getElementById('product-customization-table');
                        if (!tbl) return;
                        function syncCustUi() {
                            syncAllCustomizationDimensionValidity();
                            updateCustomizationContinueEnabled();
                            if (typeof updateVariationSummaryAndButton === 'function') updateVariationSummaryAndButton();
                        }
                        tbl.addEventListener('change', function(e) {
                            if (e.target && e.target.classList && e.target.classList.contains('customization-row-check') && e.target.checked) {
                                setCustomizationSkipSelected(false);
                            }
                            if (e.target && e.target.classList && e.target.classList.contains('customization-print-tech')) {
                                var card = e.target.closest('.customization-row-card');
                                syncCustomizationColorFieldForCard(card);
                                syncCustomizationColorsHeader();
                            }
                            syncCustUi();
                        });
                        tbl.addEventListener('input', function(e) {
                            if (e.target && e.target.closest && e.target.closest('.customization-row-card')) syncCustUi();
                        });
                        var skipCb = document.getElementById('customization-skip-checkbox');
                        if (skipCb) {
                            skipCb.addEventListener('change', function() {
                                var custPanel = document.querySelector('[data-customization-panel="1"]');
                                if (skipCb.checked) {
                                    tbl.querySelectorAll('.customization-row-check').forEach(function(cb) { cb.checked = false; });
                                    var ta = document.getElementById('product-customization-notes-field');
                                    if (ta) ta.value = '';
                                } else {
                                    syncAllCustomizationColorFields();
                                    if (custPanel && (custPanel.getAttribute('data-customization-confirmed') || '') === '1') {
                                        custPanel.setAttribute('data-customization-confirmed', '0');
                                        applyCustomizationChoiceToVariationInput();
                                        applyDependencyChainNow();
                                        var custIdx = parseInt(custPanel.getAttribute('data-step-index'), 10);
                                        if (!isNaN(custIdx)) showVariationStep(custIdx, { forceEdit: true });
                                    }
                                }
                                syncCustomizationFieldsDisabledState();
                                syncCustUi();
                                if (skipCb.checked) {
                                    confirmCustomizationStepAndAdvance();
                                }
                            });
                        }
                        syncAllCustomizationColorFields();
                        syncCustomizationFieldsDisabledState();
                        syncCustUi();
                    })();

                    document.querySelectorAll('.size-table-variation-continue-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var block = btn.closest('.product-variation-block');
                            if (!block || !validateSizeStepOrWarn()) return;
                            var info = getSizeQuantities();
                            var selectedBtn = block.querySelector('.product-option.option-selected');
                            var label = selectedBtn ? optionDisplayLabel(selectedBtn) : '';
                            var summary = label;
                            if (info.total > 0) {
                                summary += (summary ? ' · ' : '') + info.total + ' ' + (PU.units_suffix || '');
                            }
                            block.setAttribute('data-size-table-confirmed', '1');
                            updatePanelSummary(block, summary || label || '—');
                            updateSizeTableVisibility();
                            finishVariationStepIfReady(block);
                            updateVariationSummaryAndButton();
                        });
                    });

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

                    var fabricDetailModal = document.getElementById('fabric-detail-modal');
                    var fabricDetailModalClose = document.getElementById('fabric-detail-modal-close');
                    var fabricDetailModalCloseIcon = document.getElementById('fabric-detail-modal-close-icon');
                    var fabricDetailModalBackdrop = document.getElementById('fabric-detail-modal-backdrop');
                    if (fabricDetailModalClose) fabricDetailModalClose.addEventListener('click', closeFabricDetailModal);
                    if (fabricDetailModalCloseIcon) fabricDetailModalCloseIcon.addEventListener('click', closeFabricDetailModal);
                    if (fabricDetailModalBackdrop) fabricDetailModalBackdrop.addEventListener('click', closeFabricDetailModal);

                    var customizationPositionModal = document.getElementById('customization-position-modal');
                    var customizationPositionModalClose = document.getElementById('customization-position-modal-close');
                    var customizationPositionModalCloseIcon = document.getElementById('customization-position-modal-close-icon');
                    var customizationPositionModalBackdrop = document.getElementById('customization-position-modal-backdrop');
                    if (customizationPositionModalClose) customizationPositionModalClose.addEventListener('click', closeCustomizationPositionModal);
                    if (customizationPositionModalCloseIcon) customizationPositionModalCloseIcon.addEventListener('click', closeCustomizationPositionModal);
                    if (customizationPositionModalBackdrop) customizationPositionModalBackdrop.addEventListener('click', closeCustomizationPositionModal);

                    var moldModelSizeTableModal = document.getElementById('mold-model-size-table-modal');
                    var moldModelSizeTableModalClose = document.getElementById('mold-model-size-table-modal-close');
                    var moldModelSizeTableModalCloseIcon = document.getElementById('mold-model-size-table-modal-close-icon');
                    var moldModelSizeTableModalBackdrop = document.getElementById('mold-model-size-table-modal-backdrop');
                    if (moldModelSizeTableModalClose) moldModelSizeTableModalClose.addEventListener('click', closeMoldModelSizeTableModal);
                    if (moldModelSizeTableModalCloseIcon) moldModelSizeTableModalCloseIcon.addEventListener('click', closeMoldModelSizeTableModal);
                    if (moldModelSizeTableModalBackdrop) moldModelSizeTableModalBackdrop.addEventListener('click', closeMoldModelSizeTableModal);

                    document.addEventListener('keydown', function(ev) {
                        if (ev.key === 'Escape' && warningDialog && !warningDialog.classList.contains('hidden')) closeProductWarningDialog();
                        if (ev.key === 'Escape' && fabricDetailModal && !fabricDetailModal.classList.contains('hidden')) closeFabricDetailModal();
                        if (ev.key === 'Escape' && customizationPositionModal && !customizationPositionModal.classList.contains('hidden')) closeCustomizationPositionModal();
                        if (ev.key === 'Escape' && moldModelSizeTableModal && !moldModelSizeTableModal.classList.contains('hidden')) closeMoldModelSizeTableModal();
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
                    var fallbackBaseTry = baseTry;
                    var rate = parseFloat(priceEl.getAttribute('data-exchange-rate')) || 1;
                    var symbol = priceEl.getAttribute('data-currency-symbol') || '₺';
                    var code = priceEl.getAttribute('data-currency-code') || 'TRY';
                    var priceTiers = [];
                    try {
                        var tiersRaw = priceEl.getAttribute('data-price-tiers') || '[]';
                        if (tiersRaw.indexOf('&quot;') !== -1 || tiersRaw.indexOf('&#') !== -1) {
                            var ta = document.createElement('textarea');
                            ta.innerHTML = tiersRaw;
                            tiersRaw = ta.value;
                        }
                        priceTiers = JSON.parse(tiersRaw);
                        if (!Array.isArray(priceTiers)) priceTiers = [];
                    } catch (e) {
                        priceTiers = [];
                    }

                    function resolveQuantityPriceMultiplier(qty) {
                        var q = Math.max(0, parseInt(qty, 10) || 0);
                        for (var i = 0; i < priceTiers.length; i++) {
                            var t = priceTiers[i] || {};
                            var min = parseInt(t.min, 10);
                            if (!isFinite(min)) min = 1;
                            var max = t.max === null || t.max === undefined || t.max === '' ? null : parseInt(t.max, 10);
                            if (q < min) continue;
                            if (max !== null && (!isFinite(max) || q > max)) continue;
                            var m = parseFloat(t.multiplier != null ? t.multiplier : t.unit_try);
                            if (isFinite(m) && m > 0) return m;
                        }
                        return 1;
                    }

                    function resolveUnitBaseTryForQty(qty) {
                        return fallbackBaseTry * resolveQuantityPriceMultiplier(qty);
                    }

                    function convertFromTry(tryAmount) {
                        var n = parseFloat(tryAmount);
                        if (!isFinite(n)) n = 0;
                        if (code === 'TRY') return n;
                        var r = parseFloat(rate);
                        if (!isFinite(r) || r <= 0) return n;
                        return n / r;
                    }

                    function formatPrice(num) {
                        if (code === 'TRY') return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num) + ' ' + symbol;
                        return symbol + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
                    }

                    function updateSimpleLineTotal() {
                        var qty = Math.max(0, parseInt(quantityInput.value, 10) || 0);
                        baseTry = resolveUnitBaseTryForQty(qty > 0 ? qty : 1);
                        var lineTry = baseTry * qty;
                        var lineConverted = convertFromTry(lineTry);
                        priceEl.textContent = formatPrice(lineConverted);
                        var basePriceEl = document.getElementById('product-base-price');
                        if (basePriceEl) {
                            var baseConverted = convertFromTry(baseTry);
                            basePriceEl.textContent = formatPrice(baseConverted);
                        }
                        var strikeEl = document.getElementById('product-price-strike');
                        if (strikeEl) {
                            var normalAttr = priceEl.getAttribute('data-normal-try');
                            var normalTry = normalAttr !== null && normalAttr !== '' ? parseFloat(normalAttr) : NaN;
                            if (qty > 0 && !isNaN(normalTry) && normalTry > baseTry) {
                                var oldLineTry = normalTry * qty;
                                var oldConverted = convertFromTry(oldLineTry);
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
