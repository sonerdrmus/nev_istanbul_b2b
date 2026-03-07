@extends('store.layout')

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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
        {{-- Görsel --}}
        <div class="rounded-2xl overflow-hidden bg-slate-100 aspect-square max-h-[400px] lg:max-h-none lg:aspect-square flex items-center justify-center">
            @if($product->image)
                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            @else
                <span class="text-slate-300 text-8xl">📦</span>
            @endif
        </div>

        <div>
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

            @if($canSeePrices)
                @php
                    $productCurrency = $product->currency ?? \App\Models\Currency::getDefault();
                    $selectedCurrency = $selectedCurrency ?? \App\Models\Currency::getDefault();
                    $normalPriceTry = $product->getPriceInTRY($customerDiscountPercent ?? null);
                    $discountUnitTry = $product->getDiscountUnitPriceInTRY(1, $customerGroupId ?? 1);
                    $hasProductDiscount = $discountUnitTry !== null && $discountUnitTry < $normalPriceTry;
                    if ($hasProductDiscount) {
                        $discountedPriceTry = $discountUnitTry * (1 - ($customerDiscountPercent ?? 0) / 100);
                        $baseTry = $discountedPriceTry;
                        $discountPercentDisplay = $normalPriceTry > 0 ? round((($normalPriceTry - $baseTry) / $normalPriceTry) * 100) : 0;
                    } else {
                        $baseTry = $normalPriceTry;
                        $discountPercentDisplay = 0;
                    }
                    $baseConverted = $selectedCurrency->convertFromTRY($baseTry);
                    $optionPriceMap = [];
                    foreach ($product->variations as $v) {
                        foreach ($v->optionPrices ?? [] as $op) {
                            $delta = (float) $op->price_delta_try;
                            if (isset($customerDiscountPercent) && $customerDiscountPercent > 0) {
                                $delta = $delta * (1 - $customerDiscountPercent / 100);
                            }
                            $optionPriceMap[$v->name][(string) $op->option_value] = $delta;
                        }
                    }
                @endphp
                <div class="mt-4 {{ $hasProductDiscount ? 'p-4 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200/60' : '' }}">
                    @if($hasProductDiscount)
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-emerald-500 text-white shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                %{{ $discountPercentDisplay }} İndirim
                            </span>
                            <span class="text-sm text-emerald-700/90">Bu üründe indirim uygulanıyor</span>
                        </div>
                        <div class="flex flex-wrap items-baseline gap-3">
                            <span class="text-xl text-slate-400 line-through whitespace-nowrap" aria-hidden="true">
                                {{ $selectedCurrency->format($selectedCurrency->convertFromTRY($normalPriceTry)) }}
                            </span>
                            <p class="text-2xl font-bold text-slate-900 whitespace-nowrap">
                                <span id="product-price" data-base-try="{{ $baseTry }}" data-exchange-rate="{{ (float) $selectedCurrency->exchange_rate }}" data-currency-code="{{ $selectedCurrency->code }}" data-currency-symbol="{{ $selectedCurrency->symbol }}">
                                    {{ $selectedCurrency->format($baseConverted) }}
                                </span>
                            </p>
                        </div>
                    @else
                        <p class="text-2xl font-bold text-slate-900 whitespace-nowrap">
                            <span id="product-price" data-base-try="{{ $baseTry }}" data-exchange-rate="{{ (float) $selectedCurrency->exchange_rate }}" data-currency-code="{{ $selectedCurrency->code }}" data-currency-symbol="{{ $selectedCurrency->symbol }}">
                                {{ $selectedCurrency->format($baseConverted) }}
                            </span>
                        </p>
                    @endif
                </div>
                @if(!empty($customerDiscountPercent))
                    <p class="mt-1 text-sm text-slate-600">Müşteri indiriminiz: %{{ number_format($customerDiscountPercent, 0, ',', '.') }}</p>
                @endif
                <div id="price-delta-box" class="mt-2 hidden text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <span>Varyasyon farkı</span>
                        <span class="font-medium" id="price-delta-total"></span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500" id="price-delta-breakdown"></div>
                </div>
            @else
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
        </div>
    </div>

    {{-- Varyasyon seçimi ve sepete ekleme alanını ürün detayının altına taşıdık --}}
    @if($canSeePrices && $product->isOnSale() && ($product->stock_quantity === null || (int) $product->stock_quantity > 0))
    <section class="mt-10">
        <div class="max-w-4xl mx-auto">
            <div class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white/90 shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-primary-500 via-sky-500 to-emerald-500"></div>
                <div class="px-6 pt-5 pb-4 sm:px-8 sm:pt-6 sm:pb-5 border-b border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-primary-100 flex items-center justify-center text-primary-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM4 21v-1a7 7 0 017-7h2a7 7 0 017 7v1"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11h8M8 15h4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-semibold text-slate-900">Varyasyon Seçimi ve Sipariş</h2>
                        <p class="text-xs sm:text-sm text-slate-500">Beden / renk gibi seçenekleri belirleyip sipariş adedini seçin.</p>
                    </div>
                </div>
            <form action="{{ route('store.cart.add') }}" method="POST" class="px-6 pb-6 pt-5 sm:px-8 sm:pb-7 sm:pt-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if($product->variations->isNotEmpty())
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-start">
                        <div class="lg:col-span-2 space-y-4" id="product-detail-vars">
                            @foreach($product->variations as $v)
                                @if(empty($v->depends_on))
                                <div class="var-row rounded-2xl border border-slate-200/80 bg-slate-50/60 px-4 py-3.5 sm:px-5 sm:py-4" data-var-name="{{ $v->name }}" data-var-type="{{ $v->type ?? 'select' }}">
                                    <label class="block text-xs font-semibold text-slate-600 tracking-wide mb-1.5 uppercase">{{ $v->name }} <span class="text-red-500">*</span></label>
                                    @if(($v->type ?? '') === 'color')
                                    <div class="flex flex-wrap gap-2" role="group" aria-label="{{ $v->name }}">
                                        @foreach($v->options ?? [] as $opt)
                                            @php $meta = $v->getOptionMeta($opt); $hex = $meta['color'] ?? null; @endphp
                                            <label class="var-option-color cursor-pointer flex flex-col items-center gap-1 group/opt">
                                                <input type="radio" name="variations[{{ $v->name }}]" value="{{ $opt }}" class="sr-only peer var-select" required>
                                                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-slate-300/80 bg-white peer-checked:border-primary-500 peer-checked:ring-2 peer-checked:ring-primary-500/30 group-hover/opt:border-primary-400 transition-all flex-shrink-0 {{ $hex ? '' : 'bg-slate-100' }}" @if($hex) style="background-color: {{ $hex }};" @endif title="{{ $opt }}"></span>
                                                <span class="text-[11px] sm:text-xs text-slate-600">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @elseif(($v->type ?? '') === 'image')
                                    <div class="flex flex-wrap gap-3" role="group" aria-label="{{ $v->name }}">
                                        @foreach($v->options ?? [] as $opt)
                                            @php $meta = $v->getOptionMeta($opt); $imgPath = $meta['image'] ?? null; @endphp
                                            <label class="var-option-image cursor-pointer flex flex-col items-center gap-1.5 group/opt">
                                                <input type="radio" name="variations[{{ $v->name }}]" value="{{ $opt }}" class="sr-only peer var-select" required>
                                                <span class="w-16 h-16 rounded-2xl border-2 border-slate-200/90 bg-slate-50 overflow-hidden flex items-center justify-center flex-shrink-0 shadow-sm peer-checked:border-primary-500 peer-checked:ring-2 peer-checked:ring-primary-500/30 group-hover/opt:border-primary-400 transition-all">
                                                    @if($imgPath)
                                                        <img src="{{ Storage::url($imgPath) }}" alt="{{ $opt }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-slate-400 text-xs font-medium">{{ $opt }}</span>
                                                    @endif
                                                </span>
                                                <span class="text-[11px] sm:text-xs text-slate-600">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @else
                                    <select name="variations[{{ $v->name }}]" required class="var-select w-full rounded-2xl border border-slate-300/80 bg-white px-4 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/15 text-sm">
                                        <option value="">Seçin</option>
                                        @foreach($v->options ?? [] as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                                @else
                                <div class="var-row var-dependent rounded-2xl border border-dashed border-slate-200/80 bg-slate-50/40 px-4 py-3.5 sm:px-5 sm:py-4 hidden" data-var-name="{{ $v->name }}" data-depends-on="{{ $v->depends_on }}" data-options-by-parent="{{ json_encode($v->options_by_parent ?? []) }}">
                                    <label class="block text-xs font-semibold text-slate-600 tracking-wide mb-1.5 uppercase">{{ $v->name }} <span class="text-red-500">*</span></label>
                                    <select name="variations[{{ $v->name }}]" class="var-select var-dependent-select w-full rounded-2xl border border-slate-300/80 bg-white px-4 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/15 text-sm" required disabled>
                                        <option value="">Önce üst seçimi yapın</option>
                                    </select>
                                </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="lg:col-span-1 w-full rounded-2xl bg-slate-900 text-slate-50 px-4 py-4 sm:px-5 sm:py-5 flex flex-col gap-3 shadow-sm">
                            @php $minOrder = $product->getMinimumOrderQuantity(); @endphp
                            <div>
                                <p class="text-xs font-semibold tracking-wide text-slate-300 uppercase mb-1.5">Sipariş Özeti</p>
                                <p class="text-xs text-slate-400">Toplam tutar, yukarıdaki fiyat alanında seçtiğiniz varyasyonlara göre anlık güncellenir.</p>
                            </div>
                            <div class="flex items-end gap-3">
                                <div class="flex-1">
                                    <label class="block text-xs font-semibold text-slate-200 mb-1.5 uppercase">Adet</label>
                                    <input type="number" name="quantity" value="{{ $minOrder }}" min="{{ $minOrder }}" max="999" class="w-full rounded-xl border border-slate-600 bg-slate-900/60 px-3 py-2.5 text-slate-50 placeholder-slate-500 focus:border-primary-300 focus:ring-2 focus:ring-primary-400/40 text-sm">
                                    @if($minOrder > 1)
                                        <p class="mt-1 text-[11px] text-slate-400">Minimum sipariş: {{ $minOrder }} adet</p>
                                    @endif
                                </div>
                            </div>
                            <button type="submit" class="mt-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary-400 hover:bg-primary-300 text-slate-900 font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Sepete Ekle
                            </button>
                        </div>
                    </div>
                @endif

                
            </form>
            </div>
        </div>
    </section>
    @elseif($canSeePrices && !$product->isOnSale())
    <section class="mt-10">
        <div class="max-w-3xl mx-auto">
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
    <section class="mt-10">
        <div class="max-w-3xl mx-auto">
            <div class="p-6 rounded-2xl bg-slate-100 border border-slate-200">
                <p class="text-red-600 font-medium">Stokta yok</p>
                <p class="mt-1 text-sm text-slate-600">Bu ürün şu an satışta değildir. Stok geldiğinde sepete ekleyebilirsiniz.</p>
            </div>
        </div>
    </section>
    @endif

    @if($canSeePrices && $product->variations->isNotEmpty())
    @push('scripts')
    <script>
    (function() {
        var optionPriceMap = @json($optionPriceMap);
        var priceEl = document.getElementById('product-price');
        var deltaBox = document.getElementById('price-delta-box');
        var deltaTotalEl = document.getElementById('price-delta-total');
        var deltaBreakdownEl = document.getElementById('price-delta-breakdown');

        function fmt(amount, code, symbol) {
            // basit format (TR)
            return amount.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + symbol;
        }
        function convertFromTry(tryAmount) {
            var code = priceEl.getAttribute('data-currency-code');
            var rate = parseFloat(priceEl.getAttribute('data-exchange-rate') || '1');
            if (code === 'TRY' || !rate) return tryAmount;
            return tryAmount / rate;
        }
        function recalcPrice() {
            var baseTry = parseFloat(priceEl.getAttribute('data-base-try') || '0');
            var code = priceEl.getAttribute('data-currency-code');
            var symbol = priceEl.getAttribute('data-currency-symbol');

            var selections = {};
            document.querySelectorAll('#product-detail-vars select[name^="variations["], #product-detail-vars input[type=radio][name^="variations["]:checked').forEach(function(el) {
                var name = el.getAttribute('name') || '';
                var m = name.match(/^variations\[(.+)\]$/);
                if (!m) return;
                var vName = m[1];
                if (el.disabled) return;
                var val = el.tagName === 'SELECT' ? el.value : el.value;
                if (!val) return;
                selections[vName] = val;
            });

            var deltaTry = 0;
            var lines = [];
            Object.keys(selections).forEach(function(vName) {
                var opt = selections[vName];
                var d = optionPriceMap && optionPriceMap[vName] ? optionPriceMap[vName][opt] : 0;
                d = parseFloat(d || 0);
                if (d) {
                    deltaTry += d;
                    lines.push(vName + ' ' + opt + ': ' + (d > 0 ? '+' : '') + fmt(convertFromTry(d), code, symbol));
                }
            });

            var totalTry = baseTry + deltaTry;
            priceEl.textContent = fmt(convertFromTry(totalTry), code, symbol);

            if (deltaTry) {
                deltaBox.classList.remove('hidden');
                deltaTotalEl.textContent = (deltaTry > 0 ? '+' : '') + fmt(convertFromTry(deltaTry), code, symbol);
                deltaBreakdownEl.textContent = lines.join(' • ');
            } else {
                deltaBox.classList.add('hidden');
                deltaTotalEl.textContent = '';
                deltaBreakdownEl.textContent = '';
            }
        }

        var container = document.getElementById('product-detail-vars');
        if (!container) return;
        var dependentRows = container.querySelectorAll('.var-dependent');
        dependentRows.forEach(function(row) {
            var dependsOn = row.getAttribute('data-depends-on');
            var optionsByParent = {};
            try { optionsByParent = JSON.parse(row.getAttribute('data-options-by-parent') || '{}'); } catch (e) {}
            var dependentSelect = row.querySelector('.var-dependent-select');
            var parentRow = container.querySelector('.var-row[data-var-name="' + dependsOn.replace(/"/g, '\\"') + '"]');
            if (!parentRow || !dependentSelect) return;
            function updateDependent() {
                var parentEl = parentRow.querySelector('select.var-select') || parentRow.querySelector('.var-select:checked');
                var parentVal = parentEl ? parentEl.value : '';
                var opts = optionsByParent[parentVal];
                dependentSelect.innerHTML = '';
                if (!parentVal || !opts || !opts.length) {
                    row.classList.add('hidden');
                    dependentSelect.disabled = true;
                    var opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'Önce üst seçimi yapın';
                    dependentSelect.appendChild(opt);
                    return;
                }
                row.classList.remove('hidden');
                dependentSelect.disabled = false;
                var first = document.createElement('option');
                first.value = '';
                first.textContent = 'Seçin';
                dependentSelect.appendChild(first);
                opts.forEach(function(o) {
                    var opt = document.createElement('option');
                    opt.value = o;
                    opt.textContent = o;
                    dependentSelect.appendChild(opt);
                });
            }
            parentRow.querySelectorAll('.var-select').forEach(function(el) {
                el.addEventListener('change', updateDependent);
            });
            updateDependent();
        });

        container.querySelectorAll('select.var-select, input[type=radio].var-select').forEach(function(el) {
            el.addEventListener('change', recalcPrice);
        });
        recalcPrice();
    })();
    </script>
    @endpush
    @endif
@endsection
