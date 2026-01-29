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

            @if($canSeePrices && $product->isOnSale() && ($product->stock_quantity === null || (int) $product->stock_quantity > 0))
                <form action="{{ route('store.cart.add') }}" method="POST" class="mt-8 p-6 rounded-2xl bg-slate-50 border border-slate-200">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    @if($product->variations->isNotEmpty())
                        <div class="space-y-4 mb-6" id="product-detail-vars">
                            @foreach($product->variations as $v)
                                @if(empty($v->depends_on))
                                <div class="var-row" data-var-name="{{ $v->name }}" data-var-type="{{ $v->type ?? 'select' }}">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $v->name }} <span class="text-red-500">*</span></label>
                                    @if(($v->type ?? '') === 'color')
                                    <div class="flex flex-wrap gap-2" role="group" aria-label="{{ $v->name }}">
                                        @foreach($v->options ?? [] as $opt)
                                            @php $meta = $v->getOptionMeta($opt); $hex = $meta['color'] ?? null; @endphp
                                            <label class="var-option-color cursor-pointer flex flex-col items-center gap-1 group/opt">
                                                <input type="radio" name="variations[{{ $v->name }}]" value="{{ $opt }}" class="sr-only peer var-select" required>
                                                <span class="w-10 h-10 rounded-full border-2 border-slate-300 peer-checked:border-primary-500 peer-checked:ring-2 peer-checked:ring-primary-500/30 group-hover/opt:border-primary-400 transition-all flex-shrink-0 {{ $hex ? '' : 'bg-slate-100' }}" @if($hex) style="background-color: {{ $hex }};" @endif title="{{ $opt }}"></span>
                                                <span class="text-xs text-slate-600">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @elseif(($v->type ?? '') === 'image')
                                    <div class="flex flex-wrap gap-3" role="group" aria-label="{{ $v->name }}">
                                        @foreach($v->options ?? [] as $opt)
                                            @php $meta = $v->getOptionMeta($opt); $imgPath = $meta['image'] ?? null; @endphp
                                            <label class="var-option-image cursor-pointer flex flex-col items-center gap-1.5 group/opt">
                                                <input type="radio" name="variations[{{ $v->name }}]" value="{{ $opt }}" class="sr-only peer var-select" required>
                                                <span class="w-14 h-14 rounded-xl border-2 border-slate-300 peer-checked:border-primary-500 peer-checked:ring-2 peer-checked:ring-primary-500/30 group-hover/opt:border-primary-400 transition-all overflow-hidden bg-slate-100 flex items-center justify-center flex-shrink-0">
                                                    @if($imgPath)
                                                        <img src="{{ Storage::url($imgPath) }}" alt="{{ $opt }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-slate-400 text-xs font-medium">{{ $opt }}</span>
                                                    @endif
                                                </span>
                                                <span class="text-xs text-slate-600">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @else
                                    <select name="variations[{{ $v->name }}]" required class="var-select w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                                        <option value="">Seçin</option>
                                        @foreach($v->options ?? [] as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                                @else
                                <div class="var-row var-dependent hidden" data-var-name="{{ $v->name }}" data-depends-on="{{ $v->depends_on }}" data-options-by-parent="{{ json_encode($v->options_by_parent ?? []) }}">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $v->name }} <span class="text-red-500">*</span></label>
                                    <select name="variations[{{ $v->name }}]" class="var-select var-dependent-select w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20" required disabled>
                                        <option value="">Önce üst seçimi yapın</option>
                                    </select>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @php $minOrder = $product->getMinimumOrderQuantity(); @endphp
                    <div class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Adet</label>
                            <input type="number" name="quantity" value="{{ $minOrder }}" min="{{ $minOrder }}" max="999" class="w-24 rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                            @if($minOrder > 1)
                                <p class="mt-1 text-xs text-slate-500">Minimum sipariş: {{ $minOrder }} adet</p>
                            @endif
                        </div>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-semibold shadow-sm hover:shadow transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Sepete Ekle
                        </button>
                    </div>
                </form>
            @elseif($canSeePrices && !$product->isOnSale())
                <div class="mt-8 p-6 rounded-2xl bg-slate-100 border border-slate-200">
                    <p class="font-medium text-slate-700">{{ $product->getStatusLabel() }}</p>
                    <p class="mt-1 text-sm text-slate-600">
                        @if($productStatus === 'stokta_yok')
                            Bu ürün şu an stokta bulunmuyor. Stok geldiğinde satışa sunulacaktır.
                        @else
                            Bu ürün yakında satışa sunulacaktır.
                        @endif
                    </p>
                </div>
            @elseif($canSeePrices && $product->stock_quantity !== null && (int) $product->stock_quantity === 0)
                <div class="mt-8 p-6 rounded-2xl bg-slate-100 border border-slate-200">
                    <p class="text-red-600 font-medium">Stokta yok</p>
                    <p class="mt-1 text-sm text-slate-600">Bu ürün şu an satışta değildir. Stok geldiğinde sepete ekleyebilirsiniz.</p>
                </div>
            @endif
            @endif
        </div>
    </div>

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
