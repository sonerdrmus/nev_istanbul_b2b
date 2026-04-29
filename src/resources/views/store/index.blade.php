@extends('store.layout')

@php
    $showHomeProductSection = $currentCategory
        || trim((string) (request('q') ?? '')) !== ''
        || request()->filled('company')
        || request('in_stock') === '1'
        || request()->filled('status_satista')
        || request()->filled('status_yakinda')
        || request()->filled('color')
        || request()->filled('cinsiyet')
        || (request()->filled('sort') && request('sort') !== 'default')
        || (request()->filled('per_page') && (int) request('per_page') !== 12)
        || request()->filled('category')
        || request()->filled('parent');
    $filterUrl = function ($overrides = []) {
        $params = array_merge(request()->only(['category', 'parent', 'company', 'in_stock', 'status_satista', 'status_yakinda', 'color', 'cinsiyet', 'q', 'sort', 'per_page']), $overrides);
        $params = array_filter($params, fn ($v) => $v !== null && $v !== '');

        return route('home', $params);
    };
@endphp

@section('store_content_full_width', '1')

@section('title', $currentCategory ? ($currentCategory->name . ' Ürünleri') : ($showHomeProductSection ? 'Ürünler' : 'Ana Sayfa'))

@section('hero')
    @if(!$currentCategory)

    @if($bannerSlides->isNotEmpty())
    {{-- Banner slider (admin'den yönetilen slaytlar) --}}
    <div class="relative w-full overflow-hidden bg-slate-900" id="hero-carousel">
        @foreach($bannerSlides as $index => $slide)
        <div class="carousel-slide {{ $index === 0 ? 'active' : '' }} flex items-center px-4 sm:px-8 lg:px-16 {{ $slide->text_align === 'center' ? 'justify-center text-center' : ($slide->text_align === 'right' ? 'justify-end text-right' : 'justify-start') }}"
             @if($slide->image_path) style="background-image: url('{{ Storage::url($slide->image_path) }}'); background-size: cover; background-position: center;" @else style="background: linear-gradient(135deg, #114a8c 0%, #155fb3 50%, #0f3c6f 100%);" @endif>
            @if($slide->image_path)<div class="absolute inset-0 bg-black/40"></div>@else<div class="absolute inset-0 bg-black/20"></div>@endif
            <div class="relative z-10 max-w-7xl mx-auto w-full py-12 sm:py-16 lg:py-20 {{ $slide->text_align === 'center' ? 'text-center' : '' }} {{ $slide->text_align === 'right' ? 'text-right' : '' }}">
                @if($slide->title)<p class="text-primary-100 text-sm font-semibold uppercase tracking-widest mb-2">{{ $slide->title }}</p>@endif
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white tracking-tight max-w-2xl {{ $slide->text_align === 'center' ? 'mx-auto' : ($slide->text_align === 'right' ? 'ml-auto' : '') }}">{{ $slide->headline }}</h2>
                @if($slide->description)<p class="mt-4 text-lg text-white/90 max-w-xl {{ $slide->text_align === 'center' ? 'mx-auto' : ($slide->text_align === 'right' ? 'ml-auto' : '') }}">{{ $slide->description }}</p>@endif
                @if($slide->button_text && $slide->button_url)
                <a href="{{ $slide->button_url }}" class="inline-flex items-center gap-2 mt-6 px-6 py-3.5 rounded-xl bg-white text-primary-700 font-semibold shadow-lg hover:bg-primary-50 transition-all duration-200">
                    {{ $slide->button_text }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                @endif
            </div>
        </div>
        @endforeach

        <button type="button" id="carousel-prev" class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/90 hover:bg-white text-slate-800 shadow-lg flex items-center justify-center transition-colors" aria-label="Önceki">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button type="button" id="carousel-next" class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/90 hover:bg-white text-slate-800 shadow-lg flex items-center justify-center transition-colors" aria-label="Sonraki">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2" id="carousel-dots">
            @foreach($bannerSlides as $i => $s)
            <button type="button" class="carousel-dot w-2.5 h-2.5 rounded-full bg-white/80 hover:bg-white transition-all {{ $i === 0 ? 'carousel-dot-active' : '' }}" data-index="{{ $i }}" aria-label="Slide {{ $i + 1 }}"></button>
            @endforeach
        </div>
    </div>
    <script>
    (function() {
        var carousel = document.getElementById('hero-carousel');
        if (!carousel) return;
        var slides = carousel.querySelectorAll('.carousel-slide');
        var dots = carousel.querySelectorAll('.carousel-dot');
        var prev = document.getElementById('carousel-prev');
        var next = document.getElementById('carousel-next');
        var current = 0;
        var total = slides.length;
        function goTo(i) {
            current = (i + total) % total;
            slides.forEach(function(s, j) { s.classList.toggle('active', j === current); });
            dots.forEach(function(d, j) { d.classList.toggle('carousel-dot-active', j === current); });
        }
        if (prev) prev.addEventListener('click', function() { goTo(current - 1); });
        if (next) next.addEventListener('click', function() { goTo(current + 1); });
        dots.forEach(function(d, i) { d.addEventListener('click', function() { goTo(i); }); });
        var timer = setInterval(function() { goTo(current + 1); }, 5000);
        carousel.addEventListener('mouseenter', function() { clearInterval(timer); });
        carousel.addEventListener('mouseleave', function() { timer = setInterval(function() { goTo(current + 1); }, 5000); });
    })();
    </script>
    @endif

    @php
        $homeShowcaseHasCategories = isset($homeCategoryShowcase) && $homeCategoryShowcase->isNotEmpty();
        $homeShowcaseHasProducts = isset($homeProductShowcase) && $homeProductShowcase->isNotEmpty();
    @endphp
    @if($homeShowcaseHasCategories || $homeShowcaseHasProducts)
    <div class="w-full bg-white border-b border-slate-200 py-8 sm:py-10" aria-label="Kategori ve ürün vitrinleri">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight mb-5 sm:mb-6">
                @if($homeShowcaseHasCategories && $homeShowcaseHasProducts)
                    Kategoriler ve ürünler
                @elseif($homeShowcaseHasProducts)
                    Ürünler
                @else
                    Kategoriler
                @endif
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach($homeCategoryShowcase ?? [] as $cat)
                    <a href="{{ route('home', ['category' => $cat->slug]) }}" class="group flex flex-col rounded-2xl border border-slate-200 bg-slate-50/80 overflow-hidden shadow-sm hover:shadow-lg hover:border-primary-200/80 transition-all duration-300 ring-1 ring-transparent hover:ring-primary-100/60">
                        <div class="relative aspect-[4/3] bg-slate-200 overflow-hidden">
                            <img src="{{ Storage::url($cat->image_path) }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" width="400" height="300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent opacity-80 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                        </div>
                        <div class="px-3 py-3 sm:px-3.5 sm:py-3.5 flex-1 flex items-center justify-center text-center min-h-[3rem]">
                            <span class="text-sm font-semibold text-slate-800 group-hover:text-primary-700 transition-colors line-clamp-2">{{ $cat->name }}</span>
                        </div>
                    </a>
                @endforeach
                @foreach($homeProductShowcase ?? [] as $product)
                    @php $vitrinThumb = $product->image ?? $product->productImages->first()?->path; @endphp
                    <a href="{{ route('store.product.show', $product) }}" class="group flex flex-col rounded-2xl border border-slate-200 bg-slate-50/80 overflow-hidden shadow-sm hover:shadow-lg hover:border-primary-200/80 transition-all duration-300 ring-1 ring-transparent hover:ring-primary-100/60">
                        <div class="relative aspect-[4/3] bg-slate-200 overflow-hidden flex items-center justify-center">
                            @if($vitrinThumb)
                                <img src="{{ Storage::url($vitrinThumb) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" width="400" height="300">
                            @else
                                <span class="text-slate-400 text-5xl select-none" aria-hidden="true">📦</span>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent opacity-80 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                        </div>
                        <div class="px-3 py-3 sm:px-3.5 sm:py-3.5 flex-1 flex items-center justify-center text-center min-h-[3rem]">
                            <span class="text-sm font-semibold text-slate-800 group-hover:text-primary-700 transition-colors line-clamp-2">{{ $product->name }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Promo banner strip (ikon + metin) --}}
    <div class="w-full bg-white border-b border-slate-200 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8">
                <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-primary-50/50 transition-colors">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900">Ücretsiz Kargo</h3>
                        <p class="text-sm text-slate-600">Belirli tutar üzeri siparişlerde</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-primary-50/50 transition-colors">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900">Güvenli Ödeme</h3>
                        <p class="text-sm text-slate-600">Havale / EFT ile güvenle ödeyin</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-primary-50/50 transition-colors">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900">Kolay İade</h3>
                        <p class="text-sm text-slate-600">Memnuniyet garantisi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif
@endsection

@section('content')
    @if($showHomeProductSection)
    <div class="max-w-7xl mx-auto w-full">
    <div class="mb-6" id="urunler">
        @if($currentCategory)
            <nav class="flex items-center gap-2 text-sm text-slate-500 mb-3">
                <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">Tüm Ürünler</a>
                <span>/</span>
                @if($currentCategory->parent)
                    <span class="text-slate-700 font-medium">{{ $currentCategory->parent->name }} › {{ $currentCategory->name }}</span>
                @else
                    <span class="text-slate-700 font-medium">{{ $currentCategory->name }}</span>
                @endif
            </nav>
        @endif
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">{{ $currentCategory ? ($currentCategory->name . ' Ürünleri') : 'Ürünler' }}</h1>
        <p class="mt-2 text-slate-600">İhtiyacınız olan ürünleri sepetinize ekleyin, havale ile güvenle sipariş verin.</p>
    </div>

    <div class="min-w-0 w-full">
        {{-- Ürün listesi + sıralama / sayfa (sol filtre kaldırıldı; kategori layout üst şeridinden) --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <p class="text-slate-600 text-sm">{{ $products->total() }} ürün bulundu</p>
                <div class="flex flex-wrap items-center gap-3">
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <span>Sırala:</span>
                        <select onchange="window.location.href=this.value" class="rounded-lg border border-slate-300 text-slate-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="{{ $filterUrl(['sort' => 'default']) }}" {{ request('sort', 'default') === 'default' ? 'selected' : '' }}>Önerilen</option>
                            <option value="{{ $filterUrl(['sort' => 'name_asc']) }}" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>İsim A–Z</option>
                            <option value="{{ $filterUrl(['sort' => 'name_desc']) }}" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>İsim Z–A</option>
                            <option value="{{ $filterUrl(['sort' => 'price_asc']) }}" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Fiyat (düşük → yüksek)</option>
                            <option value="{{ $filterUrl(['sort' => 'price_desc']) }}" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Fiyat (yüksek → düşük)</option>
                            <option value="{{ $filterUrl(['sort' => 'newest']) }}" {{ request('sort') === 'newest' ? 'selected' : '' }}>En yeni</option>
                        </select>
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <span>Sayfa başına:</span>
                        <select onchange="window.location.href=this.value" class="rounded-lg border border-slate-300 text-slate-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                            @foreach([12, 20, 40, 60] as $n)
                                <option value="{{ $filterUrl(['per_page' => $n]) }}" {{ (int)request('per_page', 12) === $n ? 'selected' : '' }}>{{ $n }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>

    @if($products->isEmpty())
        <div class="rounded-2xl bg-white border border-slate-200 p-16 text-center shadow-sm">
            <div class="w-20 h-20 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-4xl mb-5">📦</div>
            <p class="text-slate-700 font-semibold text-lg">Bu kriterlere uygun ürün bulunamadı.</p>
            <p class="text-slate-500 text-sm mt-1">Tüm ürünlere göz atmak için <a href="{{ route('home') }}" class="text-primary-600 hover:underline">Tümünü temizle</a> veya <a href="{{ route('home') }}" class="text-primary-600 hover:underline">buraya tıklayın</a>.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-5 sm:gap-5 md:gap-6">
            @foreach($products as $product)
                @php $isComingSoon = ($product->status ?? 'satista') === 'yakinda_gelecek'; @endphp
                <article class="group bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-0.5 hover:border-slate-300/80 transition-all duration-300 flex flex-col h-full">
                    <a href="{{ route('store.product.show', $product) }}" class="relative block aspect-square bg-slate-100 flex items-center justify-center overflow-hidden">
                        @php $thumbPath = $product->image ?? $product->productImages->first()?->path; @endphp
                        @if($thumbPath)
                            <img src="{{ Storage::url($thumbPath) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 {{ $isComingSoon ? 'opacity-95' : '' }}">
                        @else
                            <span class="text-slate-300 text-5xl">📦</span>
                        @endif
                        @if($isComingSoon)
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent pointer-events-none"></div>
                        @endif
                        @if($product->category)
                            <span class="absolute top-3 left-3 px-2.5 py-1.5 rounded-lg bg-white/95 backdrop-blur-sm text-xs font-medium text-slate-700 shadow-sm">{{ $product->category->name }}</span>
                        @endif
                        @if($isComingSoon)
                            <span class="absolute top-3 right-3 inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-amber-500/95 backdrop-blur-sm text-xs font-semibold text-white shadow-sm">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Yakında gelecek
                            </span>
                        @endif
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300 pointer-events-none"></div>
                    </a>
                    <div class="p-6 flex-1 flex flex-col min-h-0">
                        <a href="{{ route('store.product.show', $product) }}" class="block flex-1 flex flex-col min-h-0">
                            <p class="text-xs font-semibold text-primary-600 uppercase tracking-wider mb-1.5">{{ $product->company?->name ?? '—' }}</p>
                            <h2 class="font-semibold text-slate-900 text-lg leading-snug hover:text-primary-600 transition-colors line-clamp-2" title="{{ $product->name }}">{{ $product->name }}</h2>
                            @if($product->description)
                                <p class="text-sm text-slate-500 mt-2 line-clamp-2 min-h-[2.5rem]">{{ Str::limit($product->description, 80) }}</p>
                            @else
                                <p class="mt-2 min-h-[2.5rem]"></p>
                            @endif
                        </a>
                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between gap-3 min-h-[3.25rem] flex-wrap">
                            @if($canSeePrices)
                                <p class="text-xl font-bold text-slate-900 whitespace-nowrap">{{ $product->getPriceInCurrency($selectedCurrency ?? null, $customerDiscountPercent ?? null) }}</p>
                                @if($isComingSoon)
                                    <a href="{{ route('store.product.show', $product) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-medium text-sm transition-colors whitespace-nowrap flex-shrink-0">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Yakında satışta</span>
                                    </a>
                                @else
                                    @if($product->stock_quantity === null || (int) $product->stock_quantity > 0)
                                        <a href="{{ route('store.product.show', $product) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-medium text-sm shadow-sm hover:shadow transition-all duration-200 flex-shrink-0">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                            Varyasyon Belirle
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-3.5 py-2 rounded-xl bg-slate-100 text-slate-600 font-medium text-sm flex-shrink-0">Stokta yok</span>
                                    @endif
                                @endif
                            @else
                                <p class="text-slate-500 text-sm">Fiyatları görmek için giriş yapın.</p>
                                <a href="{{ route('store.product.show', $product) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex-shrink-0">Ürün detayı</a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

            @if($products->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $products->links() }}
                </div>
            @endif
        @endif
    </div>
    </div>
    @else
        {{-- Eski kampanya linkleri (/#urunler) için hedef; görünür ürün listesi yok --}}
        <div id="urunler" class="sr-only" aria-hidden="true"></div>
    @endif

@endsection
