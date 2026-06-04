@extends('store.layout')

@section('title', __('store.cart.title'))

@section('content')
    @php 
        $selectedCurrency = $selectedCurrency ?? \App\Models\Currency::getDefault();
        $totalSymbol = $selectedCurrency?->symbol ?? '₺';
    @endphp
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">{{ __('store.cart.title') }}</h1>
        <p class="mt-2 text-slate-600">{{ __('store.cart.subtitle') }}</p>
    </div>

    @if($cartItems->isEmpty())
        <div class="rounded-2xl bg-white border border-slate-200 p-12 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-3xl mb-4">🛒</div>
            <p class="text-slate-600 font-medium">{{ __('store.cart.empty') }}</p>
            <a href="{{ route('home') }}" class="inline-flex mt-4 px-5 py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-medium text-sm transition-colors">{{ __('store.cart.go_products') }}</a>
        </div>
    @else
        <form action="{{ route('store.cart.update') }}" method="POST">
            @csrf
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <div class="lg:col-span-8">
                    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                        <ul class="divide-y divide-slate-200">
                            @foreach($cartItems as $item)
                                @php $p = $item->product; @endphp
                                <li class="flex gap-4 p-5 sm:p-6">
                                    <div class="flex-shrink-0 w-24 h-24 rounded-xl bg-slate-100 overflow-hidden flex items-center justify-center">
                                        @if($p->image)
                                            <img src="{{ \App\Support\MediaUrl::public($p->image) }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-slate-400 text-2xl">📦</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-primary-600 font-medium">{{ $p->company?->name }}</p>
                                        <h2 class="font-semibold text-slate-900 truncate">{{ $p->name }}</h2>
                                        @if(!empty($item->variation_data) && is_array($item->variation_data))
                                            <ul class="mt-1.5 text-sm text-slate-600 space-y-0.5">
                                                @foreach($item->variation_data as $optName => $optValue)
                                                    @if($optName === 'product_customization')
                                                        @if($optValue === 'skipped')
                                                            <li><span class="font-medium text-slate-700">{{ __('store.product.customization_summary_section_label') }}:</span> {{ __('store.product.skip_customization') }}</li>
                                                        @endif
                                                        @continue
                                                    @endif
                                                    @if($optName === 'product_customization_notes')
                                                        @if(is_string($optValue) && trim($optValue) !== '')
                                                            <li><span class="font-medium text-slate-700">{{ __('store.product.customization_panel_title') }}:</span> {{ $optValue }}</li>
                                                        @endif
                                                        @continue
                                                    @endif
                                                    @if($optName === 'product_customization_table' && is_array($optValue))
                                                        @php
                                                            $custRows = $optValue['rows'] ?? (isset($optValue['row_id']) ? [$optValue] : []);
                                                        @endphp
                                                        @if(count($custRows) > 0)
                                                            <li>
                                                                <span class="font-medium text-slate-700">{{ __('store.product.customization_summary_section_label') }}</span>
                                                                <ul class="mt-0.5 ml-3 list-disc space-y-0.5 text-slate-600">
                                                                    @foreach($custRows as $crow)
                                                                        <li>
                                                                            {{ $crow['konum'] ?? '' }} — {{ $crow['en_boy_cm'] ?? '' }}
                                                                            @if(!empty($crow['alan_cm2_display']))
                                                                                · {{ __('store.product.customization_area_cm2', ['area' => $crow['alan_cm2_display']]) }}
                                                                            @elseif(!empty($crow['alan_m2_display']))
                                                                                · {{ __('store.product.customization_area_sqm', ['area' => $crow['alan_m2_display']]) }}
                                                                            @endif
                                                                            @if(!empty($crow['ebat']))
                                                                                · {{ __('store.product.customization_matched_ebat', ['ebat' => $crow['ebat']]) }}
                                                                            @endif
                                                                            @if(!empty($crow['renk_sayisi']))
                                                                                · {{ $crow['renk_sayisi'] }} {{ __('store.product.customization_colors_unit') }}
                                                                            @endif
                                                                            · {{ $crow['baski_teknigi'] ?? '' }}
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </li>
                                                        @endif
                                                    @else
                                                        @php
                                                            $varDisp = \App\Support\LabelTypeVariationDisplay::formatVariationValue($optValue);
                                                            $showVar = $varDisp !== null && $varDisp !== '';
                                                        @endphp
                                                        @if($showVar)
                                                            <li><span class="font-medium text-slate-700">{{ $optName }}:</span> {{ $varDisp }}</li>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                        @if(!empty($item->size_quantities) && is_array($item->size_quantities))
                                            @php $sizeParts = array_filter($item->size_quantities, fn($q) => (int)$q > 0); @endphp
                                            @if(count($sizeParts) > 0)
                                                <p class="mt-1 text-xs text-slate-500">{{ __('store.cart.size_breakdown') }} @foreach($sizeParts as $size => $qty){{ $size }}: {{ $qty }}@if(!$loop->last), @endif @endforeach</p>
                                            @endif
                                        @endif
                                        @php
    $unitTry = (float) ($item->unit_price_try ?? $item->subtotal / max(1, (int) $item->quantity));
    $unitConverted = $selectedCurrency->convertFromTRY($unitTry);
@endphp
                                        <p class="text-slate-500 text-sm mt-0.5 whitespace-nowrap">
                                            {{ $selectedCurrency->format($unitConverted) }} × <input type="number" name="items[{{ $loop->index }}][quantity]" value="{{ $item->quantity }}" min="{{ $p->getMinimumOrderQuantity() }}" class="w-16 rounded border border-slate-300 px-2 py-1 text-sm inline-block" />
                                        </p>
                                        <input type="hidden" name="items[{{ $loop->index }}][cart_key]" value="{{ $item->cart_key }}">
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        @php
    // $item->subtotal zaten TL cinsinden (getCartItems'da çevrildi)
    $convertedSubtotal = $selectedCurrency->convertFromTRY($item->subtotal);
@endphp
<p class="font-bold text-slate-900 whitespace-nowrap">{{ $selectedCurrency->format($convertedSubtotal) }}</p>
                                        <button type="button" onclick="if(confirm(@json(__('store.cart.remove_confirm')))) { document.getElementById('remove-{{ $item->cart_key }}').submit(); }" class="text-sm text-red-600 hover:text-red-700 font-medium">{{ __('store.cart.remove') }}</button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-medium text-sm transition-colors">{{ __('store.cart.update') }}</button>
                        <a href="{{ route('home') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-medium text-sm hover:bg-slate-50 transition-colors">{{ __('store.cart.continue_shopping') }}</a>
                    </div>
                </div>

                <div class="mt-8 lg:mt-0 lg:col-span-4">
                    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 sticky top-24">
                        <h2 class="text-lg font-semibold text-slate-900 mb-4">{{ __('store.cart.order_summary') }}</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-slate-600">
                                <span>{{ __('store.cart.items_count', ['count' => $cartCount]) }}</span>
@php
    // $cartTotal zaten TL cinsinden
    $convertedCartTotal = $selectedCurrency->convertFromTRY($cartTotal);
@endphp
                                <span class="whitespace-nowrap">{{ $selectedCurrency->format($convertedCartTotal) }}</span>
                            </div>
                            <div class="border-t border-slate-200 pt-3 mt-3 flex justify-between font-semibold text-slate-900 text-base">
                                <span>{{ __('store.cart.total') }}</span>
                                <span class="whitespace-nowrap">{{ $selectedCurrency->format($convertedCartTotal) }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-3">{{ __('store.cart.payment_line') }}</p>
                        <a href="{{ route('store.checkout') }}" class="mt-5 w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-semibold shadow-sm hover:shadow transition-all duration-200">
                            {{ __('store.cart.checkout') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        @foreach($cartItems as $item)
            <form id="remove-{{ $item->cart_key }}" action="{{ route('store.cart.remove', $item->cart_key) }}" method="POST" class="hidden">
                @csrf
            </form>
        @endforeach
    @endif
@endsection
