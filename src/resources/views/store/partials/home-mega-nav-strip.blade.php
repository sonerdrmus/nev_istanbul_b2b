{{-- Anasayfa üst kategori şeridi: modern pill navigasyon + sade alt menü --}}
@php
    $qBase = request()->query();
@endphp
<div class="relative z-[45] border-b border-slate-200/90 bg-gradient-to-b from-white via-slate-50/40 to-white shadow-[0_1px_0_0_rgba(15,23,42,0.04)]" id="home-mega-nav-bar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 py-3 sm:py-3.5" aria-label="{{ __('store.mega_nav.nav_aria') }}">
            {{-- Tek satır: tüm kırılımlarda yatay kaydırma (wrap kapatıldı — şerit taşması / iki satır bozulmasını önler) --}}
            <div class="flex min-w-0 flex-1 flex-nowrap items-center gap-2 overflow-x-auto overflow-y-visible overscroll-x-contain scrollbar-hide py-0.5 touch-pan-x" id="home-mega-nav-strip">
                <a href="{{ route('home') }}"
                   class="group relative flex-shrink-0 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold tracking-tight transition-all duration-200 whitespace-nowrap
                   {{ ! request('category')
                        ? 'bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-md shadow-primary-600/25 ring-1 ring-primary-500/30'
                        : 'border border-slate-200/90 bg-white text-slate-700 shadow-sm hover:border-primary-200 hover:bg-primary-50/60 hover:text-primary-800 hover:shadow-md' }}">
                    <svg class="h-4 w-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/></svg>
                    {{ __('store.mega_nav.all_products') }}
                </a>

                @foreach($topMenuCategories ?? collect() as $cat)
                    @php
                        $slug = $cat->slug;
                        $label = $cat->localized_name;
                        $activeCategory = (string) request('category');
                        $isActive = $activeCategory === $slug
                            || (string) request('parent') === $slug
                            || $cat->children->contains('slug', $activeCategory);
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
                            {{ $label }}
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div data-home-mega-panel
                             id="home-mega-panel-{{ $slug }}"
                             class="home-mega-panel-shell overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl ring-1 ring-slate-900/[0.05]"
                             aria-hidden="true"
                             role="menu"
                             aria-label="{{ __('store.mega_nav.submenu_aria', ['category' => $label]) }}">
                            <div class="max-h-[min(70vh,22rem)] overflow-y-auto py-2">
                                <p class="px-4 pt-1 pb-2 text-[11px] font-semibold uppercase tracking-wide text-slate-500" id="mega-panel-heading-{{ $slug }}">
                                    {{ __('store.mega_nav.section_products_title') }}
                                </p>

                                @php
                                    $megaPanelProducts = ($topMenuCategoryProducts ?? collect())->get($cat->id, collect());
                                @endphp
                                @if($megaPanelProducts->isNotEmpty())
                                    <ul class="list-none border-t border-slate-100 pb-1 pt-2" role="list">
                                        @foreach($megaPanelProducts as $megaProduct)
                                            <li>
                                                <a href="{{ route('store.product.show', $megaProduct) }}"
                                                   class="block px-4 py-2 text-sm leading-snug text-slate-700 transition-colors hover:bg-slate-50 hover:text-primary-700 line-clamp-2"
                                                   role="menuitem">
                                                    {{ $megaProduct->localized_name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if($cat->children->isNotEmpty())
                                    <div class="mx-4 border-t border-slate-100 pt-2 pb-1">
                                        <p class="mb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                            {{ __('store.mega_nav.subcategories_title') }}
                                        </p>
                                    </div>
                                    @foreach($cat->children as $child)
                                        <a href="{{ route('home', array_merge($qBase, ['parent' => $slug, 'category' => $child->slug])) }}"
                                           class="block px-4 py-2 text-sm text-slate-700 transition-colors hover:bg-slate-50 hover:text-primary-700"
                                           role="menuitem">
                                            {{ $child->name }}
                                        </a>
                                    @endforeach
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
        var w = Math.min(340, window.innerWidth - 24);
        var left = Math.max(12, r.left);
        if (left + w > window.innerWidth - 12) {
            left = window.innerWidth - w - 12;
        }
        if (left < 12) left = 12;
        panel.style.position = 'fixed';
        panel.style.left = left + 'px';
        panel.style.top = (r.bottom + 8) + 'px';
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
