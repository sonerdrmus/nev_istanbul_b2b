{{-- Anasayfa üst kategori şeridi: modern pill navigasyon + mega panel (fixed) --}}
@php
    $qBase = request()->query();
@endphp
<div class="relative z-[45] border-b border-slate-200/90 bg-gradient-to-b from-white via-slate-50/40 to-white shadow-[0_1px_0_0_rgba(15,23,42,0.04)]" id="home-mega-nav-bar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 py-3 sm:py-3.5" aria-label="Ürün kategorileri">
            {{-- Tek satır: tüm kırılımlarda yatay kaydırma (wrap kapatıldı — şerit taşması / iki satır bozulmasını önler) --}}
            <div class="flex min-w-0 flex-1 flex-nowrap items-center gap-2 overflow-x-auto overflow-y-visible overscroll-x-contain scrollbar-hide py-0.5 touch-pan-x" id="home-mega-nav-strip">
                <a href="{{ route('home') }}"
                   class="group relative flex-shrink-0 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold tracking-tight transition-all duration-200 whitespace-nowrap
                   {{ ! request('category')
                        ? 'bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-md shadow-primary-600/25 ring-1 ring-primary-500/30'
                        : 'border border-slate-200/90 bg-white text-slate-700 shadow-sm hover:border-primary-200 hover:bg-primary-50/60 hover:text-primary-800 hover:shadow-md' }}">
                    <svg class="h-4 w-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/></svg>
                    Tüm Ürünler
                </a>

                @foreach($homeMegaNav as $item)
                    @php
                        $cat = $item['category'];
                        $slug = $item['slug'];
                        $label = $item['label'];
                        $products = $item['products'] ?? collect();
                        $isActive = (string) request('category') === $slug;
                    @endphp
                    {{-- w-max: flex min-width:auto ile mega içeriği satırı şişirmesin (panel kapalıyken tek satır) --}}
                    <div class="relative w-max max-w-full flex-shrink-0" data-home-mega>
                        <button type="button"
                                data-home-mega-trigger
                                class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold tracking-tight transition-all duration-200 whitespace-nowrap
                                {{ $isActive
                                    ? 'border-primary-300 bg-primary-50 text-primary-900 shadow-sm ring-2 ring-primary-500/20'
                                    : 'border-slate-200/90 bg-white text-slate-700 shadow-sm hover:border-primary-200 hover:bg-slate-50 hover:text-primary-800 hover:shadow-md' }}"
                                aria-expanded="false"
                                aria-haspopup="true">
                            @if($cat)
                                <span class="text-primary-600">
                                    @include('store.partials.category-icon', ['category' => $cat, 'size' => 'w-4 h-4'])
                                </span>
                            @endif
                            {{ $label }}
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div data-home-mega-panel
                             id="home-mega-panel-{{ $slug }}"
                             class="home-mega-panel-shell overflow-hidden rounded-2xl border border-slate-200/90 bg-white/95 shadow-[0_16px_30px_-14px_rgba(15,23,42,0.22)] ring-1 ring-slate-900/[0.05] backdrop-blur-xl"
                             aria-hidden="true"
                             role="region"
                             aria-label="{{ $label }} alt menü">
                            <div class="max-h-[min(72vh,22rem)] overflow-y-auto px-4 py-4 sm:px-5 sm:py-4.5">
                                <div class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600/80">Koleksiyon</p>
                                        <h3 class="mt-1 text-lg font-bold tracking-tight text-slate-900">{{ $label }}</h3>
                                        @if($cat)
                                            <p class="mt-0.5 max-w-md text-xs text-slate-500">{{ $cat->name }} ürünlerini keşfedin.</p>
                                        @endif
                                    </div>
                                    <a href="{{ route('home', array_merge($qBase, ['category' => $slug, 'parent' => null])) }}"
                                       class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl border border-primary-200 bg-primary-50 px-3 py-2 text-xs font-semibold text-primary-700 transition hover:bg-primary-100">
                                        Tüm ürünler
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 12h4m0 0l-2-2m2 2l-2 2"/></svg>
                                    </a>
                                </div>

                                @if($products->isNotEmpty())
                                    <div class="my-4">
                                        <div class="mb-2 flex items-center justify-between gap-3">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Öne çıkan ürünler</p>
                                            <span class="text-[11px] text-slate-400">{{ $products->count() }} ürün</span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6">
                                            @foreach($products as $product)
                                                @php $thumbPath = $product->image ?? $product->productImages->first()?->path; @endphp
                                                <a href="{{ route('store.product.show', $product) }}"
                                                   class="group/product rounded-xl border border-slate-100 bg-white p-2 transition-all hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-sm">
                                                    <div class="aspect-square overflow-hidden rounded-lg bg-slate-100">
                                                        @if($thumbPath)
                                                            <img src="{{ Storage::url($thumbPath) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition-transform duration-300 group-hover/product:scale-105">
                                                        @else
                                                            <div class="flex h-full w-full items-center justify-center text-2xl text-slate-300">📦</div>
                                                        @endif
                                                    </div>
                                                    <div class="pt-2">
                                                        <p class="line-clamp-2 text-xs font-semibold leading-tight text-slate-800 transition-colors group-hover/product:text-primary-700">{{ $product->name }}</p>
                                                        @if(isset($canSeePrices) && $canSeePrices)
                                                            <p class="mt-1 text-xs font-bold text-slate-900">{{ $product->getPriceInCurrency($selectedCurrency ?? null, $customerDiscountPercent ?? null) }}</p>
                                                        @else
                                                            <p class="mt-1 text-[11px] font-medium text-slate-500">Detay</p>
                                                        @endif
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($cat && $cat->children->isNotEmpty())
                                    <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Alt kategoriler</p>
                                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                                        @foreach($cat->children as $child)
                                            <a href="{{ route('home', array_merge($qBase, ['parent' => $slug, 'category' => $child->slug])) }}"
                                               class="group/card flex items-center gap-2.5 rounded-xl border border-slate-100 bg-slate-50/50 p-2.5 text-left transition-all hover:border-primary-200 hover:bg-white hover:shadow-sm">
                                                <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 ring-1 ring-primary-100 transition group-hover/card:bg-primary-100 group-hover/card:ring-primary-200">
                                                    @include('store.partials.category-icon', ['category' => $child, 'size' => 'w-5 h-5', 'class' => 'text-primary-600'])
                                                </span>
                                                <span class="min-w-0 flex-1 text-xs font-semibold leading-snug text-slate-800 group-hover/card:text-primary-900">{{ $child->name }}</span>
                                                <svg class="h-3.5 w-3.5 flex-shrink-0 text-slate-300 transition group-hover/card:translate-x-0.5 group-hover/card:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        @endforeach
                                    </div>
                                @elseif($products->isEmpty())
                                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/90 px-6 py-10 text-center">
                                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-100 text-primary-600">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-slate-600">Bu kategorideki ürünleri listelemek için mağazaya gidin.</p>
                                        <a href="{{ route('home', array_merge($qBase, ['category' => $slug, 'parent' => null])) }}"
                                           class="mt-5 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-primary-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-primary-50 hover:ring-primary-200">
                                            {{ $label }} ürünlerine git
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </nav>
    </div>
</div>

<script>
(function () {
    var DELAY = 220;
    var panels = document.querySelectorAll('#home-mega-nav-bar [data-home-mega-panel]');

    function placePanel(panel, trigger) {
        var r = trigger.getBoundingClientRect();
        var w = Math.min(980, window.innerWidth - 24);
        var left = Math.max(12, r.left + r.width / 2 - w / 2);
        if (left + w > window.innerWidth - 12) {
            left = window.innerWidth - w - 12;
        }
        if (left < 12) left = 12;
        panel.style.position = 'fixed';
        panel.style.left = left + 'px';
        panel.style.top = (r.bottom + 10) + 'px';
        panel.style.width = w + 'px';
        panel.style.zIndex = '200';
    }

    function closeAll() {
        panels.forEach(function (p) {
            p.classList.remove('is-open');
            p.setAttribute('aria-hidden', 'true');
        });
        document.querySelectorAll('#home-mega-nav-bar [data-home-mega-trigger]').forEach(function (t) {
            t.setAttribute('aria-expanded', 'false');
        });
    }

    document.querySelectorAll('#home-mega-nav-bar [data-home-mega]').forEach(function (wrap) {
        var trig = wrap.querySelector('[data-home-mega-trigger]');
        var panel = wrap.querySelector('[data-home-mega-panel]');
        if (!trig || !panel) return;
        var t;
        function open() {
            clearTimeout(t);
            closeAll();
            placePanel(panel, trig);
            panel.classList.add('is-open');
            panel.setAttribute('aria-hidden', 'false');
            trig.setAttribute('aria-expanded', 'true');
        }
        function schedClose() {
            t = setTimeout(function () {
                panel.classList.remove('is-open');
                panel.setAttribute('aria-hidden', 'true');
                trig.setAttribute('aria-expanded', 'false');
            }, DELAY);
        }
        trig.addEventListener('mouseenter', open);
        trig.addEventListener('mouseleave', schedClose);
        panel.addEventListener('mouseenter', function () { clearTimeout(t); });
        panel.addEventListener('mouseleave', schedClose);
    });

    window.addEventListener('scroll', closeAll, { passive: true });
    window.addEventListener('resize', closeAll);
    document.addEventListener('click', function (e) {
        var bar = document.getElementById('home-mega-nav-bar');
        if (bar && !bar.contains(e.target)) closeAll();
    });
})();
</script>
