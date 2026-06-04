@extends('store.layout')

@section('title', __('store.checkout.title'))

@section('content')
    @php 
        $selectedCurrency = $selectedCurrency ?? \App\Models\Currency::getDefault();
        $totalSymbol = $selectedCurrency?->symbol ?? '₺';
    @endphp
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">{{ __('store.checkout.title') }}</h1>
        <p class="mt-2 text-slate-600">{{ __('store.checkout.subtitle') }}</p>
    </div>

    <form action="{{ route('store.place-order') }}" method="POST" class="lg:grid lg:grid-cols-12 lg:gap-8">
        @csrf
        <div class="lg:col-span-7">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 sm:p-8">
                <h2 class="text-lg font-semibold text-slate-900 mb-6">{{ __('store.checkout.section_contact') }}</h2>
                <div class="space-y-5">
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('store.checkout.label_name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('customer_name') border-red-500 @enderror"
                            placeholder="{{ __('store.checkout.placeholder_name') }}">
                        @error('customer_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('store.checkout.label_email') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}" required
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('customer_email') border-red-500 @enderror"
                            placeholder="email@example.com">
                        @error('customer_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('store.checkout.label_phone') }}</label>
                        <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('customer_phone') border-red-500 @enderror"
                            placeholder="{{ __('store.checkout.placeholder_phone') }}">
                        @error('customer_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="customer_address" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('store.checkout.label_address') }}</label>
                        <textarea name="customer_address" id="customer_address" rows="3"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('customer_address') border-red-500 @enderror"
                            placeholder="{{ __('store.checkout.placeholder_address') }}">{{ old('customer_address') }}</textarea>
                        @error('customer_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('store.checkout.label_order_note') }}</label>
                        <textarea name="notes" id="notes" rows="2"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('notes') border-red-500 @enderror"
                            placeholder="{{ __('store.checkout.placeholder_order_note') }}">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    @if(isset($shippingMethods) && $shippingMethods->isNotEmpty())
                        <div class="pt-4 border-t border-slate-200">
                            <h3 class="text-sm font-semibold text-slate-800 mb-3">{{ __('store.checkout.shipping_choice') }}</h3>
                            <div class="space-y-3">
                                @foreach($shippingMethods as $method)
                                    @php
                                        $cost = $method->getCostForCartTotal($cartTotal);
                                        $costConv = $selectedCurrency->convertFromTRY($cost);
                                    @endphp
                                    <label class="flex items-start gap-3 p-4 rounded-xl border-2 border-slate-200 hover:border-primary-300 cursor-pointer transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50">
                                        <input type="radio" name="shipping_method_id" value="{{ $method->id }}" {{ old('shipping_method_id', $shippingMethods->first()?->id) == $method->id ? 'checked' : '' }}
                                            class="mt-1 rounded-full border-slate-300 text-primary-600 focus:ring-primary-500"
                                            data-cost-try="{{ $cost }}"
                                            data-cost-formatted="{{ $cost == 0 ? __('store.checkout.free') : $selectedCurrency->format($costConv) }}">
                                        <div class="flex-1 min-w-0">
                                            <span class="font-medium text-slate-900">{{ $method->name }}</span>
                                            @if($method->estimated_days)
                                                <span class="text-slate-500 text-sm ml-1">({{ $method->estimated_days }})</span>
                                            @endif
                                            @if($method->description)
                                                <p class="text-sm text-slate-500 mt-0.5">{{ $method->description }}</p>
                                            @endif
                                        </div>
                                        <span class="font-semibold text-slate-900 whitespace-nowrap" data-cost-label>
                                            {{ $cost == 0 ? __('store.checkout.free') : $selectedCurrency->format($costConv) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('shipping_method_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @endif
                    @if(isset($bankAccounts) && $bankAccounts->isNotEmpty())
                        <div class="pt-4 border-t border-slate-200">
                            <h3 class="text-sm font-semibold text-slate-800 mb-3">{{ __('store.checkout.bank_section') }}</h3>
                            <p class="text-xs text-slate-500 mb-3">{{ __('store.checkout.bank_help') }}</p>
                            <div class="space-y-3">
                                @foreach($bankAccounts as $bank)
                                    <label class="flex items-start gap-3 p-4 rounded-xl border-2 border-slate-200 hover:border-primary-300 cursor-pointer transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50">
                                        <input type="radio" name="bank_account_id" value="{{ $bank->id }}" {{ old('bank_account_id', $bankAccounts->first()?->id) == $bank->id ? 'checked' : '' }}
                                            class="mt-1 rounded-full border-slate-300 text-primary-600 focus:ring-primary-500">
                                        <div class="flex-1 min-w-0">
                                            <span class="font-medium text-slate-900">{{ $bank->bank_name }}</span>
                                            @if($bank->branch)
                                                <span class="text-slate-500 text-sm block">{{ __('store.checkout.branch') }}: {{ $bank->branch }}</span>
                                            @endif
                                            @if($bank->iban)<span class="text-slate-500 text-sm block">{{ __('store.checkout.iban') }}: …{{ substr($bank->iban, -8) }}</span>@endif
                                            @if($bank->account_holder)
                                                <span class="text-slate-500 text-sm block">{{ __('store.checkout.account_holder') }}: {{ $bank->account_holder }}</span>
                                            @endif
                                            @if($bank->currency)
                                                <span class="text-slate-500 text-sm">{{ $bank->currency }}</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('bank_account_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @endif
                </div>
                <input type="hidden" name="payment_method" value="havale">
            </div>
        </div>

        <div class="mt-8 lg:mt-0 lg:col-span-5">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 sticky top-24">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">{{ __('store.checkout.summary') }}</h2>
                <ul class="space-y-3 divide-y divide-slate-100">
                    @foreach($cartItems as $item)
                        <li class="pt-3 first:pt-0">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600">{{ $item->product->name }} × {{ $item->quantity }}</span>
                                @php
    $convertedSubtotal = $selectedCurrency->convertFromTRY($item->subtotal);
@endphp
<span class="font-medium text-slate-900 whitespace-nowrap">{{ $selectedCurrency->format($convertedSubtotal) }}</span>
                            </div>
                            @if(!empty($item->variation_data) && is_array($item->variation_data))
                                <ul class="mt-1 text-xs text-slate-500 space-y-0.5">
                                    @foreach($item->variation_data as $optName => $optValue)
                                        @if($optName === 'product_customization')
                                            @if($optValue === 'skipped')
                                                <li><span class="font-medium text-slate-600">{{ __('store.product.customization_summary_section_label') }}:</span> {{ __('store.product.skip_customization') }}</li>
                                            @endif
                                            @continue
                                        @endif
                                        @if($optName === 'product_customization_notes')
                                            @if(is_string($optValue) && trim($optValue) !== '')
                                                <li><span class="font-medium text-slate-600">{{ __('store.product.customization_panel_title') }}:</span> {{ $optValue }}</li>
                                            @endif
                                            @continue
                                        @endif
                                        @if($optName === 'product_customization_table' && is_array($optValue))
                                            @php
                                                $custRowsChk = $optValue['rows'] ?? (isset($optValue['row_id']) ? [$optValue] : []);
                                            @endphp
                                            @if(count($custRowsChk) > 0)
                                                <li>
                                                    <span class="font-medium text-slate-600">{{ __('store.product.customization_summary_section_label') }}</span>
                                                    <ul class="mt-0.5 ml-3 list-disc space-y-0.5">
                                                        @foreach($custRowsChk as $crow)
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
                                                $varDispChk = \App\Support\LabelTypeVariationDisplay::formatVariationValue($optValue);
                                                $showVarChk = $varDispChk !== null && $varDispChk !== '';
                                            @endphp
                                            @if($showVarChk)
                                                <li><span class="font-medium text-slate-600">{{ $optName }}:</span> {{ $varDispChk }}</li>
                                            @endif
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                            @if(!empty($item->size_quantities) && is_array($item->size_quantities))
                                @php $sizeParts = array_filter($item->size_quantities, fn($q) => (int)$q > 0); @endphp
                                @if(count($sizeParts) > 0)
                                    <p class="mt-0.5 text-xs text-slate-500">{{ __('store.checkout.sizes_label') }} @foreach($sizeParts as $size => $qty){{ $size }}: {{ $qty }}@if(!$loop->last), @endif @endforeach</p>
                                @endif
                            @endif
                        </li>
                    @endforeach
                </ul>
                @php
                    $defaultShippingCost = isset($shippingMethods) && $shippingMethods->isNotEmpty() ? $shippingMethods->first()->getCostForCartTotal($cartTotal) : 0;
                    $orderTotal = $cartTotal + $defaultShippingCost;
                @endphp
                <div class="mt-4 pt-4 border-t border-slate-200 space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>{{ __('store.checkout.subtotal') }}</span>
                        <span id="checkout-subtotal" class="whitespace-nowrap">{{ $selectedCurrency->format($selectedCurrency->convertFromTRY($cartTotal)) }}</span>
                    </div>
                    @if(isset($shippingMethods) && $shippingMethods->isNotEmpty())
                        <div class="flex justify-between text-slate-600">
                            <span>{{ __('store.checkout.shipping') }}</span>
                            <span id="checkout-shipping" class="whitespace-nowrap">{{ $defaultShippingCost == 0 ? __('store.checkout.free') : $selectedCurrency->format($selectedCurrency->convertFromTRY($defaultShippingCost)) }}</span>
                        </div>
                    @endif
                </div>
                <div class="mt-3 pt-3 border-t border-slate-200 flex justify-between font-semibold text-slate-900 text-lg">
                    <span>{{ __('store.checkout.total') }}</span>
                    <span id="checkout-total" class="whitespace-nowrap">{{ $selectedCurrency->format($selectedCurrency->convertFromTRY($orderTotal)) }}</span>
                </div>

                <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <p class="text-sm font-medium text-slate-700 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        {{ __('store.checkout.payment_line') }}
                    </p>
                    <p class="text-xs text-slate-500 mt-1">{{ __('store.checkout.payment_email_note') }}</p>
                </div>

                <button type="submit" class="mt-6 w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-semibold shadow-sm hover:shadow transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    {{ __('store.checkout.place_order') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
                <a href="{{ route('store.cart') }}" class="mt-3 block text-center text-sm text-slate-500 hover:text-slate-700">{{ __('store.checkout.back_cart') }}</a>
            </div>
        </div>
    </form>

    @if(isset($shippingMethods) && $shippingMethods->isNotEmpty())
    @push('scripts')
    @php
        $checkoutScriptLocaleTag = match (app()->getLocale()) {
            'tr' => 'tr-TR',
            'it' => 'it-IT',
            default => 'en-US',
        };
    @endphp
    <script>
    (function() {
        var cartTotalTry = {{ (float) $cartTotal }};
        var rate = {{ (float) $selectedCurrency->exchange_rate }};
        var symbol = {{ json_encode($selectedCurrency->symbol) }};
        var localeTag = @json($checkoutScriptLocaleTag);
        var freeLabel = @json(__('store.checkout.free'));
        function format(amountTry) {
            if (amountTry === 0) return freeLabel;
            var amount = rate && rate !== 1 ? amountTry / rate : amountTry;
            return amount.toLocaleString(localeTag, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + symbol;
        }
        document.querySelectorAll('input[name="shipping_method_id"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                var costTry = parseFloat(this.getAttribute('data-cost-try') || '0');
                document.getElementById('checkout-shipping').textContent = format(costTry);
                var totalTry = cartTotalTry + costTry;
                document.getElementById('checkout-total').textContent = format(totalTry);
            });
        });
    })();
    </script>
    @endpush
    @endif
@endsection
