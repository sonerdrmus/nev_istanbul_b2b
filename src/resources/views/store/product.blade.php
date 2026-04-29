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
        <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">Ana Sayfa</a>
        <span>/</span>
        @if($product->category)
            @if($product->category->parent)
                <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">Ürünler</a>
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
    <div class="grid grid-cols-1 lg:grid-cols-[auto_minmax(0,1fr)] gap-6 lg:gap-6 xl:gap-8 items-start">
        {{-- Sol: ürün görseli (büyük ekranda scroll ile sabit / sticky) --}}
        @php $displayImages = $product->display_image_urls; @endphp
        <div class="w-full max-w-[30.8rem] mx-auto lg:mx-0 lg:max-w-none lg:w-[19.8rem] xl:w-[22rem] 2xl:w-[34.2rem] shrink-0 lg:sticky lg:top-24 lg:z-[1] lg:self-start">
        <div class="rounded-2xl overflow-hidden bg-slate-100 aspect-square max-h-[400px] lg:max-h-[min(88vh,572px)] lg:aspect-square relative shadow-2xl shadow-slate-300/30 ring-1 ring-slate-200/70 w-full">
            @if(count($displayImages) > 0)
                <div id="product-gallery" class="relative w-full h-full flex items-center justify-center cursor-zoom-in" role="region" aria-label="Ürün görselleri" title="Büyütmek için tıklayın">
                    @foreach($displayImages as $idx => $url)
                        <div class="product-gallery-slide absolute inset-0 flex items-center justify-center transition-opacity duration-300 {{ $idx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}" data-slide-index="{{ $idx }}" data-image-url="{{ $url }}" aria-hidden="{{ $idx !== 0 }}">
                            <img src="{{ $url }}" alt="{{ $product->name }} — {{ $idx + 1 }}. görsel" class="max-w-full max-h-full w-auto h-auto object-contain">
                        </div>
                    @endforeach
                    @if(count($displayImages) > 1)
                        <div class="absolute bottom-3 left-0 right-0 z-20 flex justify-center gap-1.5">
                            @foreach($displayImages as $idx => $url)
                                <button type="button" class="product-gallery-dot w-2.5 h-2.5 rounded-full transition-colors {{ $idx === 0 ? 'bg-white ring-2 ring-primary-500' : 'bg-white/60 hover:bg-white/80' }}" data-slide-index="{{ $idx }}" aria-label="{{ $idx + 1 }}. görsele git"></button>
                            @endforeach
                        </div>
                        <button type="button" class="product-gallery-prev absolute left-2 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-md flex items-center justify-center text-slate-700 transition-opacity" aria-label="Önceki görsel">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" class="product-gallery-next absolute right-2 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-md flex items-center justify-center text-slate-700 transition-opacity" aria-label="Sonraki görsel">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endif
                </div>
                {{-- Lightbox: tıklanınca büyütme --}}
                <div id="product-image-lightbox" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/90 p-4" aria-modal="true" role="dialog" aria-label="Ürün görseli büyütülmüş">
                    <button type="button" id="lightbox-close" class="absolute top-4 right-4 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors" aria-label="Kapat">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    @if(count($displayImages) > 1)
                        <button type="button" id="lightbox-prev" class="absolute left-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors" aria-label="Önceki görsel">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" id="lightbox-next" class="absolute right-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors" aria-label="Sonraki görsel">
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

        @if($canSeePrices && $hasVariations && $product->isOnSale())
            {{-- Seçilen seçenekler özeti: ürün görselinin altında --}}
            <div id="variation-summary-wrap" class="mt-4 lg:mt-5 p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white shadow-sm">
                <p class="text-sm font-semibold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </span>
                    Seçilen seçenekler
                </p>
                <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50/50">
                    <table class="w-full text-sm border-collapse" aria-label="Seçilen varyasyonlar ve adet">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-200">
                                <th scope="col" class="text-left font-semibold text-slate-700 py-2.5 px-3">Seçenek</th>
                                <th scope="col" class="text-left font-semibold text-slate-700 py-2.5 px-3">Değer</th>
                            </tr>
                        </thead>
                        <tbody id="variation-summary-tbody">
                            <tr>
                                <td colspan="2" class="text-slate-400 py-4 px-3 text-center">Tüm seçenekleri yukarıdan belirleyin.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p id="variation-summary-warning" class="mt-3 text-sm text-amber-700 font-medium hidden" role="alert">
                    Sepete eklemek için tüm seçenekleri belirleyin.
                </p>
                <div id="variation-confirm-wrap" class="mt-4 hidden">
                    <label class="flex items-start gap-3 cursor-pointer group">
                        <input type="checkbox" id="variation-confirm-checkbox" name="variation_confirmed" value="1" form="add-to-cart-form" class="mt-1 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Seçimlerimi onaylıyorum, sepete ekleyebilirim.</span>
                    </label>
                </div>
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50/80 px-3.5 py-3">
                    <div class="grid grid-cols-[auto_1fr] items-center gap-x-3 gap-y-1">
                        <span class="text-sm font-semibold text-slate-600">Ürün Fiyatı:</span>
                        <div class="text-right">
                            <span id="product-base-price" class="text-sm font-semibold text-slate-700">
                                {{ $selectedCurrency->format($baseConverted) }}
                            </span>
                        </div>
                        <span class="text-sm font-semibold text-slate-600">Toplam Sipariş Tutarı:</span>
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
                <button type="submit" id="add-to-cart-btn" form="add-to-cart-form" class="mt-4 w-full py-3 sm:py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm sm:text-base font-semibold flex items-center justify-center gap-2 shadow-md shadow-primary-600/20 disabled:opacity-60 disabled:cursor-not-allowed" disabled>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span id="add-to-cart-btn-label">Seçenekleri belirleyin, ardından onaylayın.</span>
                </button>
            </div>
        @endif
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
                            Stokta yok
                        </span>
                    @elseif($productStatus === 'yakinda_gelecek')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Yakında gelecek
                        </span>
                    @endif
                </div>
            @endif

            @if(!$canSeePrices)
                <p class="mt-4 text-slate-500 text-sm">Fiyatları görmek ve sipariş vermek için giriş yapmanız gerekmektedir.</p>
            @endif

            @if($product->description)
                <div class="mt-6 prose prose-slate max-w-none text-slate-600 text-sm sm:text-base prose-img:rounded-lg">
                    {!! $product->description !!}
                </div>
            @endif

            @if($canSeePrices)
                <div class="mt-6 flex items-center gap-2 text-sm">
                    <span class="font-medium text-slate-700">Stok:</span>
                    @if($product->stock_quantity === null)
                        <span class="text-slate-600">Stok takibi yapılmıyor</span>
                    @elseif((int) $product->stock_quantity > 0)
                        <span class="text-slate-600">{{ number_format($product->stock_quantity, 0, ',', '.') }} adet</span>
                    @else
                        <span class="text-red-600 font-medium">Stokta yok</span>
                    @endif
                </div>
            @endif

    @if($canSeePrices && $product->isOnSale() && ($product->stock_quantity === null || (int) $product->stock_quantity > 0))
    @php
        $minOrder = $product->getMinimumOrderQuantity();
        $availableStock = $product->stock_quantity !== null ? (int) $product->stock_quantity : 999999;
    @endphp
    <section class="mt-5 lg:mt-6 w-full" aria-label="Sipariş ve seçenekler">
        <div class="max-w-full">
            <form action="{{ route('store.cart.add') }}" method="POST" class="rounded-2xl lg:rounded-2xl border border-slate-200/90 bg-slate-50/90 p-4 sm:p-5 lg:p-6 shadow-sm shadow-slate-200/30 ring-1 ring-slate-200/40" id="add-to-cart-form" data-available-stock="{{ $availableStock }}" data-size-table-trigger-variation="{{ e($product->size_table_trigger_variation ?? '') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                @if($hasVariations)
                    <input type="hidden" name="variation_data" id="variation-data-input" value="">
                @endif
                <input type="hidden" name="size_quantities" id="size-quantities-input" value="">

                {{-- Normal adet alanı (varyasyon yoksa hemen göster, varyasyon varsa tüm seçenekler seçilince göster) --}}
                <div id="quantity-simple-wrap" class="{{ $hasVariations ? 'hidden' : '' }}">
                    <label class="block font-semibold text-slate-700 mb-2">Adet</label>
                    <input type="number" name="quantity" id="quantity-input" value="{{ $minOrder }}" min="{{ $minOrder }}" max="{{ $availableStock }}" class="w-full max-w-xs sm:max-w-sm lg:max-w-md rounded-xl border border-slate-300 px-4 py-3.5 lg:px-5 lg:py-4 text-base text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    @if($minOrder > 1)
                        <p class="mt-1 text-sm text-slate-500">Minimum sipariş: {{ $minOrder }} adet</p>
                    @endif
                </div>

                @if($hasVariations)
                    {{-- Varyasyon adımları: üstte, "Seçenekleri belirleyin" + timeline; son step = Beden Tablosu --}}
                    @php
                        $variationSteps = [];
                        $addedNames = [];
                        $variationsCollection = $product->variations;
                        while (count($variationSteps) < $variationsCollection->count()) {
                            $found = false;
                            foreach ($variationsCollection as $v) {
                                if (in_array($v->name, $addedNames)) continue;
                                $dep = $v->depends_on ?? '';
                                if ($dep === '' || in_array($dep, $addedNames)) {
                                    $variationSteps[] = $v;
                                    $addedNames[] = $v->name;
                                    $found = true;
                                }
                            }
                            if (! $found) break;
                        }
                        $totalSteps = count($variationSteps);
                        $sizeTables = $sizeTables ?? collect();
                    @endphp
                    <section class="mt-3 lg:mt-4 w-full" aria-labelledby="variations-heading">
                        <div class="rounded-2xl border border-slate-200/70 bg-gradient-to-b from-white to-slate-50/60 shadow-sm overflow-hidden ring-1 ring-slate-200/30">
                            <div class="px-4 sm:px-5 lg:px-6 py-3.5 lg:py-4 border-b border-slate-200/70 bg-white/95">
                                <h2 id="variations-heading" class="text-lg sm:text-xl lg:text-2xl font-semibold text-slate-800 tracking-tight flex items-center gap-2.5">
                                    <span class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    </span>
                                    Seçenekleri belirleyin
                                </h2>
                                <p class="mt-1 text-sm sm:text-base text-slate-500 leading-snug">Adım adım seçenekleri belirleyin.</p>
                            </div>
                            <div id="product-variations" class="variation-steps-container px-3.5 sm:px-5 lg:px-6 py-4 lg:py-5">
                                @foreach($variationSteps as $stepIndex => $variation)
                                    @php $isDependent = !empty($variation->depends_on); @endphp
                                    <div class="product-variation-block variation-step-panel flex flex-row gap-0 {{ $loop->first ? '' : 'mt-3 lg:mt-4' }} {{ $isDependent ? 'dependent-variation-block' : '' }}"
                                         data-variation-name="{{ $variation->name }}"
                                         data-depends-on="{{ $variation->depends_on ?? '' }}"
                                         data-step-index="{{ $stepIndex }}"
                                         @if($isDependent) style="display: none;" @endif>
                                        <div class="variation-timeline-cell flex flex-col items-center w-10 sm:w-11 shrink-0 pt-3 sm:pt-3.5">
                                            <span class="variation-step-num flex h-7 w-7 sm:h-8 sm:w-8 shrink-0 items-center justify-center rounded-full text-xs sm:text-sm font-bold ring-2 ring-white sm:ring-4 bg-slate-200 text-slate-600 shadow-sm z-10 transition-colors duration-300">{{ $stepIndex + 1 }}</span>
                                            <div class="w-0.5 flex-1 min-h-[6px] -mt-0.5 -mb-4 pb-4 bg-slate-200 rounded-full self-center" aria-hidden="true"></div>
                                        </div>
                                        <div class="variation-step-card flex-1 min-w-0 rounded-xl border border-slate-200/90 bg-white overflow-hidden transition-all duration-300 -ml-px shadow-sm">
                                            <button type="button" class="variation-step-dot w-full flex flex-row items-center gap-2.5 text-left py-3 sm:py-3.5 px-4 sm:px-5 bg-slate-50/90 hover:bg-slate-100/80 border-b border-slate-100/90 transition-colors {{ $stepIndex === 0 ? 'bg-primary-50/90 border-primary-100/80' : '' }} focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-inset" data-step="{{ $stepIndex }}" aria-label="{{ $variation->name }} seçin">
                                                <span class="variation-step-name text-sm sm:text-base font-semibold text-slate-800">{{ $variation->name }}</span>
                                                <span class="variation-step-check hidden shrink-0 text-emerald-600 ml-auto" aria-hidden="true"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>
                                            </button>
                                            <div class="variation-step-summary hidden flex items-center justify-between gap-2 px-4 sm:px-5 py-2.5 sm:py-3 bg-slate-50/70 border-b border-slate-100/90">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="text-slate-500 text-sm">{{ $variation->name }}:</span>
                                                    <span class="variation-step-summary-value font-medium text-slate-800">—</span>
                                                </div>
                                                <button type="button" class="variation-step-change-btn text-sm font-medium text-primary-600 hover:text-primary-700">Değiştir</button>
                                            </div>
                                            <div class="variation-step-full p-3.5 sm:p-4 lg:px-5 lg:py-4 {{ $stepIndex > 0 ? 'hidden' : '' }}">
                                                <div class="flex flex-wrap gap-2.5 sm:gap-3 lg:gap-3.5 product-variation-options">
                                    @foreach($variation->options as $option)
                                        @php $optionClasses = 'product-option border-2 border-slate-300 hover:border-primary-500 hover:shadow-md hover:shadow-primary-500/10 focus:outline-none focus:ring-2 focus:ring-primary-500/30 transition-all rounded-xl'; @endphp
                                        @php $parentIdsList = $option->getParentOptionIdsList(); @endphp
                                        @if($variation->type === 'color' && $option->option_color)
                                            <button type="button" class="{{ $optionClasses }} flex flex-col items-center rounded-xl p-1.5 shrink-0 min-w-[72px]" data-variation="{{ $variation->name }}" data-option="{{ $option->option_value }}" data-option-id="{{ $option->id }}" data-parent-option-id="{{ $option->parent_option_id ?? '' }}" data-parent-option-ids="{{ json_encode($parentIdsList) }}" data-price-delta="{{ (float) $option->price_delta }}" title="{{ $option->option_value }}">
                                                <span class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg shrink-0 border border-slate-200" style="background-color: {{ $option->option_color }}"></span>
                                                <span class="text-xs font-medium text-slate-600 mt-1.5 w-full max-w-[88px] sm:max-w-[100px] text-center break-words leading-tight">{{ $option->option_value }}</span>
                                            </button>
                                        @elseif($variation->type === 'image' && $option->option_image)
                                            @php $optionImageUrl = Storage::url($option->option_image); $imgSize = $option->option_image_size ?? 'medium'; $imgSizeClass = match($imgSize) { 'small' => 'w-14 h-14 sm:w-16 sm:h-16', 'large' => 'w-28 h-28 sm:w-32 sm:h-32', default => 'w-20 h-20 sm:w-24 sm:h-24' }; $minWClass = match($imgSize) { 'small' => 'min-w-[72px] sm:min-w-[88px]', 'large' => 'min-w-[120px] sm:min-w-[140px]', default => 'min-w-[88px] sm:min-w-[110px]' }; $labelMaxW = match($imgSize) { 'small' => 'max-w-[80px] sm:max-w-[88px]', 'large' => 'max-w-[120px] sm:max-w-[140px]', default => 'max-w-[96px] sm:max-w-[110px]' }; @endphp
                                            <button type="button" class="{{ $optionClasses }} flex flex-col items-center rounded-xl p-2 sm:p-3 {{ $minWClass }} relative" data-variation="{{ $variation->name }}" data-option="{{ $option->option_value }}" data-option-id="{{ $option->id }}" data-parent-option-id="{{ $option->parent_option_id ?? '' }}" data-parent-option-ids="{{ json_encode($parentIdsList) }}" data-price-delta="{{ (float) $option->price_delta }}">
                                                <span class="relative inline-block group">
                                                    <img src="{{ $optionImageUrl }}" alt="{{ $option->option_value }}" class="{{ $imgSizeClass }} object-cover rounded-xl">
                                                    <span class="variation-zoom-btn absolute top-1 right-1 w-6 h-6 rounded-md bg-black/50 hover:bg-primary-600 flex items-center justify-center text-white cursor-pointer transition-all opacity-0 group-hover:opacity-100" data-image-url="{{ $optionImageUrl }}" data-image-alt="{{ $option->option_value }}" title="Büyüt" role="button" aria-label="{{ $option->option_value }} görselini büyüt">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                                    </span>
                                                </span>
                                                <span class="text-sm font-medium text-slate-600 mt-2 w-full {{ $labelMaxW }} text-center break-words leading-tight">{{ $option->option_value }}</span>
                                            </button>
                                        @else
                                            <button type="button" class="{{ $optionClasses }} px-4 py-2.5 sm:px-5 sm:py-3 text-sm sm:text-base font-medium text-slate-700 min-h-[2.75rem]" data-variation="{{ $variation->name }}" data-option="{{ $option->option_value }}" data-option-id="{{ $option->id }}" data-parent-option-id="{{ $option->parent_option_id ?? '' }}" data-parent-option-ids="{{ json_encode($parentIdsList) }}" data-price-delta="{{ (float) $option->price_delta }}">{{ $option->option_value }}</button>
                                        @endif
                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                {{-- Son step: Beden Tablosu (sadece burada) --}}
                                <div class="variation-step-panel flex flex-row gap-0 mt-3 lg:mt-4" data-step-index="{{ $totalSteps }}">
                                    <div class="variation-timeline-cell flex flex-col items-center w-10 sm:w-11 shrink-0 pt-3 sm:pt-3.5">
                                        <span class="variation-step-num flex h-7 w-7 sm:h-8 sm:w-8 shrink-0 items-center justify-center rounded-full text-xs sm:text-sm font-bold ring-2 ring-white sm:ring-4 bg-slate-200 text-slate-600 shadow-sm z-10">{{ $totalSteps + 1 }}</span>
                                    </div>
                                    <div class="variation-step-card flex-1 min-w-0 rounded-xl border border-slate-200/90 bg-white overflow-hidden -ml-px shadow-sm">
                                        <button type="button" class="variation-step-dot w-full flex flex-row items-center gap-2.5 text-left py-3 sm:py-3.5 px-4 sm:px-5 bg-slate-50/90 hover:bg-slate-100/80 border-b border-slate-100/90 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-inset" data-step="{{ $totalSteps }}" aria-label="Beden tablosu">
                                            <span class="variation-step-name text-sm sm:text-base font-semibold text-slate-800">Beden tablosu</span>
                                            <span class="variation-step-check hidden shrink-0 text-emerald-600 ml-auto" aria-hidden="true"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>
                                        </button>
                                        {{-- İçerik sadece tüm varyasyonlar seçildikten sonra görünür; hangi tablo gösterileceği JS ile güncellenir --}}
                                        <div class="variation-step-full size-table-step-content p-3.5 sm:p-4 lg:px-5 lg:py-4 hidden">
                                            @foreach($sizeTables as $sizeTable)
                                            <div id="{{ $sizeTable->slug }}-size-table-wrap" class="hidden mt-4 first:mt-0 size-table-wrap" data-slug="{{ $sizeTable->slug }}" data-trigger-variation="{{ e($sizeTable->trigger_variation_name ?? '') }}" data-trigger-value="{{ e($sizeTable->trigger_option_value ?? '') }}">
                                                <p class="text-base sm:text-lg font-semibold text-slate-700 mb-3 sm:mb-4 flex items-center gap-2">
                                                    <span class="h-px flex-1 max-w-[40px] rounded-full bg-primary-200"></span>
                                                    {{ $sizeTable->title ?: 'BEDEN SEÇİNİZ' }}
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
                                                                <td class="font-medium text-slate-700 py-3 px-3">Sipariş adeti</td>
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
                                                        <span class="text-slate-500 font-medium">Min. sipariş</span>
                                                        <span class="font-bold text-slate-800">{{ number_format($minOrder) }} adet</span>
                                                    </span>
                                                    <span class="text-slate-300">·</span>
                                                    <span class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-1.5 shadow-sm ring-1 ring-slate-200/80">
                                                        <span class="text-slate-500 font-medium">Maks. sipariş</span>
                                                        <span class="font-bold text-slate-800">{{ $availableStock >= 999999 ? 'Sınırsız' : number_format($availableStock) }} adet</span>
                                                    </span>
                                                    <span class="text-slate-300">·</span>
                                                    <span class="inline-flex items-center gap-2 rounded-lg bg-primary-50 px-3 py-1.5 shadow-sm ring-1 ring-primary-200/80">
                                                        <span class="text-slate-600 font-medium">Girilen toplam</span>
                                                        <span id="{{ $sizeTable->slug }}-size-total" class="font-bold text-primary-700">0</span>
                                                        <span class="text-slate-600">adet</span>
                                                    </span>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <div id="variation-image-lightbox" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/90 p-4" aria-modal="true" role="dialog" aria-label="Seçenek görseli büyütülmüş">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" id="variation-lightbox-backdrop"></div>
                        <button type="button" id="variation-lightbox-close" class="absolute top-4 right-4 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors" aria-label="Kapat">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <img id="variation-lightbox-image" src="" alt="" class="relative z-10 max-w-full max-h-[90vh] w-auto h-auto object-contain rounded-lg shadow-2xl">
                    </div>

                @endif

                @if(!$hasVariations)
                    @if($canSeePrices && $baseTry !== null)
                        <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50/80 px-3.5 py-3">
                            <div class="grid grid-cols-[auto_1fr] items-center gap-x-3 gap-y-1">
                                <span class="text-sm font-semibold text-slate-600">Ürün Fiyatı:</span>
                                <div class="text-right">
                                    <span class="text-sm font-semibold text-slate-700">
                                        {{ $selectedCurrency->format($baseConverted) }}
                                    </span>
                                </div>
                                <span class="text-sm font-semibold text-slate-600">Toplam Ürün Fiyatı:</span>
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
                    <button type="submit" id="add-to-cart-btn" class="mt-4 lg:mt-5 w-full py-3 sm:py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm sm:text-base font-semibold flex items-center justify-center gap-2 shadow-md shadow-primary-600/20 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Sepete Ekle
                    </button>
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
                        <h2 id="product-warning-dialog-title" class="text-xl font-semibold text-slate-900 text-center mb-2">Minimum Sipariş Uyarısı</h2>
                        <p id="product-warning-dialog-desc" class="text-slate-600 text-center text-sm sm:text-base leading-relaxed">Minimum sipariş adeti karşılanmadı.</p>
                    </div>
                    <div class="px-6 sm:px-8 pb-6 sm:pb-8">
                        <button type="button" id="product-warning-dialog-close" class="w-full py-3.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            Tamam
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @elseif($canSeePrices && !$product->isOnSale())
    <section class="mt-5 lg:mt-6 w-full" aria-label="Satış durumu">
        <div class="w-full">
            <div class="p-6 rounded-2xl bg-slate-100 border border-slate-200">
                <p class="font-medium text-slate-700">{{ $product->getStatusLabel() }}</p>
                <p class="mt-1 text-sm text-slate-600">
                    @if($productStatus === 'stokta_yok')
                        Bu ürün şu an stokta bulunmuyor. Stok geldiğinde satışa sunulacaktır.
                    @else
                        Bu ürün yakında satışa sunulacaktır.
                    @endif
                </p>
            </div>
        </div>
    </section>
    @elseif($canSeePrices && $product->stock_quantity !== null && (int) $product->stock_quantity === 0)
    <section class="mt-5 lg:mt-6 w-full" aria-label="Stok bilgisi">
        <div class="w-full">
            <div class="p-6 rounded-2xl bg-slate-100 border border-slate-200">
                <p class="text-red-600 font-medium">Stokta yok</p>
                <p class="mt-1 text-sm text-slate-600">Bu ürün şu an satışta değildir. Stok geldiğinde sepete ekleyebilirsiniz.</p>
            </div>
        </div>
    </section>
    @endif

        </div>
    </div>

    @if($canSeePrices && $hasVariations)
        @push('head')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var priceEl = document.getElementById('product-price');
                    var variationInput = document.getElementById('variation-data-input');
                    if (!priceEl) return;

                    var baseTry = parseFloat(priceEl.getAttribute('data-base-try')) || 0;
                    var rate = parseFloat(priceEl.getAttribute('data-exchange-rate')) || 1;
                    var symbol = priceEl.getAttribute('data-currency-symbol') || '₺';
                    var code = priceEl.getAttribute('data-currency-code') || 'TRY';

                    function formatPrice(num) {
                        if (code === 'TRY') return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num) + ' ' + symbol;
                        return symbol + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
                    }

                    function getSelectedOptions() {
                        var list = getSelectedOptionsInStepOrder();
                        var selected = {};
                        list.forEach(function(item) { selected[item.name] = item.value; });
                        return selected;
                    }

                    /** Seçilen varyasyonları adım sırasına göre döndürür; özet satırından da okur (gizli paneller dahil). */
                    function getSelectedOptionsInStepOrder() {
                        var list = [];
                        var panels = Array.from(document.querySelectorAll('.variation-step-panel')).sort(function(a, b) {
                            return (parseInt(a.getAttribute('data-step-index'), 10) || 0) - (parseInt(b.getAttribute('data-step-index'), 10) || 0);
                        });
                        panels.forEach(function(panel) {
                            if (panel.style.display === 'none') return;
                            var name = (panel.getAttribute('data-variation-name') || '').trim();
                            if (!name) return;
                            var value = '';
                            var delta = 0;
                            var sel = panel.querySelector('.product-option.option-selected');
                            if (sel && sel.style.display !== 'none') {
                                delta = parseFloat(sel.getAttribute('data-price-delta')) || 0;
                            }
                            var summary = panel.querySelector('.variation-step-summary');
                            var summaryVal = panel.querySelector('.variation-step-summary-value');
                            if (summary && summaryVal && !summary.classList.contains('hidden')) {
                                value = (summaryVal.textContent || '').trim();
                            } else {
                                if (sel && sel.style.display !== 'none') value = (sel.getAttribute('data-option') || '').trim();
                            }
                            if (value && value !== '—') list.push({ name: name, value: value, priceDelta: delta });
                        });
                        return list;
                    }

                    function getDeltaTotal() {
                        var total = 0;
                        document.querySelectorAll('.variation-step-panel').forEach(function(panel) {
                            if (panel.style.display === 'none') return;
                            var sel = panel.querySelector('.product-option.option-selected');
                            if (sel) total += parseFloat(sel.getAttribute('data-price-delta')) || 0;
                        });
                        return total;
                    }

                    function updatePriceAndInput() {
                        if (!priceEl) return;
                        var delta = getDeltaTotal();
                        var unitTry = baseTry + delta;
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
                            if (qty > 0 && !isNaN(normalTry) && normalTry > unitTry) {
                                var oldLineTry = (normalTry + delta) * pricingWeight;
                                var oldConverted = code === 'TRY' ? oldLineTry : oldLineTry * rate;
                                strikeEl.textContent = formatPrice(oldConverted);
                                strikeEl.classList.remove('hidden');
                            } else {
                                strikeEl.textContent = '';
                                strikeEl.classList.add('hidden');
                            }
                        }
                        if (variationInput) {
                            variationInput.value = JSON.stringify(getSelectedOptions());
                        }
                    }

                    function getSelectedOptionIdInVariation(variationName) {
                        var block = document.querySelector('.product-variation-block[data-variation-name="' + variationName + '"]');
                        if (!block) return null;
                        var selected = block.querySelector('.product-option.option-selected');
                        if (selected && selected.style.display !== 'none') return selected.getAttribute('data-option-id');
                        var firstVisible = block.querySelector('.product-option:not([style*="display: none"])');
                        return firstVisible ? firstVisible.getAttribute('data-option-id') : null;
                    }

                    function filterDependentVariation(block, parentOptionId) {
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
                            if (parentOptionId === null || parentOptionId === undefined || parentOptionId === '') {
                                show = (parentIds.length === 0 && !parentIdSingle);
                            } else {
                                var matchMulti = parentIds.length > 0 && parentIds.indexOf(Number(parentOptionId)) !== -1;
                                var matchSingle = parentIdSingle && parentIdSingle === String(parentOptionId);
                                show = matchMulti || matchSingle || (parentIds.length === 0 && !parentIdSingle);
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

                    function allVisibleVariationsSelected() {
                        var blocks = document.querySelectorAll('.product-variation-block');
                        for (var i = 0; i < blocks.length; i++) {
                            var block = blocks[i];
                            if (block.style.display === 'none') continue;
                            var selected = block.querySelector('.product-option.option-selected');
                            if (!selected || selected.style.display === 'none') return false;
                        }
                        return true;
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
                        if (!tbody) return;
                        var rows = [];
                        ordered.forEach(function(item) {
                            rows.push('<tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50"><td class="py-2.5 px-3 font-medium text-slate-700">' + escapeHtml(item.name) + '</td><td class="py-2.5 px-3 text-slate-800">' + escapeHtml(item.value) + '</td></tr>');
                        });
                        if (ordered.length > 0) {
                            if (sizeInfo.sizeQuantities && Object.keys(sizeInfo.sizeQuantities).length) {
                                Object.keys(sizeInfo.sizeQuantities).forEach(function(size) {
                                    var qty = sizeInfo.sizeQuantities[size];
                                    if (qty > 0) {
                                        rows.push('<tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50"><td class="py-2.5 px-3 font-medium text-slate-700">Beden ' + escapeHtml(size) + '</td><td class="py-2.5 px-3 text-slate-800">' + qty + ' adet</td></tr>');
                                    }
                                });
                                rows.push('<tr class="border-b border-slate-100 last:border-0 bg-slate-100/70"><td class="py-2.5 px-3 font-semibold text-slate-800">Toplam adet</td><td class="py-2.5 px-3 font-semibold text-slate-800">' + sizeInfo.total + ' adet</td></tr>');
                            } else {
                                rows.push('<tr class="border-b border-slate-100 last:border-0 bg-slate-100/70"><td class="py-2.5 px-3 font-semibold text-slate-800">Adet</td><td class="py-2.5 px-3 font-semibold text-slate-800">' + sizeInfo.total + ' adet</td></tr>');
                            }
                            tbody.innerHTML = rows.join('');
                        } else {
                            tbody.innerHTML = '<tr><td colspan="2" class="text-slate-400 py-4 px-3 text-center">Tüm seçenekleri yukarıdan belirleyin.</td></tr>';
                        }
                        var confirmWrap = document.getElementById('variation-confirm-wrap');
                        if (confirmWrap) {
                            confirmWrap.classList.toggle('hidden', ordered.length === 0);
                        }
                        if (confirmCheckbox && ordered.length === 0) {
                            confirmCheckbox.checked = false;
                        }
                        var allSelected = allVisibleVariationsSelected();
                        var confirmed = !confirmCheckbox || confirmCheckbox.checked;
                        var canSubmit = allSelected && confirmed;
                        var btnLabel = document.getElementById('add-to-cart-btn-label');
                        if (warningEl) {
                            warningEl.classList.toggle('hidden', allSelected);
                        }
                        if (btn) {
                            btn.disabled = !canSubmit;
                            if (btnLabel) {
                                btnLabel.textContent = canSubmit
                                    ? 'Sepete Ekle'
                                    : 'Seçenekleri belirleyin, ardından onaylayın.';
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
                    }
                    function escapeHtml(s) {
                        var div = document.createElement('div');
                        div.textContent = s;
                        return div.innerHTML;
                    }

                    function applyDependencyChain() {
                        document.querySelectorAll('.product-variation-block').forEach(function(block) {
                            var dependsOn = (block.getAttribute('data-depends-on') || '').trim();
                            if (!dependsOn) return;
                            var parentId = getSelectedOptionIdInVariation(dependsOn);
                            filterDependentVariation(block, parentId);
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

                    function showVariationStep(stepIndex) {
                        currentVariationStep = stepIndex;
                        document.querySelectorAll('.variation-step-panel').forEach(function(panel) {
                            var idx = parseInt(panel.getAttribute('data-step-index'), 10);
                            var summary = panel.querySelector('.variation-step-summary');
                            var full = panel.querySelector('.variation-step-full');
                            var card = panel.querySelector('.variation-step-card');
                            panel.style.display = '';
                            if (idx === stepIndex) {
                                if (summary) { summary.classList.add('hidden'); summary.classList.remove('flex'); }
                                if (full) { full.classList.remove('hidden'); full.style.display = ''; }
                                if (card) { card.classList.add('border-primary-400'); card.classList.remove('border-slate-200'); }
                            } else if (idx < stepIndex) {
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
                            if (i === stepIndex) {
                                dot.classList.add('bg-primary-50/80', 'border-primary-100');
                                if (num) { num.classList.add('bg-primary-500', 'text-white'); num.classList.remove('bg-slate-200', 'text-slate-600', 'bg-emerald-500'); }
                                if (check) check.classList.add('hidden');
                                dot.removeAttribute('aria-disabled');
                            } else if (i < stepIndex) {
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
                    }

                    function selectOption(btn) {
                        var variation = btn.getAttribute('data-variation');
                        var optionValue = btn.getAttribute('data-option') || '';
                        var container = btn.closest('.product-variation-block');
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
                            var parentId = btn.getAttribute('data-option-id');
                            depBlock.style.display = '';
                            filterDependentVariation(depBlock, parentId);
                        });
                        applyDependencyChain();
                        if (totalVariationSteps > 0 && currentVariationStep < totalVariationSteps - 1) {
                            showVariationStep(currentVariationStep + 1);
                        } else if (totalVariationSteps > 0 && allVisibleVariationsSelected()) {
                            showVariationStep(totalVariationSteps - 1);
                        }
                    }

                    document.querySelectorAll('.product-option').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            if (this.style.display === 'none') return;
                            selectOption(this);
                        });
                    });

                    document.querySelectorAll('.variation-step-dot').forEach(function(dot) {
                        dot.addEventListener('click', function() {
                            if (this.getAttribute('aria-disabled') === 'true') return;
                            var step = parseInt(this.getAttribute('data-step'), 10);
                            if (isNaN(step)) return;
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
                            var step = parseInt(panel.getAttribute('data-step-index'), 10);
                            showVariationStep(step);
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
                    function updateSizeTableVisibility() {
                        var selected = getSelectedOptions();
                        var formEl = document.getElementById('add-to-cart-form');
                        var simpleWrap = document.getElementById('quantity-simple-wrap');
                        var quantityInput = document.getElementById('quantity-input');
                        var hasVariations = document.querySelectorAll('.product-variation-block').length > 0;
                        var allVariationsSelected = allVisibleVariationsSelected();
                        var sizeTableContent = document.querySelector('.size-table-step-content');

                        if (!allVariationsSelected) {
                            if (sizeTableContent) sizeTableContent.classList.add('hidden');
                            document.querySelectorAll('.size-table-wrap').forEach(function(wrap) { wrap.classList.add('hidden'); });
                            if (simpleWrap) simpleWrap.classList.toggle('hidden', hasVariations);
                            if (quantityInput) quantityInput.setAttribute('name', 'quantity');
                            return;
                        }

                        if (sizeTableContent) sizeTableContent.classList.remove('hidden');
                        var productTriggerVariation = (formEl && formEl.getAttribute('data-size-table-trigger-variation')) ? formEl.getAttribute('data-size-table-trigger-variation').trim() : '';
                        var productTriggerSelected = productTriggerVariation ? findSelectedValueForVariation(selected, productTriggerVariation) : '';
                        if (productTriggerVariation && !productTriggerSelected) {
                            document.querySelectorAll('.size-table-wrap').forEach(function(wrap) { wrap.classList.add('hidden'); });
                            if (simpleWrap) simpleWrap.classList.toggle('hidden', hasVariations);
                            if (quantityInput) quantityInput.setAttribute('name', 'quantity');
                            return;
                        }
                        var anyTableVisible = false;
                        function findSelectedValueForVariation(selected, variation) {
                        if (!variation) return '';
                        var vv = variation.trim().toLowerCase();
                        var val = (selected[variation] || '').trim();
                        if (val) return val;
                        var keyMatch = Object.keys(selected).find(function(k) {
                            var kk = (k || '').trim().toLowerCase();
                            if (kk === vv) return true;
                            if (kk.indexOf(vv) === 0) return true;
                            var firstWord = kk.split(/\s+/)[0] || '';
                            return firstWord === vv;
                        });
                        return keyMatch ? (selected[keyMatch] || '').trim() : '';
                    }
                        document.querySelectorAll('.size-table-wrap').forEach(function(wrap) {
                            var variation = (wrap.getAttribute('data-trigger-variation') || '').trim();
                            var value = (wrap.getAttribute('data-trigger-value') || '').trim();
                            var selectedVal = findSelectedValueForVariation(selected, variation);
                            var show = false;
                            if (variation && value) {
                                show = valueMatchesTrigger(selectedVal, value);
                            } else if (variation) {
                                show = selectedVal.length > 0;
                            } else {
                                var slug = (wrap.getAttribute('data-slug') || '').toLowerCase();
                                if (slug === 'erkek') show = Object.keys(selected).some(function(k) { return valueMatchesTrigger((selected[k] || '').trim(), 'Erkek'); });
                                else if (slug === 'kadin') show = Object.keys(selected).some(function(k) { return valueMatchesTrigger((selected[k] || '').trim(), 'Kadın'); });
                                else if (slug === 'cocuk') show = Object.keys(selected).some(function(k) { return valueMatchesTrigger((selected[k] || '').trim(), 'Çocuk'); });
                            }
                            wrap.classList.toggle('hidden', !show);
                            if (show) anyTableVisible = true;
                        });
                        if (simpleWrap) {
                            simpleWrap.classList.toggle('hidden', anyTableVisible || (hasVariations && !allVariationsSelected));
                        }
                        if (quantityInput) quantityInput.setAttribute('name', anyTableVisible ? 'quantity_placeholder' : 'quantity');
                    }

                    applyDependencyChain();
                    if (totalVariationSteps > 0) showVariationStep(0);
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
                                        if (titleEl) titleEl.textContent = 'Minimum Sipariş Uyarısı';
                                        descEl.textContent = 'Minimum sipariş adeti ' + minOrder + ' olmalıdır. Toplam: ' + total;
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
                                        if (titleEl) titleEl.textContent = 'Stok Yetersiz';
                                        descEl.textContent = 'Maksimum ' + availableStock + ' adet ekleyebilirsiniz. Toplam: ' + total;
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
                                        if (titleEl) titleEl.textContent = 'Stok Yetersiz';
                                        descEl.textContent = 'Maksimum ' + availableStock + ' adet ekleyebilirsiniz. Girdiğiniz adet: ' + qty;
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
