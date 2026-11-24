<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $pageTitle = trim((string) View::yieldContent('title', config('app.name')));
        $fullTitle = $pageTitle !== ''
            ? $pageTitle.' – '.__('store.meta_suffix')
            : (string) __('store.meta_suffix');
        $metaDescription = trim((string) View::yieldContent('meta_description', ''));
        if ($metaDescription === '') {
            $metaDescription = (string) __('store.meta_default_description');
        }
        $ogImage = trim((string) View::yieldContent('og_image', ''));
        if ($ogImage === '') {
            $ogImage = asset('images/logo.png');
        }
        $robotsMeta = trim((string) View::yieldContent('robots', 'index,follow'));
        $canonicalUrl = url()->current();
        $hreflangUrls = \App\Support\StoreLocale::alternateUrls();
    @endphp
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="{{ $robotsMeta }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @foreach($hreflangUrls as $hreflangLocale => $hreflangUrl)
        <link rel="alternate" hreflang="{{ $hreflangLocale }}" href="{{ $hreflangUrl }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $hreflangUrls[\App\Support\StoreLocale::default()] ?? $canonicalUrl }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ __('store.meta_suffix') }}">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    @include('store.partials.json-ld-organization')
    @stack('meta')
    <script>
        window.__nevAnalytics = {
            measurementId: 'G-CMZEX5E35S',
            consentKey: 'nev_cookie_consent',
            load: function () {
                if (window.__nevAnalyticsLoaded) return;
                window.__nevAnalyticsLoaded = true;
                var s = document.createElement('script');
                s.async = true;
                s.src = 'https://www.googletagmanager.com/gtag/js?id=' + this.measurementId;
                document.head.appendChild(s);
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                window.gtag = gtag;
                gtag('js', new Date());
                gtag('config', this.measurementId);
            }
        };
        try {
            if (localStorage.getItem(window.__nevAnalytics.consentKey) === 'accepted') {
                window.__nevAnalytics.load();
            }
        } catch (e) {}
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @php
        $mediaPreconnectHosts = [];
        foreach ([
            config('filesystems.disks.public.url'),
            config('filesystems.disks.s3.url'),
            \App\Models\StoreSetting::mediaCdnUrl(),
        ] as $mediaBase) {
            $mediaBase = trim((string) $mediaBase);
            if ($mediaBase === '') {
                continue;
            }
            $host = parse_url($mediaBase, PHP_URL_HOST);
            $scheme = parse_url($mediaBase, PHP_URL_SCHEME) ?: 'https';
            if (! is_string($host) || $host === '') {
                continue;
            }
            $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            if (is_string($appHost) && strcasecmp($host, $appHost) === 0) {
                continue;
            }
            $mediaPreconnectHosts[$scheme.'://'.$host] = true;
        }
    @endphp
    @foreach(array_keys($mediaPreconnectHosts) as $preconnectOrigin)
        <link rel="preconnect" href="{{ $preconnectOrigin }}" crossorigin>
        <link rel="dns-prefetch" href="{{ $preconnectOrigin }}">
    @endforeach
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body @class([
    'bg-slate-50 text-slate-900 min-h-screen antialiased flex flex-col',
    'h-dvh overflow-hidden pb-0' => View::hasSection('compact_chrome'),
    'pb-20 lg:pb-0' => ! View::hasSection('compact_chrome'),
])>
    <a href="#main-content" class="absolute left-4 top-4 z-[600] -translate-y-[160%] rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-primary-700 shadow-lg transition focus:translate-y-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
        {{ __('store.a11y.skip_to_content') }}
    </a>
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
                                    <!--<span class="topbar-rate" data-currency="{{ $curr->code }}">{{ $rateFormatted }}</span>-->
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
                                        <!--<span class="topbar-rate" data-currency="{{ $curr->code }}">{{ $rateFormatted }}</span>-->
                                    @endif
                                </a>
                            @endforeach
                        @endif
                    @endif
                </div>
                <span class="hidden sm:inline-block h-4 w-px shrink-0 bg-white/20" aria-hidden="true"></span>
                <a href="{{ asset(app()->getLocale() === 'en' ? 'docs/nevistanbul-b2b-user-guide-en.pdf' : 'docs/nevistanbul-b2b-user-guide.pdf') }}" target="_blank" rel="noopener" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-2.5 py-1 text-sm font-medium text-white transition-colors hover:bg-white/10">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 3v5a1 1 0 001 1h5"/></svg>
                    {{ __('store.topbar.user_guide') }}
                </a>
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
                                    data-url="{{ \App\Support\StoreLocale::alternateUrl($localeCode) }}"
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
                    <img src="{{ asset('images/logo.png') }}" alt="{{ __('store.meta_suffix') }}" class="h-10 sm:h-12 w-auto object-contain">
                </a>

                <div @class(['flex-1 min-w-0 max-w-xl mx-2 sm:mx-4 relative', 'hidden' => View::hasSection('compact_chrome')]) id="header-search-wrap">
                    <form action="{{ route('home') }}" method="GET" class="relative">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <label for="header-search-input" class="sr-only">{{ __('store.header.search_aria') }}</label>
                        <input type="search" name="q" id="header-search-input" value="{{ $searchQuery ?? '' }}" placeholder="{{ __('store.header.search_placeholder') }}" aria-label="{{ __('store.header.search_aria') }}" class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 text-slate-900 placeholder-slate-400 text-sm transition-colors" autocomplete="off">
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
                            <a href="{{ \Filament\Facades\Filament::getPanel('admin')->getUrl() }}" class="hidden sm:inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium text-primary-700 hover:bg-primary-50 transition-colors">{{ __('store.header.admin') }}</a>
                        @else
                            <a href="{{ route('store.account') }}" class="hidden sm:inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium text-primary-700 hover:bg-primary-50 transition-colors">{{ __('store.header.my_panel') }}</a>
                        @endif
                    @else
                        <a href="{{ route('store.login.show') }}" class="hidden sm:inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium text-primary-700 hover:bg-primary-50 transition-colors">{{ __('store.header.login') }}</a>
                        <a href="{{ route('store.dealer-registration') }}" class="hidden sm:inline-flex items-center px-3 py-2 rounded-xl text-sm font-semibold bg-primary-600 hover:bg-primary-700 text-white transition-colors">
                            {{ __('store.header.register') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Giriş Yap Modal (modern UI) --}}
    <div id="login-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" aria-modal="true" role="dialog" aria-labelledby="login-modal-title">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('login-modal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
            <div class="px-6 sm:px-8 pt-8 pb-2 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h2 id="login-modal-title" class="text-xl font-bold text-slate-900">{{ __('store.login_modal.title') }}</h2>
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
                    <a href="{{ route('store.dealer-registration') }}" class="flex-1 px-4 py-3 rounded-xl border border-slate-300 text-slate-700 font-medium hover:bg-slate-50 text-center transition-colors">
                        {{ __('store.login_modal.register_link') }}
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
    @if(! View::hasSection('compact_chrome') && isset($topMenuCategories) && $topMenuCategories->isNotEmpty())
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

    @hasSection('full_bleed')
        <div id="main-content" @class(['flex-1 w-full', 'min-h-0 overflow-hidden' => View::hasSection('compact_chrome')]) tabindex="-1">
            @yield('full_bleed')
        </div>
    @else
        @yield('hero')

        <main id="main-content" class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-8 lg:py-12" tabindex="-1">
            @yield('content')
        </main>
    @endif

    @unless(View::hasSection('compact_chrome'))
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
                    <a href="{{ auth()->user()->is_admin ? \Filament\Facades\Filament::getPanel('admin')->getUrl() : route('store.account') }}" class="flex flex-col items-center justify-center py-2 rounded-xl text-slate-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\"/></svg>
                        <span class="text-[11px] font-medium mt-1">{{ auth()->user()->is_admin ? __('store.header.admin') : __('store.bottom_nav.account') }}</span>
                    </a>
                @else
                    <a href="{{ route('store.login.show') }}" class="flex flex-col items-center justify-center py-2 rounded-xl text-slate-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors w-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1\"/></svg>
                        <span class="text-[11px] font-medium mt-1">{{ __('store.bottom_nav.login') }}</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>
    @endunless

    {{-- Mobil kategori sheet (bottom sheet) --}}
    <div id="mobile-cats" class="hidden lg:hidden fixed inset-0 z-50" aria-modal="true" role="dialog" aria-labelledby="mobile-cats-title">
        <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('mobile-cats').classList.add('hidden')"></div>
        <div class="absolute left-0 right-0 bottom-0 bg-white rounded-t-2xl border-t border-slate-200 shadow-2xl max-h-[75vh] overflow-y-auto">
            <div class="px-5 pt-4 pb-2 flex items-center justify-between">
                <h2 id="mobile-cats-title" class="font-semibold text-slate-900 text-base">{{ __('store.mobile_cats.title') }}</h2>
                <button type="button" class="p-2 rounded-xl hover:bg-slate-100 text-slate-600" onclick="document.getElementById('mobile-cats').classList.add('hidden')" aria-label="{{ __('store.mobile_cats.close') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12\"/></svg>
                </button>
            </div>
            <div class="px-5 pb-5">
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

    @unless(View::hasSection('compact_chrome'))
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
                                <h3 id="footer-company-heading" class="sr-only">{{ $companyFooterGroup?->localized_title ?? \App\Support\CatalogLabelTranslator::label('Şirket') }}</h3>
                                <a href="{{ route('home') }}" class="inline-flex items-center focus:outline-none focus:ring-2 focus:ring-primary-500/50 rounded-lg">
                                    <img src="{{ asset('images/logo.png') }}" alt="{{ __('store.meta_suffix') }}" class="h-10 sm:h-11 w-auto brightness-0 invert opacity-90 hover:opacity-100 transition-opacity">
                                </a>
                                <p class="mt-3 text-sm font-semibold text-white leading-snug">{{ __('store.footer.company_name') }}</p>
                                <div class="mt-4 space-y-2.5 text-sm text-slate-400 leading-relaxed">
                                    <p class="flex gap-2.5">
                                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>{{ __('store.footer.company_address') }}</span>
                                    </p>
                                    @php
                                        $footerPhoneDisplay = trim((string) config('store.whatsapp.number', __('store.footer.company_phone')));
                                        $footerPhoneDigits = preg_replace('/\D+/', '', $footerPhoneDisplay) ?: '';
                                        if (str_starts_with($footerPhoneDigits, '0') && strlen($footerPhoneDigits) === 11) {
                                            $footerPhoneDigits = '90'.substr($footerPhoneDigits, 1);
                                        }
                                    @endphp
                                    @if($footerPhoneDisplay !== '')
                                    <p class="flex gap-2.5">
                                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span>
                                            <a href="tel:+{{ $footerPhoneDigits }}" class="hover:text-white transition-colors">{{ $footerPhoneDisplay }}</a>
                                        </span>
                                    </p>
                                    @endif
                                    @php
                                        $socialLinks = array_filter([
                                            'instagram' => trim((string) config('store.social.instagram', '')),
                                            'linkedin' => trim((string) config('store.social.linkedin', '')),
                                            'facebook' => trim((string) config('store.social.facebook', '')),
                                        ]);
                                    @endphp
                                    @if($socialLinks !== [])
                                    <div class="flex flex-wrap items-center gap-2 pt-1" aria-label="{{ __('store.footer.social_aria') }}">
                                        @foreach($socialLinks as $network => $socialUrl)
                                            <a href="{{ $socialUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white transition-colors" aria-label="{{ __('store.footer.social_'.$network) }}">
                                                @if($network === 'instagram')
                                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8A3.6 3.6 0 0 0 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6A3.6 3.6 0 0 0 16.4 4H7.6m9.65 1.5a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10m0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
                                                @elseif($network === 'linkedin')
                                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.98 3.5C4.98 4.88 3.88 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.2 8.5h4.6V24H.2V8.5zM8.34 8.5h4.41v2.12h.06c.61-1.16 2.12-2.38 4.36-2.38 4.66 0 5.52 3.07 5.52 7.06V24h-4.6v-6.63c0-1.58-.03-3.61-2.2-3.61-2.2 0-2.54 1.72-2.54 3.5V24h-4.6V8.5z"/></svg>
                                                @else
                                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12.07C22 6.48 17.52 2 11.93 2S1.86 6.48 1.86 12.07c0 5.02 3.66 9.18 8.44 9.93v-7.02H7.9v-2.91h2.4V9.84c0-2.37 1.41-3.68 3.57-3.68 1.03 0 2.11.18 2.11.18v2.32h-1.19c-1.17 0-1.54.73-1.54 1.48v1.78h2.62l-.42 2.91h-2.2V22c4.78-.75 8.44-4.91 8.44-9.93z"/></svg>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                    @endif
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
                                                    <a href="{{ $item->storefrontUrl() }}" class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300 hover:bg-white/10 hover:text-white transition-colors" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $item->localized_label }}</a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </section>

                            @if($customerFooterGroup && $customerFooterGroup->items->isNotEmpty())
                            <section class="xl:col-span-3 xl:border-l xl:border-white/10 xl:pl-8" aria-labelledby="footer-customer-heading">
                                <h3 id="footer-customer-heading" class="text-xs font-semibold text-white uppercase tracking-widest">{{ $customerFooterGroup->localized_title }}</h3>
                                <ul class="mt-4 space-y-1">
                                    @foreach($customerFooterGroup->items as $item)
                                        <li>
                                            @if($item->url && $item->url !== '#')
                                                <a href="{{ $item->storefrontUrl() }}" class="group flex items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm text-slate-400 hover:bg-white/5 hover:text-white transition-colors" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>
                                                    <span>{{ $item->localized_label }}</span>
                                                    <svg class="w-4 h-4 shrink-0 text-slate-600 group-hover:text-primary-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </a>
                                            @else
                                                <span class="flex items-center px-3 py-2 text-sm text-slate-400">{{ $item->localized_label }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                            @endif

                            @if($legalFooterGroup && $legalFooterGroup->items->isNotEmpty())
                            <section class="md:col-span-2 xl:col-span-5 xl:border-l xl:border-white/10 xl:pl-8" aria-labelledby="footer-legal-heading">
                                <h3 id="footer-legal-heading" class="text-xs font-semibold text-white uppercase tracking-widest">{{ $legalFooterGroup->localized_title }}</h3>
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($legalFooterChunks as $chunk)
                                        <ul class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3.5 space-y-2.5">
                                            @foreach($chunk as $item)
                                                <li>
                                                    @if($item->url && $item->url !== '#')
                                                        <a href="{{ $item->storefrontUrl() }}" class="text-sm text-slate-400 hover:text-white transition-colors" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $item->localized_label }}</a>
                                                    @else
                                                        <span class="text-sm text-slate-400">{{ $item->localized_label }}</span>
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
                                <h3 class="text-xs font-semibold text-white uppercase tracking-widest">{{ $group->localized_title }}</h3>
                                @if($group->type === \App\Models\FooterMenuGroup::TYPE_MENU)
                                    <ul class="mt-4 space-y-2.5 text-sm">
                                        @foreach($group->items as $item)
                                        <li>
                                            @if($item->url && $item->url !== '#')
                                                <a href="{{ $item->storefrontUrl() }}" class="text-slate-400 hover:text-white transition-colors" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $item->localized_label }}</a>
                                            @else
                                                <span class="text-slate-400">{{ $item->localized_label }}</span>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </section>
                            @endforeach
                        </div>

                        @if(($bankAccounts ?? collect())->isNotEmpty())
                        <section class="mt-8 pt-8 border-t border-white/10" aria-labelledby="footer-bank-heading">
                            <h3 id="footer-bank-heading" class="text-xs font-semibold text-white uppercase tracking-widest">{{ __('store.footer.bank_heading') }}</h3>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-3">
                                @foreach($bankAccounts as $account)
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
                                @endforeach
                            </div>
                        </section>
                        @endif
                    </div>
            </div>

            <div class="border-t border-slate-800/80">
                <div class="w-full px-4 sm:px-6 lg:px-8 py-5">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                        <p class="text-xs text-slate-500">&copy; {{ date('Y') }} {{ __('store.meta_suffix') }}. {{ __('store.footer.copyright') }}</p>
                        <div class="flex items-center gap-2 text-slate-500 text-xs">
                            <span class="hidden sm:inline">{{ __('store.footer.secure_payment') }}</span>
                            <span class="px-2 py-1 rounded bg-slate-800/60 text-slate-400 font-medium">{{ __('store.footer.payment_label') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    @endunless

    {{-- Topbar kurları: sunucu cache TTL’ine yakın, sekme görünürken yenile --}}
    @php
        $topbarRateLocaleTag = match (app()->getLocale()) {
            'tr' => 'tr-TR',
            'it' => 'it-IT',
            default => 'en-US',
        };
        $exchangePollMs = max(300_000, (int) config('services.tcmb.cache_ttl_seconds', 900) * 1000);
    @endphp
    <script>
    (function() {
        var rateEls = document.querySelectorAll('.topbar-rate');
        if (!rateEls.length) return;
        var rateLocaleTag = @json($topbarRateLocaleTag);
        var pollMs = {{ (int) $exchangePollMs }};
        var timer = null;
        function formatRate(n) {
            return Number(n).toLocaleString(rateLocaleTag, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        function refreshRates() {
            if (document.hidden) return;
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
        function startPoll() {
            if (timer) return;
            timer = setInterval(refreshRates, pollMs);
        }
        function stopPoll() {
            if (!timer) return;
            clearInterval(timer);
            timer = null;
        }
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopPoll();
            } else {
                refreshRates();
                startPoll();
            }
        });
        startPoll();
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
    <script>
        document.querySelectorAll('.store-overlay').forEach(function (el) {
            if (el.parentElement !== document.body) {
                document.body.appendChild(el);
            }
        });
    </script>

    @php
        $whatsappEnabled = (bool) config('store.whatsapp.enabled', true);
        $whatsappDisplay = trim((string) config('store.whatsapp.number', ''));
        $whatsappDigits = preg_replace('/\D+/', '', $whatsappDisplay) ?: '';
        if (str_starts_with($whatsappDigits, '0') && strlen($whatsappDigits) === 11) {
            $whatsappDigits = '90' . substr($whatsappDigits, 1);
        }
    @endphp
    @if($whatsappEnabled && $whatsappDigits !== '')
        <a
            href="https://wa.me/{{ $whatsappDigits }}"
            target="_blank"
            rel="noopener noreferrer"
            class="fixed z-[450] right-5 bottom-24 lg:right-8 lg:bottom-8 block h-14 w-14 shrink-0 overflow-hidden rounded-full shadow-[0_10px_28px_-6px_rgba(37,211,102,0.65)] transition hover:scale-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#25D366] focus-visible:ring-offset-2"
            aria-label="{{ __('store.whatsapp.aria') }} ({{ $whatsappDisplay }})"
            title="{{ __('store.whatsapp.label') }}"
        >
            <img
                src="{{ asset('images/whatsapp-fab.svg') }}"
                alt=""
                width="56"
                height="56"
                class="block h-full w-full"
                decoding="async"
            >
            <span class="sr-only">{{ __('store.whatsapp.label') }}</span>
        </a>
    @endif

    <div id="cookie-consent-banner" class="fixed inset-x-0 bottom-0 z-[500] hidden border-t border-slate-200 bg-white/95 p-4 shadow-[0_-12px_40px_-20px_rgba(15,23,42,0.35)] backdrop-blur sm:p-5" role="dialog" aria-live="polite" aria-label="{{ __('store.cookie.title') }}">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-900">{{ __('store.cookie.title') }}</p>
                <p class="mt-1 text-sm leading-relaxed text-slate-600">
                    {{ __('store.cookie.body') }}
                    <a href="{{ route('store.legal.show', \App\Models\LegalPage::COOKIE_SLUG) }}" class="font-medium text-primary-700 underline underline-offset-2 hover:text-primary-800">{{ __('store.cookie.policy_link') }}</a>
                </p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-2">
                <button type="button" id="cookie-consent-reject" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ __('store.cookie.reject') }}</button>
                <button type="button" id="cookie-consent-accept" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">{{ __('store.cookie.accept') }}</button>
            </div>
        </div>
    </div>
    <script>
    (function () {
        var key = (window.__nevAnalytics && window.__nevAnalytics.consentKey) || 'nev_cookie_consent';
        var banner = document.getElementById('cookie-consent-banner');
        if (!banner) return;
        function setConsent(value) {
            try { localStorage.setItem(key, value); } catch (e) {}
            banner.classList.add('hidden');
            document.dispatchEvent(new CustomEvent('nev-cookie-consent', { detail: { value: value } }));
            if (value === 'accepted' && window.__nevAnalytics) window.__nevAnalytics.load();
        }
        try {
            var existing = localStorage.getItem(key);
            if (existing === 'accepted' || existing === 'rejected') return;
        } catch (e) {}
        banner.classList.remove('hidden');
        var accept = document.getElementById('cookie-consent-accept');
        var reject = document.getElementById('cookie-consent-reject');
        if (accept) accept.addEventListener('click', function () { setConsent('accepted'); });
        if (reject) reject.addEventListener('click', function () { setConsent('rejected'); });
    })();
    </script>
</body>
</html>
