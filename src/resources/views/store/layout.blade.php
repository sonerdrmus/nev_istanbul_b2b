<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) – {{ __('store.meta_suffix') }}</title>
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
        /* Genel yazı boyutu: tarayıcı varsayılanı 16px + 2px */
        html { font-size: 18px; }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-4 { display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; }
        .dropdown:hover .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        .dropdown-menu { opacity: 0; visibility: hidden; transform: translateY(-8px); transition: opacity 0.2s, transform 0.2s, visibility 0.2s; }
        /* Slider: 1024×278 banner oranı (~3.68:1); slaytlar üst üste, yalnızca aktif görünür */
        #hero-carousel {
            position: relative;
            width: 100%;
            aspect-ratio: 1024 / 278;
            min-height: 0;
        }
        #hero-carousel .carousel-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.1s ease;
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
                {{ __('store.topbar.free_shipping') }}
            </span>
            <div class="flex min-w-0 flex-wrap items-center justify-center gap-x-2 gap-y-1.5 sm:justify-end sm:gap-x-3">
                <div class="flex min-w-0 max-w-full flex-wrap items-center justify-center gap-x-1.5 gap-y-1 sm:justify-end" id="topbar-currencies">
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
                <span class="hidden sm:inline-block h-4 w-px shrink-0 bg-white/20" aria-hidden="true"></span>
                {{-- Dil: kompakt (bayrak + kod) — glob ikon yok, tek satır --}}
                <div class="relative inline-flex shrink-0" title="{{ __('store.header.locale_aria') }}">
                    <label for="store-locale-select" class="sr-only">{{ __('store.header.locale_aria') }}</label>
                    <select id="store-locale-select"
                            name="store_locale"
                            class="h-7 w-[5.25rem] shrink-0 cursor-pointer appearance-none rounded-md border border-white/20 bg-white/10 py-0 pl-1.5 pr-6 text-left text-[11px] font-semibold uppercase tracking-wide text-white shadow-none outline-none transition-colors hover:bg-white/15 focus-visible:ring-2 focus-visible:ring-white/40 sm:w-24 sm:text-xs [&>option]:bg-slate-900 [&>option]:font-normal [&>option]:normal-case [&>option]:text-white"
                            onchange="var u = this.options[this.selectedIndex].getAttribute('data-url'); if (u) window.location.href = u;">
                        @foreach (['tr' => '🇹🇷', 'en' => '🇬🇧', 'it' => '🇮🇹'] as $localeCode => $localeFlag)
                            <option value="{{ $localeCode }}"
                                    data-url="{{ route('locale.switch', ['locale' => $localeCode]) }}"
                                    @selected(app()->getLocale() === $localeCode)>{{ $localeFlag }} {{ __('store.header.lang_' . $localeCode) }}</option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute right-1.5 top-1/2 -translate-y-1/2 text-white/70" aria-hidden="true">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Header: logo + ürün arama + sağ aksiyonlar --}}
    <header class="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 sm:gap-4 h-14 sm:h-16">
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-10 sm:h-12 w-auto object-contain">
                </a>

                <div class="flex-1 min-w-0 max-w-xl mx-2 sm:mx-4 relative" id="header-search-wrap">
                    <form action="{{ route('home') }}" method="GET" class="relative">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <input type="search" name="q" id="header-search-input" value="{{ $searchQuery ?? '' }}" placeholder="{{ __('store.header.search_placeholder') }}" class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 text-slate-900 placeholder-slate-400 text-sm transition-colors" autocomplete="off">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded-lg text-slate-500 hover:text-primary-600 hover:bg-primary-50 transition-colors" aria-label="{{ __('store.header.search_aria') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </form>
                    <div id="header-search-dropdown" class="absolute left-0 right-0 top-full mt-1 bg-white rounded-xl border border-slate-200 shadow-xl max-h-[min(400px,70vh)] overflow-y-auto z-[100] hidden">
                        <div id="header-search-loading" class="p-4 text-center text-slate-500 text-sm hidden">{{ __('store.search.searching') }}</div>
                        <div id="header-search-results" class="py-1"></div>
                        <div id="header-search-empty" class="p-4 text-center text-slate-500 text-sm hidden">{{ __('store.search.no_results') }}</div>
                    </div>
                </div>

                {{-- Sağ aksiyonlar: Sepet + Hesap --}}
                <div class="ml-auto flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                    <a href="{{ route('store.cart') }}" class="relative flex items-center justify-center w-10 h-10 sm:w-auto sm:px-4 sm:py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="hidden sm:inline ml-1">{{ __('store.header.cart') }}</span>
                    @php $headerCartCount = collect(session('cart', []))->sum('quantity'); @endphp
                    @if($headerCartCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[20px] h-5 px-1.5 flex items-center justify-center rounded-full bg-primary-500 text-white text-xs font-bold">{{ $headerCartCount > 99 ? '99+' : $headerCartCount }}</span>
                    @endif
                    </a>

                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ url('/admin') }}" class="hidden sm:inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium text-primary-700 hover:bg-primary-50 transition-colors">{{ __('store.header.admin') }}</a>
                        @else
                            <a href="{{ url('/panel') }}" class="hidden sm:inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium text-primary-700 hover:bg-primary-50 transition-colors">{{ __('store.header.my_panel') }}</a>
                        @endif
                    @else
                        <button type="button" onclick="document.getElementById('login-modal').classList.remove('hidden')" class="hidden sm:inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium text-primary-700 hover:bg-primary-50 transition-colors">{{ __('store.header.login') }}</button>
                        <a href="{{ route('store.dealer-registration') }}" class="hidden sm:inline-flex items-center px-3 py-2 rounded-xl text-sm font-semibold bg-primary-600 hover:bg-primary-700 text-white transition-colors">
                            {{ __('store.header.register') }}
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
                        <h2 class="text-xl font-bold text-slate-900">{{ __('store.login_modal.title') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('store.login_modal.subtitle') }}</p>
                    </div>
                </div>
                <button type="button" class="p-2 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition-colors" onclick="document.getElementById('login-modal').classList.add('hidden')" aria-label="{{ __('store.login_modal.close') }}">
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
                        <label for="login-email" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('store.login_modal.email') }}</label>
                        <input id="login-email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('email') border-red-500 @enderror"
                            placeholder="ornek@email.com">
                    </div>
                    <div>
                        <label for="login-password" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('store.login_modal.password') }}</label>
                        <input id="login-password" name="password" type="password" required autocomplete="current-password"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('password') border-red-500 @enderror"
                            placeholder="••••••••">
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input name="remember" type="checkbox" value="1" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-600">{{ __('store.login_modal.remember') }}</span>
                    </label>
                </div>
                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold shadow-sm hover:shadow transition-all">
                        {{ __('store.login_modal.submit') }}
                    </button>
                    <a href="{{ url('/panel/login') }}" class="flex-1 px-4 py-3 rounded-xl border border-slate-300 text-slate-700 font-medium hover:bg-slate-50 text-center transition-colors">
                        {{ __('store.login_modal.panel_link') }}
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
                <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ __('store.dealer_success.title') }}</h2>
                <p class="text-slate-600 text-lg font-medium mb-1">{{ __('store.dealer_success.body') }}</p>
                <p class="text-slate-500 text-sm mb-6">{{ __('store.dealer_success.note') }}</p>
                <button type="button" onclick="document.getElementById('dealer-success-modal').classList.add('hidden')" class="px-6 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold shadow-sm hover:shadow transition-all">
                    {{ __('store.dealer_success.ok') }}
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

    @include('store.partials.welcome-production-info-modal')

    {{-- Anasayfa üst kategori şeridi: üst kategoriler + yazılı alt menü --}}
    @if(isset($topMenuCategories) && $topMenuCategories->isNotEmpty())
        @include('store.partials.home-mega-nav-strip')
    @endif

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

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        @yield('content')
    </main>

    {{-- Bottom navbar (sadece mobil) — app menüsü gibi ikonlu --}}
    <nav class="fixed bottom-0 left-0 right-0 z-40 lg:hidden bg-white border-t border-slate-200 shadow-[0_-8px_20px_-12px_rgba(0,0,0,0.25)] safe-area-pb">
        <div class="px-4 py-2">
            <div class="grid grid-cols-4 gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center justify-center py-2 rounded-xl text-slate-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors {{ request()->routeIs('home') && !request('category') ? 'bottom-nav-active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span class="text-[11px] font-medium mt-1">{{ __('store.bottom_nav.products') }}</span>
                </a>

                <button type="button" onclick="document.getElementById('mobile-cats').classList.remove('hidden')" class="flex flex-col items-center justify-center py-2 rounded-xl text-slate-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z\"/></svg>
                    <span class="text-[11px] font-medium mt-1">{{ __('store.bottom_nav.categories') }}</span>
                </button>

                <a href="{{ route('store.cart') }}" class="relative flex flex-col items-center justify-center py-2 rounded-xl text-slate-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors {{ request()->routeIs('store.cart') ? 'bottom-nav-active' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z\"/></svg>
                    @if($headerCartCount > 0)
                        <span class="absolute top-1 right-4 min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-primary-500 text-white text-[10px] font-bold">{{ $headerCartCount > 99 ? '99+' : $headerCartCount }}</span>
                    @endif
                    <span class="text-[11px] font-medium mt-1">{{ __('store.bottom_nav.cart') }}</span>
                </a>

                @auth
                    <a href="{{ auth()->user()->is_admin ? url('/admin') : url('/panel') }}" class="flex flex-col items-center justify-center py-2 rounded-xl text-slate-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\"/></svg>
                        <span class="text-[11px] font-medium mt-1">{{ auth()->user()->is_admin ? __('store.header.admin') : __('store.bottom_nav.account') }}</span>
                    </a>
                @else
                    <button type="button" onclick="document.getElementById('login-modal').classList.remove('hidden')" class="flex flex-col items-center justify-center py-2 rounded-xl text-slate-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors w-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1\"/></svg>
                        <span class="text-[11px] font-medium mt-1">{{ __('store.bottom_nav.login') }}</span>
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
                <p class="font-semibold text-slate-900">{{ __('store.mobile_cats.title') }}</p>
                <button type="button" class="p-2 rounded-xl hover:bg-slate-100 text-slate-600" onclick="document.getElementById('mobile-cats').classList.add('hidden')" aria-label="{{ __('store.mobile_cats.close') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12\"/></svg>
                </button>
            </div>
            <div class="px-5 pb-5">
                <a href="{{ route('home') }}" class="flex items-center gap-2 px-4 py-3 rounded-xl border border-slate-200 hover:bg-primary-50 hover:border-primary-200 transition-colors text-slate-800 font-medium">
                    <svg class="w-5 h-5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
                    {{ __('store.mobile_cats.all_products') }}
                </a>
                @isset($menuCategories)
                    <div class="mt-2 space-y-1">
                        @foreach($menuCategories as $parent)
                            @if($parent->children->isNotEmpty())
                                <details class="group rounded-xl border border-slate-200 overflow-hidden">
                                    <summary class="flex items-center justify-between gap-2 px-4 py-3 cursor-pointer list-none font-medium text-slate-800 hover:bg-slate-50 transition-colors select-none">
                                        <span class="flex items-center gap-2">
                                            @include('store.partials.category-icon', ['category' => $parent])
                                            {{ $parent->localized_name }}
                                        </span>
                                        <svg class="w-5 h-5 text-slate-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </summary>
                                    <div class="border-t border-slate-100 bg-slate-50/50">
                                        @foreach($parent->children as $child)
                                            <a href="{{ route('home', ['category' => $child->slug]) }}" class="flex items-center gap-2 px-4 py-2.5 pl-10 text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                @include('store.partials.category-icon', ['category' => $child])
                                                {{ $child->localized_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </details>
                            @else
                                {{-- Alt kategorisi olmayan kök kategori: mobilde tek satır link --}}
                                <a href="{{ route('home', ['category' => $parent->slug]) }}" class="flex items-center gap-2 px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors text-slate-800 font-medium">
                                    @include('store.partials.category-icon', ['category' => $parent])
                                    {{ $parent->localized_name }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                    <a href="{{ route('home') }}" class="mt-3 flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-primary-600 hover:bg-primary-50 transition-colors">
                        {{ __('store.mobile_cats.view_all') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endisset
            </div>
        </div>
    </div>

    @php
        $footerMenuGroups = $footerMenuGroups ?? collect();
        $companyTitles = ['Şirket', 'Company', 'Azienda'];
        $customerTitles = ['Müşteri Hizmetleri', 'Customer Service', 'Assistenza clienti'];
        $legalTitles = ['Sözleşmeler', 'Legal', 'Documenti legali'];
        $companyFooterGroup = $footerMenuGroups->first(fn ($group) => in_array($group->title, $companyTitles, true));
        $customerFooterGroup = $footerMenuGroups->first(fn ($group) => in_array($group->title, $customerTitles, true));
        $legalFooterGroup = $footerMenuGroups->first(fn ($group) => in_array($group->title, $legalTitles, true));
        $legalFooterChunks = $legalFooterGroup
            ? $legalFooterGroup->items->chunk(max(1, (int) ceil($legalFooterGroup->items->count() / 2)))
            : collect();
        $skipFooterGridTitles = array_merge($companyTitles, $customerTitles, $legalTitles);
        $gridFooterGroups = $footerMenuGroups->reject(function ($group) use ($skipFooterGridTitles) {
            return $group->type === \App\Models\FooterMenuGroup::TYPE_CATEGORIES
                || $group->type === \App\Models\FooterMenuGroup::TYPE_BANK_INFO
                || in_array($group->title, $skipFooterGridTitles, true);
        });
    @endphp
    <footer class="mt-auto">
        <div class="bg-slate-900/95 text-slate-300">
            <div class="w-full px-4 sm:px-6 lg:px-8 py-12 lg:py-14">
                <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800/70 via-slate-800/40 to-slate-900/30 p-5 sm:p-7 lg:p-8 shadow-[inset_0_1px_0_rgba(255,255,255,0.04)]">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-8 xl:gap-10">
                            <section class="xl:col-span-4" aria-labelledby="footer-company-heading">
                                <h3 id="footer-company-heading" class="sr-only">{{ \App\Support\CatalogLabelTranslator::label($companyFooterGroup->title ?? 'Şirket') }}</h3>
                                <a href="{{ route('home') }}" class="inline-flex items-center focus:outline-none focus:ring-2 focus:ring-primary-500/50 rounded-lg">
                                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-10 sm:h-11 w-auto brightness-0 invert opacity-90 hover:opacity-100 transition-opacity">
                                </a>
                                <p class="mt-3 text-sm font-semibold text-white leading-snug">{{ __('store.footer.company_name') }}</p>
                                <div class="mt-4 space-y-2.5 text-sm text-slate-400 leading-relaxed">
                                    <p class="flex gap-2.5">
                                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>{{ __('store.footer.company_address') }}</span>
                                    </p>
                                    <p class="flex gap-2.5">
                                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span>
                                            <a href="mailto:{{ __('store.footer.company_email') }}" class="hover:text-white transition-colors">{{ __('store.footer.company_email') }}</a>
                                            <span class="text-slate-600"> · </span>
                                            <a href="mailto:{{ __('store.footer.company_privacy_email') }}" class="hover:text-white transition-colors">{{ __('store.footer.company_privacy_email') }}</a>
                                        </span>
                                    </p>
                                    <p class="flex gap-2.5">
                                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-7 8h8a2 2 0 002-2V8.5a2 2 0 00-.586-1.414l-4.5-4.5A2 2 0 0012.5 2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <span>{{ __('store.footer.company_tax') }}</span>
                                    </p>
                                </div>
                                @if($companyFooterGroup && $companyFooterGroup->items->isNotEmpty())
                                    <ul class="mt-5 flex flex-wrap gap-2">
                                        @foreach($companyFooterGroup->items as $item)
                                            <li>
                                                @if($item->url && $item->url !== '#')
                                                    <a href="{{ $item->url }}" class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300 hover:bg-white/10 hover:text-white transition-colors" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ \App\Support\CatalogLabelTranslator::label($item->label) }}</a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </section>

                            @if($customerFooterGroup && $customerFooterGroup->items->isNotEmpty())
                            <section class="xl:col-span-3 xl:border-l xl:border-white/10 xl:pl-8" aria-labelledby="footer-customer-heading">
                                <h3 id="footer-customer-heading" class="text-xs font-semibold text-white uppercase tracking-widest">{{ \App\Support\CatalogLabelTranslator::label($customerFooterGroup->title) }}</h3>
                                <ul class="mt-4 space-y-1">
                                    @foreach($customerFooterGroup->items as $item)
                                        <li>
                                            @if($item->url && $item->url !== '#')
                                                <a href="{{ $item->url }}" class="group flex items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm text-slate-400 hover:bg-white/5 hover:text-white transition-colors" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>
                                                    <span>{{ \App\Support\CatalogLabelTranslator::label($item->label) }}</span>
                                                    <svg class="w-4 h-4 shrink-0 text-slate-600 group-hover:text-primary-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </a>
                                            @else
                                                <span class="flex items-center px-3 py-2 text-sm text-slate-400">{{ \App\Support\CatalogLabelTranslator::label($item->label) }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                            @endif

                            @if($legalFooterGroup && $legalFooterGroup->items->isNotEmpty())
                            <section class="md:col-span-2 xl:col-span-5 xl:border-l xl:border-white/10 xl:pl-8" aria-labelledby="footer-legal-heading">
                                <h3 id="footer-legal-heading" class="text-xs font-semibold text-white uppercase tracking-widest">{{ \App\Support\CatalogLabelTranslator::label($legalFooterGroup->title) }}</h3>
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($legalFooterChunks as $chunk)
                                        <ul class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3.5 space-y-2.5">
                                            @foreach($chunk as $item)
                                                <li>
                                                    @if($item->url && $item->url !== '#')
                                                        <a href="{{ $item->url }}" class="text-sm text-slate-400 hover:text-white transition-colors" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ \App\Support\CatalogLabelTranslator::label($item->label) }}</a>
                                                    @else
                                                        <span class="text-sm text-slate-400">{{ \App\Support\CatalogLabelTranslator::label($item->label) }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endforeach
                                </div>
                            </section>
                            @endif

                            @foreach($gridFooterGroups as $group)
                            <section class="xl:col-span-3 xl:border-l xl:border-white/10 xl:pl-8">
                                <h3 class="text-xs font-semibold text-white uppercase tracking-widest">{{ \App\Support\CatalogLabelTranslator::label($group->title) }}</h3>
                                @if($group->type === \App\Models\FooterMenuGroup::TYPE_MENU)
                                    <ul class="mt-4 space-y-2.5 text-sm">
                                        @foreach($group->items as $item)
                                        <li>
                                            @if($item->url && $item->url !== '#')
                                                <a href="{{ $item->url }}" class="text-slate-400 hover:text-white transition-colors" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ \App\Support\CatalogLabelTranslator::label($item->label) }}</a>
                                            @else
                                                <span class="text-slate-400">{{ \App\Support\CatalogLabelTranslator::label($item->label) }}</span>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </section>
                            @endforeach
                        </div>

                        <section class="mt-8 pt-8 border-t border-white/10" aria-labelledby="footer-bank-heading">
                            <h3 id="footer-bank-heading" class="text-xs font-semibold text-white uppercase tracking-widest">{{ __('store.footer.bank_heading') }}</h3>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-3">
                                @forelse(($bankAccounts ?? []) as $account)
                                    <article class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3.5 hover:border-primary-400/30 transition-colors">
                                        <div class="flex items-start justify-between gap-3">
                                            <p class="text-sm font-semibold text-white leading-snug">{{ $account->bank_name }}</p>
                                            @if($account->currency)
                                                <span class="shrink-0 rounded-full bg-primary-500/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-primary-200">{{ $account->currency }}</span>
                                            @endif
                                        </div>
                                        @if($account->branch)
                                            <p class="mt-1 text-[11px] text-slate-500">{{ $account->branch }}</p>
                                        @endif
                                        <p class="mt-2 font-mono text-[11px] sm:text-xs text-slate-300 break-all leading-relaxed">{{ $account->iban }}</p>
                                        <p class="mt-1.5 text-[11px] text-slate-500">{{ $account->account_holder }}</p>
                                    </article>
                                @empty
                                    <p class="text-slate-500 text-xs sm:col-span-2">{{ __('store.footer.bank_empty_note') }}</p>
                                @endforelse
                            </div>
                        </section>
                    </div>
            </div>

            <div class="border-t border-slate-800/80">
                <div class="w-full px-4 sm:px-6 lg:px-8 py-5">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                        <p class="text-xs text-slate-500">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('store.footer.copyright') }}</p>
                        <div class="flex items-center gap-2 text-slate-500 text-xs">
                            <span class="hidden sm:inline">{{ __('store.footer.secure_payment') }}</span>
                            <span class="px-2 py-1 rounded bg-slate-800/60 text-slate-400 font-medium">{{ __('store.footer.payment_label') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- Topbar kurlarını Merkez Bankası güncellemesine göre periyodik güncelle --}}
    @php
        $topbarRateLocaleTag = match (app()->getLocale()) {
            'tr' => 'tr-TR',
            'it' => 'it-IT',
            default => 'en-US',
        };
    @endphp
    <script>
    (function() {
        var rateEls = document.querySelectorAll('.topbar-rate');
        if (!rateEls.length) return;
        var rateLocaleTag = @json($topbarRateLocaleTag);
        function formatRate(n) {
            return Number(n).toLocaleString(rateLocaleTag, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
