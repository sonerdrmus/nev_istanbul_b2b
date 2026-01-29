@extends('store.layout')

@section('title', 'Sepetim')

@section('content')
    @php 
        $selectedCurrency = $selectedCurrency ?? \App\Models\Currency::getDefault();
        $totalSymbol = $selectedCurrency?->symbol ?? '₺';
    @endphp
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Sepetim</h1>
        <p class="mt-2 text-slate-600">Siparişi tamamlamak için ödemeye geçin.</p>
    </div>

    @if($cartItems->isEmpty())
        <div class="rounded-2xl bg-white border border-slate-200 p-12 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-3xl mb-4">🛒</div>
            <p class="text-slate-600 font-medium">Sepetiniz boş.</p>
            <a href="{{ route('home') }}" class="inline-flex mt-4 px-5 py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-medium text-sm transition-colors">Ürünlere Git</a>
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
                                            <img src="{{ Storage::url($p->image) }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-slate-400 text-2xl">📦</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-primary-600 font-medium">{{ $p->company?->name }}</p>
                                        <h2 class="font-semibold text-slate-900 truncate">{{ $p->name }}</h2>
                                        @if(!empty($item->variations))
                                            <p class="text-slate-500 text-sm mt-0.5">{{ implode(', ', array_map(fn($k, $v) => "{$k}: {$v}", array_keys($item->variations), $item->variations)) }}</p>
                                        @endif
                                        @php
    $deltaTry = (float) ($item->variation_price_delta_total ?? 0);
    $unitTry = (float) ($item->unit_price_try ?? $item->subtotal / max(1, (int) $item->quantity));
    $unitConverted = $selectedCurrency->convertFromTRY($unitTry);
@endphp
                                        <p class="text-slate-500 text-sm mt-0.5 whitespace-nowrap">
                                            {{ $selectedCurrency->format($unitConverted) }}
                                            @if($deltaTry !== 0.0)
                                                <span class="text-xs text-slate-400">(varyasyon: {{ ($deltaTry > 0 ? '+' : '') . number_format($selectedCurrency->convertFromTRY($deltaTry), 2, ',', '.') }} {{ $selectedCurrency->symbol }})</span>
                                            @endif
                                            × <input type="number" name="items[{{ $loop->index }}][quantity]" value="{{ $item->quantity }}" min="{{ $p->getMinimumOrderQuantity() }}" class="w-16 rounded border border-slate-300 px-2 py-1 text-sm inline-block" />
                                        </p>
                                        @if(!empty($item->variation_price_breakdown))
                                            <div class="mt-1 text-xs text-slate-500">
                                                @foreach($item->variation_price_breakdown as $vName => $pairs)
                                                    @foreach($pairs as $opt => $d)
                                                        @php $dConv = $selectedCurrency->convertFromTRY((float) $d); @endphp
                                                        <span class="inline-block mr-2">{{ $vName }} {{ $opt }}: {{ ($dConv > 0 ? '+' : '') . $selectedCurrency->format($dConv) }}</span>
                                                    @endforeach
                                                @endforeach
                                            </div>
                                        @endif
                                        <input type="hidden" name="items[{{ $loop->index }}][cart_key]" value="{{ $item->cart_key }}">
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        @php
    // $item->subtotal zaten TL cinsinden (getCartItems'da çevrildi)
    $convertedSubtotal = $selectedCurrency->convertFromTRY($item->subtotal);
@endphp
<p class="font-bold text-slate-900 whitespace-nowrap">{{ $selectedCurrency->format($convertedSubtotal) }}</p>
                                        <button type="button" onclick="if(confirm('Ürünü sepetten çıkarmak istiyor musunuz?')) { document.getElementById('remove-{{ $item->cart_key }}').submit(); }" class="text-sm text-red-600 hover:text-red-700 font-medium">Kaldır</button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-medium text-sm transition-colors">Sepeti Güncelle</button>
                        <a href="{{ route('home') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-medium text-sm hover:bg-slate-50 transition-colors">Alışverişe Devam</a>
                    </div>
                </div>

                <div class="mt-8 lg:mt-0 lg:col-span-4">
                    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 sticky top-24">
                        <h2 class="text-lg font-semibold text-slate-900 mb-4">Sipariş Özeti</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-slate-600">
                                <span>Ara Toplam ({{ $cartCount }} ürün)</span>
@php
    // $cartTotal zaten TL cinsinden
    $convertedCartTotal = $selectedCurrency->convertFromTRY($cartTotal);
@endphp
                                <span class="whitespace-nowrap">{{ $selectedCurrency->format($convertedCartTotal) }}</span>
                            </div>
                            <div class="border-t border-slate-200 pt-3 mt-3 flex justify-between font-semibold text-slate-900 text-base">
                                <span>Toplam</span>
                                <span class="whitespace-nowrap">{{ $selectedCurrency->format($convertedCartTotal) }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-3">Ödeme: Havale / EFT</p>
                        <a href="{{ route('store.checkout') }}" class="mt-5 w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-semibold shadow-sm hover:shadow transition-all duration-200">
                            Ödemeye Geç
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
