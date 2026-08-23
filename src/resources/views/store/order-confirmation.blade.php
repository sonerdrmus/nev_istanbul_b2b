@extends('store.layout')

@section('title', __('store.order_confirmation.title'))

@section('content')
    @php
        $selectedCurrency = $selectedCurrency ?? \App\Models\Currency::getDefault();
        $totalSymbol = $selectedCurrency?->symbol ?? '₺';
    @endphp
    <div class="w-full">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-primary-50 border-b border-primary-100 px-6 py-8 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-primary-500 flex items-center justify-center text-white mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-900">{{ __('store.order_confirmation.heading') }}</h1>
                <p class="mt-2 text-slate-600">{{ __('store.order_confirmation.thanks') }} <strong class="text-primary-700">{{ $order->order_number }}</strong></p>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">{{ __('store.order_confirmation.summary') }}</h2>
                    <ul class="space-y-3">
                        @foreach($order->items as $item)
                            <li class="pb-2 border-b border-slate-100 last:border-0">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-700 font-medium">{{ $item->product_name }} × {{ $item->quantity }}</span>
                                    @php
    $convertedSubtotal = $selectedCurrency->convertFromTRY($item->subtotal);
@endphp
<span class="font-medium text-slate-900 whitespace-nowrap">{{ $selectedCurrency->format($convertedSubtotal) }}</span>
                                </div>
                                @if(!empty($item->variation_data) && is_array($item->variation_data))
                                    <ul class="mt-1.5 text-xs text-slate-600 space-y-0.5">
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
                                            @if($optName === 'size_quantities' && is_array($optValue))
                                                @php $sizeParts = array_filter($optValue, fn($q) => (int)$q > 0); @endphp
                                                @if(count($sizeParts) > 0)
                                                    <li><span class="font-medium text-slate-700">{{ __('store.order_confirmation.size_breakdown') }}</span> @foreach($sizeParts as $size => $qty){{ $size }}: {{ $qty }}@if(!$loop->last), @endif @endforeach</li>
                                                @endif
                                            @elseif($optName === 'product_customization_table' && is_array($optValue))
                                                @php
                                                    $custRowsOc = $optValue['rows'] ?? (isset($optValue['row_id']) ? [$optValue] : []);
                                                @endphp
                                                @if(count($custRowsOc) > 0)
                                                    <li>
                                                        <span class="font-medium text-slate-700">{{ __('store.product.customization_summary_section_label') }}</span>
                                                        <ul class="mt-0.5 ml-3 list-disc space-y-0.5">
                                                            @foreach($custRowsOc as $crow)
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
                                                @php $dispOc = \App\Support\LabelTypeVariationDisplay::formatVariationValue($optValue); @endphp
                                                @if($dispOc !== null && $dispOc !== '')
                                                    <li><span class="font-medium text-slate-700">{{ $optName }}:</span> {{ $dispOc }}</li>
                                                @endif
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                                @if(!empty($item->variation_data['quick_order']['notes'] ?? null))
                                    <p class="mt-1.5 text-xs text-slate-600">{{ __('store.product.quick_order_summary') }}: {{ $item->variation_data['quick_order']['notes'] }}</p>
                                @endif
                                @if(!empty($item->variation_data['quick_order']['image_url'] ?? null))
                                    <a href="{{ $item->variation_data['quick_order']['image_url'] }}" target="_blank" rel="noopener" class="mt-1 inline-flex text-xs font-medium text-primary-600 hover:text-primary-700">{{ __('store.product.quick_order_image_label') }}</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 pt-4 border-t border-slate-200 space-y-2 text-sm">
                        @if($order->shipping_method_id)
                            <div class="flex justify-between text-slate-600">
                                <span>{{ __('store.order_confirmation.shipping') }} ({{ $order->shippingMethod?->name ?? __('store.order_confirmation.shipping_generic') }})</span>
                                <span class="whitespace-nowrap">{{ (float) $order->shipping_cost > 0 ? $selectedCurrency->format($selectedCurrency->convertFromTRY((float) $order->shipping_cost)) : __('store.checkout.free') }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-2 pt-2 border-t border-slate-200 flex justify-between font-semibold text-slate-900">
                        <span>{{ __('store.order_confirmation.total') }}</span>
                        @php
    $convertedOrderTotal = $selectedCurrency->convertFromTRY($order->total);
@endphp
<span class="whitespace-nowrap">{{ $selectedCurrency->format($convertedOrderTotal) }}</span>
                    </div>
                </div>

                <div class="rounded-xl bg-amber-50 border border-amber-200 p-5">
                    <h2 class="text-sm font-semibold text-amber-800 flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        {{ __('store.order_confirmation.payment_wire') }}
                    </h2>
                    @if($order->bankAccount)
                        <div class="text-sm text-amber-800/90 space-y-1">
                            <p><strong>{{ __('store.order_confirmation.bank') }}:</strong> {{ $order->bankAccount->bank_name }}</p>
                            @if($order->bankAccount->branch)<p><strong>{{ __('store.checkout.branch') }}:</strong> {{ $order->bankAccount->branch }}</p>@endif
                            @if($order->bankAccount->iban)<p><strong>{{ __('store.order_confirmation.iban') }}:</strong> <code class="bg-amber-100/80 px-1 rounded break-all">{{ $order->bankAccount->iban }}</code></p>@endif
                            @if($order->bankAccount->account_holder)<p><strong>{{ __('store.order_confirmation.holder') }}:</strong> {{ $order->bankAccount->account_holder }}</p>@endif
                            @if($order->bankAccount->currency)<p><strong>{{ __('store.order_confirmation.currency') }}:</strong> {{ $order->bankAccount->currency }}</p>@endif
                            <p class="mt-2 pt-2 border-t border-amber-200/80"><strong>{{ __('store.order_confirmation.reference_label') }}:</strong> {{ __('store.order_confirmation.transfer_note', ['number' => $order->order_number]) }}</p>
                        </div>
                    @elseif(config('store.bank_transfer.enabled') && config('store.bank_transfer.iban'))
                        <div class="text-sm text-amber-800/90 space-y-1">
                            <p><strong>{{ __('store.order_confirmation.bank') }}:</strong> {{ config('store.bank_transfer.bank_name') }}</p>
                            <p><strong>{{ __('store.order_confirmation.iban') }}:</strong> <code class="bg-amber-100/80 px-1 rounded">{{ config('store.bank_transfer.iban') }}</code></p>
                            @if(config('store.bank_transfer.account_holder'))<p><strong>{{ __('store.order_confirmation.holder') }}:</strong> {{ config('store.bank_transfer.account_holder') }}</p>@endif
                            @if(config('store.bank_transfer.branch'))<p><strong>{{ __('store.checkout.branch') }}:</strong> {{ config('store.bank_transfer.branch') }}</p>@endif
                            <p class="mt-2 pt-2 border-t border-amber-200/80"><strong>{{ __('store.order_confirmation.reference_label') }}:</strong> {{ config('store.bank_transfer.description') }} <strong>{{ $order->order_number }}</strong></p>
                        </div>
                    @else
                        <p class="text-sm text-amber-800/90">{{ __('store.order_confirmation.email_bank_info', ['email' => $order->customer_email]) }}</p>
                        <p class="text-sm text-amber-800/90 mt-2">{{ __('store.order_confirmation.email_receipt') }}</p>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('store.proforma.pdf', ['order' => $order, 'currency' => $selectedCurrency?->code]) }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-medium text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ __('store.order_confirmation.download_pdf') }}
                    </a>
                    <a href="{{ route('store.proforma.excel', ['order' => $order, 'currency' => $selectedCurrency?->code]) }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-800 font-medium text-sm hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        {{ __('store.order_confirmation.download_excel') }}
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-medium text-sm transition-colors">{{ __('store.order_confirmation.continue_shopping') }}</a>
                    <a href="{{ route('store.cart') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-medium text-sm hover:bg-slate-50 transition-colors">{{ __('store.order_confirmation.go_cart') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
