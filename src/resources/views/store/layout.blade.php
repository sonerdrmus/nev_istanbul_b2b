<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) – E-Ticaret</title>
    @stack('meta')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'] },
                    colors: {
                        // Logo mavi tonlarına uyumlu
                        primary: { 50: '#eef6ff', 100: '#d9ecff', 200: '#b7dcff', 300: '#84c5ff', 400: '#4ea6ff', 500: '#1f7bd6', 600: '#155fb3', 700: '#114a8c', 800: '#0f3c6f', 900: '#0b2a4d' },
                        accent: { 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1', 400: '#94a3b8', 500: '#64748b', 600: '#475569', 700: '#334155', 800: '#1e293b', 900: '#0f172a' }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .dropdown:hover .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        .dropdown-menu { opacity: 0; visibility: hidden; transform: translateY(-8px); transition: opacity 0.2s, transform 0.2s, visibility 0.2s; }
        /* Slider: Slaytlar üst üste, sadece aktif görünür (Tailwind .flex display’i ezmesin diye) */
        #hero-carousel { position: relative; min-height: 320px; }
        @media (min-width: 640px) { #hero-carousel { min-height: 380px; } }
        @media (min-width: 1024px) { #hero-carousel { min-height: 420px; } }
        #hero-carousel .carousel-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease;
        }
        #hero-carousel .carousel-slide.active {
            opacity: 1;
            pointer-events: auto;
        }
        .carousel-dot-active { width: 0.75rem; height: 0.75rem; background: white !important; }
        .bottom-nav-active { color: #155fb3; }
        .dropdown.open .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0) translateX(-50%); }
        details[open] > .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        /* Anasayfa mega: ayrıntılar head stack sonrası blokta (!important + display:none) */
        /* Kategori şeridi menüleri JS ile açılır (hidden sınıfı) */
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>
    @stack('head')
    {{-- Mega panel: Tailwind CDN’den sonra — kapalıyken display:none flex min-width kırılmasını kesin önler --}}
    <style>
        #home-mega-nav-bar .home-mega-panel-shell:not(.is-open) {
            display: none !important;
        }
        #home-mega-nav-bar .home-mega-panel-shell.is-open {
            display: block !important;
            position: fixed;
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            max-height: min(72vh, 22rem);
            max-width: min(100vw - 1.5rem, 62rem);
            transform: translateY(0);
            transition: opacity 0.2s ease, transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen antialiased flex flex-col pb-20 lg:pb-0">
    {{-- Topbar: ÜCRETSİZ KARGO + Kurlar + Para birimi seçici --}}
    <div class="bg-primary-600 text-white py-2 px-4 text-sm font-medium">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-center sm:justify-between gap-2">
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                ÜCRETSİZ KARGO — Belirli tutar üzeri siparişlerde
            </span>
            <div class="flex items-center gap-2 flex-wrap justify-center" id="topbar-currencies">
                @if(isset($currencies) && $currencies->isNotEmpty())
                    @if($currencies->count() === 1)
                        @php $curr = $currencies->first(); $isTry = $curr->code === 'TRY'; $rateFormatted = $isTry ? null : number_format((float) $curr->exchange_rate, 2, ',', '.'); @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-sm font-medium text-primary-100" title="{{ $curr->code }} ({{ $curr->symbol }})">
                            @if($isTry)
                                TRY ({{ $curr->symbol }})
                            @else
                                <span class="opacity-90">{{ $curr->symbol }}</span>
                                <span>{{ $curr->code }}</span>
                                <span class="topbar-rate" data-currency="{{ $curr->code }}">{{ $rateFormatted }}</span>
                            @endif
                        </span>
                    @else
                        @foreach($currencies as $curr)
                            @php
                                $path = request()->getPathInfo() ?: '/';
                                $query = array_merge(request()->query(), ['currency' => $curr->code]);
                                $currencyUrl = $path . ($query ? '?' . http_build_query($query) : '');
                                $isTry = $curr->code === 'TRY';
                                $rateFormatted = $isTry ? null : number_format((float) $curr->exchange_rate, 2, ',', '.');
                            @endphp
                            <a href="{{ $currencyUrl }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-sm font-medium transition-colors {{ (isset($selectedCurrency) && $selectedCurrency->code === $curr->code) ? 'bg-white/20 text-white' : 'text-primary-100 hover:bg-white/10 hover:text-white' }}" title="{{ $curr->code }} ({{ $curr->symbol }})" data-currency-code="{{ $curr->code }}">
                                @if($isTry)
                                    <span>TRY ({{ $curr->symbol }})</span>
                                @else
                                    <span class="opacity-90">{{ $curr->symbol }}</span>
                                    <span>{{ $curr->code }}</span>
                                    <span class="topbar-rate" data-currency="{{ $curr->code }}">{{ $rateFormatted }}</span>
                                @endif
                            </a>
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Header: logo + ürün arama + sağ aksiyonlar --}}
    <header class="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 sm:gap-4 h-14 sm:h-16">
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-9 sm:h-11 w-auto object-contain">
                </a>

                <div class="flex-1 min-w-0 max-w-xl mx-2 sm:mx-4 relative" id="header-search-wrap">
                    <form action="{{ route('home') }}" method="GET" class="relative">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <input type="search" name="q" id="header-search-input" value="{{ $searchQuery ?? '' }}" placeholder="Ürün ara..." class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 text-slate-900 placeholder-slate-400 text-sm transition-colors" autocomplete="off">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded-lg text-slate-500 hover:text-primary-600 hover:bg-primary-50 transition-colors" aria-label="Ara">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </form>
                    <div id="header-search-dropdown" class="absolute left-0 right-0 top-full mt-1 bg-white rounded-xl border border-slate-200 shadow-xl max-h-[min(400px,70vh)] overflow-y-auto z-[100] hidden">
                        <div id="header-search-loading" class="p-4 text-center text-slate-500 text-sm hidden">Aranıyor...</div>
                        <div id="header-search-results" class="py-1"></div>
                        <div id="header-search-empty" class="p-4 text-center text-slate-500 text-sm hidden">Ürün bulunamadı.</div>
                    </div>
                </div>

                {{-- Üst bar: Kategoriler açılır menü --}}
                <details class="dropdown relative flex-shrink-0 hidden sm:block">
                    <summary class="list-none cursor-pointer select-none inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Kategoriler
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    @isset($menuCategories)
                    <div class="dropdown-menu absolute left-0 top-full mt-1 w-64 py-2 bg-white rounded-xl border border-slate-200 shadow-xl max-h-[70vh] overflow-y-auto z-50">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-colors first:rounded-t-xl">
                            <svg class="w-5 h-5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/></svg>
                            Tüm Ürünler
                        </a>
                        <div class="border-t border-slate-100 my-2"></div>
                        @foreach($menuCategories as $parent)
                            @if($parent->children->isNotEmpty())
                                <div class="px-3 pt-2 pb-1">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-1 mb-1.5">{{ $parent->name }}</p>
                                    <div class="space-y-0.5">
                                        @foreach($parent->children as $child)
                                            <a href="{{ route('home', ['category' => $child->slug]) }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                @include('store.partials.category-icon', ['category' => $child])
                                                {{ $child->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <div class="border-t border-slate-100 mt-2 pt-2">
                            <a href="{{ route('home') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-600 hover:bg-primary-50 transition-colors rounded-b-xl">
                                Tümünü gör
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                    @endisset
                </details>

                {{-- Sağ aksiyonlar: Sepet + Hesap --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('store.cart') }}" class="relative flex items-center justify-center w-10 h-10 sm:w-auto sm:px-4 sm:py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="hidden sm:inline ml-1">Sepet</span>
                    @php $headerCartCount = collect(session('cart', []))->sum('quantity'); @endphp
                    @if($headerCartCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[20px] h-5 px-1.5 flex items-center justify-center rounded-full bg-primary-500 text-white text-xs font-bold">{{ $headerCartCount > 99 ? '99+' : $headerCartCount }}</span>
                    @endif
                    </a>

                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ url('/admin') }}" class="hidden sm:inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium text-primary-700 hover:bg-primary-50 transition-colors">Admin</a>
                        @else
                            <a href="{{ url('/panel') }}" class="hidden sm:inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium text-primary-700 hover:bg-primary-50 transition-colors">Panelim</a>
                        @endif
                    @else
                        <button type="button" onclick="document.getElementById('login-modal').classList.remove('hidden')" class="hidden sm:inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium text-primary-700 hover:bg-primary-50 transition-colors">Giriş Yap</button>
                        <a href="{{ route('store.dealer-registration') }}" class="hidden sm:inline-flex items-center px-3 py-2 rounded-xl text-sm font-semibold bg-primary-600 hover:bg-primary-700 text-white transition-colors">
                            Kayıt Ol
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Giriş Yap Modal (modern UI) --}}
    <div id="login-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" aria-modal="true" role="dialog">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('login-modal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
            <div class="px-6 sm:px-8 pt-8 pb-2 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Giriş Yap</h2>
                        <p class="text-sm text-slate-500">Hesabınıza giriş yapın</p>
                    </div>
                </div>
                <button type="button" class="p-2 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition-colors" onclick="document.getElementById('login-modal').classList.add('hidden')" aria-label="Kapat">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('store.login') }}" method="POST" class="px-6 sm:px-8 pb-8 pt-4">
                @csrf
                @if(session('open_login_modal') && $errors->any())
                    <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif
                <div class="space-y-4">
                    <div>
                        <label for="login-email" class="block text-sm font-medium text-slate-700 mb-1.5">E-posta</label>
                        <input id="login-email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('email') border-red-500 @enderror"
                            placeholder="ornek@email.com">
                    </div>
                    <div>
                        <label for="login-password" class="block text-sm font-medium text-slate-700 mb-1.5">Şifre</label>
                        <input id="login-password" name="password" type="password" required autocomplete="current-password"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('password') border-red-500 @enderror"
                            placeholder="••••••••">
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input name="remember" type="checkbox" value="1" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-600">Beni hatırla</span>
                    </label>
                </div>
                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold shadow-sm hover:shadow transition-all">
                        Giriş Yap
                    </button>
                    <a href="{{ url('/panel/login') }}" class="flex-1 px-4 py-3 rounded-xl border border-slate-300 text-slate-700 font-medium hover:bg-slate-50 text-center transition-colors">
                        Panel sayfasına git
                    </a>
                </div>
            </form>
        </div>
    </div>
    @if(session('open_login_modal'))
    <script>document.addEventListener('DOMContentLoaded', function() { document.getElementById('login-modal').classList.remove('hidden'); });</script>
    @endif

    {{-- Bayilik başvurusu başarı modalı (kutlama + konfeti) --}}
    <div id="dealer-success-modal" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4" aria-modal="true" role="dialog">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="document.getElementById('dealer-success-modal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <div class="p-8 sm:p-10 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 flex items-center justify-center mb-6 ring-4 ring-emerald-50">
                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Tebrikler!</h2>
                <p class="text-slate-600 text-lg font-medium mb-1">Bayilik talebiniz alındı.</p>
                <p class="text-slate-500 text-sm mb-6">İnceleme sonrası sizinle iletişime geçilecektir.</p>
                <button type="button" onclick="document.getElementById('dealer-success-modal').classList.add('hidden')" class="px-6 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold shadow-sm hover:shadow transition-all">
                    Tamam
                </button>
            </div>
        </div>
    </div>
    @if(session('show_dealer_success_modal'))
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js" async></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('dealer-success-modal');
        if (!modal) return;
        modal.classList.remove('hidden');
        function runConfetti() {
            if (typeof confetti !== 'function') { setTimeout(runConfetti, 100); return; }
            confetti({ particleCount: 80, spread: 55, origin: { y: 0.6 }, colors: ['#155fb3', '#1f7bd6', '#059669', '#10b981', '#f59e0b'] });
            setTimeout(function() { confetti({ particleCount: 40, angle: 60, spread: 55, origin: { x: 0 } }); }, 150);
            setTimeout(function() { confetti({ particleCount: 40, angle: 120, spread: 55, origin: { x: 1 } }); }, 300);
        }
        setTimeout(runConfetti, 200);
    });
    </script>
    @endif

    {{-- Anasayfa üst kategori şeridi: Tüm Ürünler + Tişört, Bags, … mega menü --}}
    @isset($homeMegaNav)
        @include('store.partials.home-mega-nav-strip')
    @endisset

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
            <div class="rounded-xl bg-primary-50 border border-primary-200 text-primary-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
            <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
        </div>
    @endif
    @if(session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
            <div class="rounded-xl bg-sky-50 border border-sky-200 text-sky-800 px-4 py-3 text-sm font-medium">{{ session('info') }}</div>
        </div>
    @endif

    @yield('hero')

    @php
        $storeMainFullWidth = \Illuminate\Support\Facades\View::hasSection('store_content_full_width');
    @endphp
    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-8 lg:py-12 {{ $storeMainFullWidth ? '' : 'max-w-7xl mx-auto' }}">
        @yield('content')
    </main>

    {{-- Bottom navbar (sadece mobil) — app menüsü gibi ikonlu --}}
    <nav class="fixed bottom-0 left-0 right-0 z-40 lg:hidden bg-white border-t border-slate-200 shadow-[0_-8px_20px_-12px_rgba(0,0,0,0.25)] safe-area-pb">
        <div class="px-4 py-2">
            <div class="grid grid-cols-4 gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center justify-center py-2 rounded-xl text-slate-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors {{ request()->routeIs('home') && !request('category') ? 'bottom-nav-active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span class="text-[11px] font-medium mt-1">Ürünler</span>
                </a>

                <button type="button" onclick="document.getElementById('mobile-cats').classList.remove('hidden')" class="flex flex-col items-center justify-center py-2 rounded-xl text-slate-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z\"/></svg>
                    <span class="text-[11px] font-medium mt-1">Kategoriler</span>
                </button>

                <a href="{{ route('store.cart') }}" class="relative flex flex-col items-center justify-center py-2 rounded-xl text-slate-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors {{ request()->routeIs('store.cart') ? 'bottom-nav-active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z\"/></svg>
                    @if($headerCartCount > 0)
                        <span class="absolute top-1 right-4 min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-primary-500 text-white text-[10px] font-bold">{{ $headerCartCount > 99 ? '99+' : $headerCartCount }}</span>
                    @endif
                    <span class="text-[11px] font-medium mt-1">Sepet</span>
                </a>

                @auth
                    <a href="{{ auth()->user()->is_admin ? url('/admin') : url('/panel') }}" class="flex flex-col items-center justify-center py-2 rounded-xl text-slate-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\"/></svg>
                        <span class="text-[11px] font-medium mt-1">{{ auth()->user()->is_admin ? 'Admin' : 'Hesap' }}</span>
                    </a>
                @else
                    <button type="button" onclick="document.getElementById('login-modal').classList.remove('hidden')" class="flex flex-col items-center justify-center py-2 rounded-xl text-slate-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors w-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1\"/></svg>
                        <span class="text-[11px] font-medium mt-1">Giriş</span>
                    </button>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Mobil kategori sheet (bottom sheet) --}}
    <div id="mobile-cats" class="hidden lg:hidden fixed inset-0 z-50" aria-modal="true" role="dialog">
        <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('mobile-cats').classList.add('hidden')"></div>
        <div class="absolute left-0 right-0 bottom-0 bg-white rounded-t-2xl border-t border-slate-200 shadow-2xl max-h-[75vh] overflow-y-auto">
            <div class="px-5 pt-4 pb-2 flex items-center justify-between">
                <p class="font-semibold text-slate-900">Kategoriler</p>
                <button type="button" class="p-2 rounded-xl hover:bg-slate-100 text-slate-600" onclick="document.getElementById('mobile-cats').classList.add('hidden')" aria-label="Kapat">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12\"/></svg>
                </button>
            </div>
            <div class="px-5 pb-5">
                <a href="{{ route('home') }}" class="flex items-center gap-2 px-4 py-3 rounded-xl border border-slate-200 hover:bg-primary-50 hover:border-primary-200 transition-colors text-slate-800 font-medium">
                    <svg class="w-5 h-5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
                    Tüm Ürünler
                </a>
                @isset($menuCategories)
                    <div class="mt-2 space-y-1">
                        @foreach($menuCategories as $parent)
                            @if($parent->children->isNotEmpty())
                                <details class="group rounded-xl border border-slate-200 overflow-hidden">
                                    <summary class="flex items-center justify-between gap-2 px-4 py-3 cursor-pointer list-none font-medium text-slate-800 hover:bg-slate-50 transition-colors select-none">
                                        <span class="flex items-center gap-2">
                                            @include('store.partials.category-icon', ['category' => $parent])
                                            {{ $parent->name }}
                                        </span>
                                        <svg class="w-5 h-5 text-slate-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </summary>
                                    <div class="border-t border-slate-100 bg-slate-50/50">
                                        @foreach($parent->children as $child)
                                            <a href="{{ route('home', ['category' => $child->slug]) }}" class="flex items-center gap-2 px-4 py-2.5 pl-10 text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                @include('store.partials.category-icon', ['category' => $child])
                                                {{ $child->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </details>
                            @else
                                {{-- Alt kategorisi olmayan kök kategori: mobilde tek satır link --}}
                                <a href="{{ route('home', ['category' => $parent->slug]) }}" class="flex items-center gap-2 px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors text-slate-800 font-medium">
                                    @include('store.partials.category-icon', ['category' => $parent])
                                    {{ $parent->name }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                    <a href="{{ route('home') }}" class="mt-3 flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-primary-600 hover:bg-primary-50 transition-colors">
                        Tümünü gör
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endisset
            </div>
        </div>
    </div>

    @php
        $footerSetting = $footerSetting ?? \App\Models\FooterSetting::get();
        $footerCols = (int) ($footerSetting->columns ?? 4);
        $totalCols = ($footerSetting->show_brand ? 1 : 0) + $footerCols;
    @endphp
    <style>@media (min-width: 1024px) { .footer-grid { grid-template-columns: repeat(var(--footer-cols, 4), 1fr); } }</style>
    <footer class="mt-auto">
        <div class="bg-slate-900/95 text-slate-300">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-14">
                <div class="footer-grid grid grid-cols-1 sm:grid-cols-2 gap-8 lg:gap-10" style="--footer-cols: {{ min($totalCols, 6) }};">
                    @if($footerSetting->show_brand ?? true)
                    <div>
                        <a href="{{ route('home') }}" class="inline-flex items-center focus:outline-none focus:ring-2 focus:ring-primary-500/50 rounded-lg">
                            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-10 w-auto brightness-0 invert opacity-90 hover:opacity-100 transition-opacity">
                        </a>
                        <p class="mt-3 text-sm text-slate-400 leading-relaxed max-w-xs">B2B toptan ve perakende tekstil ürünleri. Güvenli alışveriş, havale / EFT ile ödeme.</p>
                        <div class="mt-4 inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-800/50 border border-slate-700/50">
                            <svg class="w-4 h-4 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span class="text-xs font-medium text-slate-300">Havale / EFT ile ödeme</span>
                        </div>
                    </div>
                    @endif

                    @foreach($footerMenuGroups ?? [] as $group)
                    <div>
                        <h3 class="text-xs font-semibold text-white uppercase tracking-widest mb-4">{{ $group->title }}</h3>
                        @if($group->type === \App\Models\FooterMenuGroup::TYPE_MENU)
                            <ul class="space-y-2.5 text-sm">
                                @foreach($group->items as $item)
                                <li>
                                    @if($item->url && $item->url !== '#')
                                        <a href="{{ $item->url }}" class="text-slate-400 hover:text-white transition-colors" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $item->label }}</a>
                                    @else
                                        <span class="text-slate-400">{{ $item->label }}</span>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        @elseif($group->type === \App\Models\FooterMenuGroup::TYPE_CATEGORIES)
                            <ul class="space-y-2.5 text-sm">
                                @isset($menuCategories)
                                @foreach($menuCategories as $parent)
                                    <li><a href="{{ route('home', ['category' => $parent->slug]) }}" class="text-slate-400 hover:text-white transition-colors">{{ $parent->name }}</a></li>
                                @endforeach
                                @endisset
                                <li><a href="{{ route('home') }}" class="text-slate-400 hover:text-primary-300 transition-colors font-medium">Tümünü gör →</a></li>
                            </ul>
                        @elseif($group->type === \App\Models\FooterMenuGroup::TYPE_BANK_INFO)
                            <div class="space-y-4 text-sm">
                                @forelse(($bankAccounts ?? []) as $account)
                                <div class="text-slate-400">
                                    <p class="font-medium text-slate-300">{{ $account->bank_name }}@if($account->branch) – {{ $account->branch }}@endif</p>
                                    <p class="mt-1 break-all">{{ $account->iban }}</p>
                                    <p class="mt-0.5">{{ $account->account_holder }}@if($account->currency) ({{ $account->currency }})@endif</p>
                                </div>
                                @empty
                                <p class="text-slate-500 text-xs">Banka hesabı eklenmemiş. Admin panelinden Banka Bilgileri’ne ekleyebilirsiniz.</p>
                                @endforelse
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-slate-800/80">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                        <p class="text-xs text-slate-500">&copy; {{ date('Y') }} {{ config('app.name') }}. Tüm hakları saklıdır.</p>
                        <div class="flex items-center gap-2 text-slate-500 text-xs">
                            <span class="hidden sm:inline">Güvenli ödeme</span>
                            <span class="px-2 py-1 rounded bg-slate-800/60 text-slate-400 font-medium">Havale / EFT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- Topbar kurlarını Merkez Bankası güncellemesine göre periyodik güncelle --}}
    <script>
    (function() {
        var rateEls = document.querySelectorAll('.topbar-rate');
        if (!rateEls.length) return;
        function formatRate(n) {
            return Number(n).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        function refreshRates() {
            fetch('{{ url("/api/exchange-rates") }}', { headers: { 'Accept': 'application/json' } })
                .then(function(r) { return r.json(); })
                .then(function(rates) {
                    Object.keys(rates).forEach(function(code) {
                        rateEls.forEach(function(el) {
                            if (el.getAttribute('data-currency') === code) {
                                el.textContent = formatRate(rates[code]);
                            }
                        });
                    });
                })
                .catch(function() {});
        }
        setInterval(refreshRates, 60000);
    })();
    </script>
    {{-- Header ürün arama: 3+ karakterde görsel + isim combobox --}}
    <script>
    (function() {
        var wrap = document.getElementById('header-search-wrap');
        var input = document.getElementById('header-search-input');
        var dropdown = document.getElementById('header-search-dropdown');
        var resultsEl = document.getElementById('header-search-results');
        var loadingEl = document.getElementById('header-search-loading');
        var emptyEl = document.getElementById('header-search-empty');
        if (!wrap || !input || !dropdown) return;
        var searchUrl = '{{ route("api.store.search-products") }}';
        var debounceTimer;
        function closeDropdown() {
            dropdown.classList.add('hidden');
        }
        function showDropdown() {
            dropdown.classList.remove('hidden');
        }
        function renderResults(products) {
            loadingEl.classList.add('hidden');
            emptyEl.classList.add('hidden');
            resultsEl.innerHTML = '';
            if (!products || products.length === 0) {
                emptyEl.classList.remove('hidden');
                return;
            }
            products.forEach(function(p) {
                var a = document.createElement('a');
                a.href = p.url;
                a.className = 'flex items-center gap-3 px-4 py-2.5 hover:bg-primary-50 transition-colors text-left';
                var img = p.image
                    ? '<img src="' + p.image + '" alt="" class="w-12 h-12 rounded-lg object-cover flex-shrink-0 bg-slate-100">'
                    : '<span class="w-12 h-12 rounded-lg bg-slate-200 flex items-center justify-center flex-shrink-0 text-slate-400 text-xl">📦</span>';
                a.innerHTML = img + '<span class="text-sm font-medium text-slate-800 line-clamp-2 flex-1 min-w-0">' + (p.name || '').replace(/</g, '&lt;') + '</span>';
                resultsEl.appendChild(a);
            });
        }
        function doSearch() {
            var q = (input.value || '').trim();
            if (q.length < 3) {
                closeDropdown();
                return;
            }
            showDropdown();
            loadingEl.classList.remove('hidden');
            emptyEl.classList.add('hidden');
            resultsEl.innerHTML = '';
            fetch(searchUrl + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    renderResults(data.products || []);
                })
                .catch(function() {
                    loadingEl.classList.add('hidden');
                    emptyEl.classList.remove('hidden');
                    resultsEl.innerHTML = '';
                });
        }
        input.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(doSearch, 300);
        });
        input.addEventListener('focus', function() {
            if ((input.value || '').trim().length >= 3) doSearch();
        });
        document.addEventListener('click', function(e) {
            if (wrap && !wrap.contains(e.target)) closeDropdown();
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>
