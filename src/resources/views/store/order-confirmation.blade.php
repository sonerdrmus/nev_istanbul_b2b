@extends('store.layout')

@section('title', 'Sipariş Onayı')

@section('content')
    @php 
        $selectedCurrency = $selectedCurrency ?? \App\Models\Currency::getDefault();
        $totalSymbol = $selectedCurrency?->symbol ?? '₺';
    @endphp
    <div class="max-w-2xl mx-auto">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-primary-50 border-b border-primary-100 px-6 py-8 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-primary-500 flex items-center justify-center text-white mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-900">Siparişiniz Alındı</h1>
                <p class="mt-2 text-slate-600">Teşekkür ederiz. Sipariş numaranız: <strong class="text-primary-700">{{ $order->order_number }}</strong></p>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Sipariş Özeti</h2>
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
                                            @if($optName === 'size_quantities' && is_array($optValue))
                                                @php $sizeParts = array_filter($optValue, fn($q) => (int)$q > 0); @endphp
                                                @if(count($sizeParts) > 0)
                                                    <li><span class="font-medium text-slate-700">Beden dağılımı:</span> @foreach($sizeParts as $size => $qty){{ $size }}: {{ $qty }}@if(!$loop->last), @endif @endforeach</li>
                                                @endif
                                            @elseif($optValue !== null && $optValue !== '' && !is_array($optValue))
                                                <li><span class="font-medium text-slate-700">{{ $optName }}:</span> {{ $optValue }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 pt-4 border-t border-slate-200 space-y-2 text-sm">
                        @if($order->shipping_method_id)
                            <div class="flex justify-between text-slate-600">
                                <span>Kargo ({{ $order->shippingMethod?->name ?? 'Kargo' }})</span>
                                <span class="whitespace-nowrap">{{ (float) $order->shipping_cost > 0 ? $selectedCurrency->format($selectedCurrency->convertFromTRY((float) $order->shipping_cost)) : 'Ücretsiz' }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-2 pt-2 border-t border-slate-200 flex justify-between font-semibold text-slate-900">
                        <span>Toplam</span>
                        @php
    $convertedOrderTotal = $selectedCurrency->convertFromTRY($order->total);
@endphp
<span class="whitespace-nowrap">{{ $selectedCurrency->format($convertedOrderTotal) }}</span>
                    </div>
                </div>

                <div class="rounded-xl bg-amber-50 border border-amber-200 p-5">
                    <h2 class="text-sm font-semibold text-amber-800 flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Havale / EFT ile Ödeme
                    </h2>
                    @if($order->bankAccount)
                        <div class="text-sm text-amber-800/90 space-y-1">
                            <p><strong>Banka:</strong> {{ $order->bankAccount->bank_name }}</p>
                            @if($order->bankAccount->branch)<p><strong>Şube:</strong> {{ $order->bankAccount->branch }}</p>@endif
                            @if($order->bankAccount->iban)<p><strong>IBAN:</strong> <code class="bg-amber-100/80 px-1 rounded break-all">{{ $order->bankAccount->iban }}</code></p>@endif
                            @if($order->bankAccount->account_holder)<p><strong>Hesap Sahibi:</strong> {{ $order->bankAccount->account_holder }}</p>@endif
                            @if($order->bankAccount->currency)<p><strong>Para Birimi:</strong> {{ $order->bankAccount->currency }}</p>@endif
                            <p class="mt-2 pt-2 border-t border-amber-200/80"><strong>Açıklama:</strong> Sipariş no <strong>{{ $order->order_number }}</strong> yazarak havale/EFT yapınız.</p>
                        </div>
                    @elseif(config('store.bank_transfer.enabled') && config('store.bank_transfer.iban'))
                        <div class="text-sm text-amber-800/90 space-y-1">
                            <p><strong>Banka:</strong> {{ config('store.bank_transfer.bank_name') }}</p>
                            <p><strong>IBAN:</strong> <code class="bg-amber-100/80 px-1 rounded">{{ config('store.bank_transfer.iban') }}</code></p>
                            @if(config('store.bank_transfer.account_holder'))<p><strong>Hesap Sahibi:</strong> {{ config('store.bank_transfer.account_holder') }}</p>@endif
                            @if(config('store.bank_transfer.branch'))<p><strong>Şube:</strong> {{ config('store.bank_transfer.branch') }}</p>@endif
                            <p class="mt-2 pt-2 border-t border-amber-200/80"><strong>Açıklama:</strong> {{ config('store.bank_transfer.description') }} <strong>{{ $order->order_number }}</strong></p>
                        </div>
                    @else
                        <p class="text-sm text-amber-800/90">Siparişiniz onaylandıktan sonra <strong>{{ $order->customer_email }}</strong> adresine banka hesap bilgileri ve ödeme talimatları gönderilecektir.</p>
                        <p class="text-sm text-amber-800/90 mt-2">Ödemenizi yaptıktan sonra dekontu aynı e-posta adresine göndererek işlemin tamamlanmasını sağlayabilirsiniz.</p>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-medium text-sm transition-colors">Alışverişe Devam</a>
                    <a href="{{ route('store.cart') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-medium text-sm hover:bg-slate-50 transition-colors">Sepete Git</a>
                </div>
            </div>
        </div>
    </div>
@endsection
