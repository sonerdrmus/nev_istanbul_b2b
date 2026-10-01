@extends('store.layout')

@section('title', __('store.order_confirmation.title'))

@section('content')
    @php
        $selectedCurrency = $selectedCurrency ?? \App\Models\Currency::getDefault();
        $statusKey = 'store.account.status.'.$order->status;
        $statusText = \Illuminate\Support\Facades\Lang::has($statusKey) ? __($statusKey) : $order->status;
        $statusClass = match ($order->status) {
            'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            'cancelled' => 'bg-red-50 text-red-700 border-red-100',
            default => 'bg-amber-50 text-amber-800 border-amber-100',
        };
        $itemsSubtotal = $order->items->sum(fn ($item) => (float) $item->subtotal);
        $meta = array_values(array_filter([
            ['label' => __('store.order_confirmation.placed_at'), 'value' => $order->created_at?->timezone(config('app.timezone'))->format('d.m.Y H:i')],
            ['label' => __('store.order_confirmation.customer'), 'value' => $order->customer_name],
            ['label' => __('store.account.email'), 'value' => $order->customer_email],
            ['label' => __('store.order_confirmation.phone'), 'value' => $order->customer_phone],
            ['label' => __('store.order_confirmation.address'), 'value' => $order->customer_address],
            ['label' => __('store.order_confirmation.notes'), 'value' => $order->notes],
        ], fn (array $row): bool => filled($row['value'])));
    @endphp

    <div class="mx-auto w-full max-w-6xl space-y-6">
        <header class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-600 text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-600">{{ __('store.order_confirmation.details') }}</p>
                        <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ __('store.order_confirmation.heading') }}</h1>
                        <p class="mt-1 text-sm text-slate-500">{{ __('store.order_confirmation.thanks') }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold tracking-wide text-slate-900">{{ $order->order_number }}</span>
                    <span class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold {{ $statusClass }}">{{ $statusText }}</span>
                </div>
            </div>
            @if($meta !== [])
                <dl class="grid grid-cols-1 gap-px bg-slate-100 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($meta as $row)
                        <div class="bg-white px-5 py-4 sm:px-7">
                            <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ $row['label'] }}</dt>
                            <dd class="mt-1 whitespace-pre-wrap break-words text-sm font-medium leading-snug text-slate-900">{{ $row['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </header>

        <section class="space-y-4">
            <div class="flex items-end justify-between gap-3 px-1">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-500">{{ __('store.order_confirmation.summary') }}</h2>
                <span class="text-xs font-medium text-slate-400">{{ __('store.account.items_count', ['count' => $order->items->count()]) }}</span>
            </div>

            @foreach($order->items as $item)
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <header class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-start sm:justify-between sm:px-6">
                        <div class="flex min-w-0 items-start gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-600/10 text-xs font-bold text-primary-700">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="min-w-0">
                                <h3 class="text-base font-semibold leading-snug text-slate-900">{{ \App\Support\CatalogLabelTranslator::label((string) $item->product_name) }}</h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $item->quantity }} {{ __('store.cart.units') }}
                                    <span class="text-slate-300">·</span>
                                    {{ __('store.order_confirmation.unit_price') }}
                                    {{ $selectedCurrency->format($selectedCurrency->convertFromTRY($item->price)) }}
                                </p>
                            </div>
                        </div>
                        <div class="sm:text-right">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('store.order_confirmation.line_total') }}</p>
                            <p class="mt-0.5 text-lg font-semibold text-slate-900">{{ $selectedCurrency->format($selectedCurrency->convertFromTRY($item->subtotal)) }}</p>
                        </div>
                    </header>
                    <div class="bg-slate-50/60 px-4 py-4 sm:px-5">
                        @include('store.partials.order-item-detail', ['item' => $item])
                    </div>
                </article>
            @endforeach
        </section>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-start">
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-amber-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    {{ __('store.order_confirmation.payment_wire') }}
                </h2>
                @if($order->bankAccount)
                    <dl class="grid grid-cols-1 gap-3 text-sm text-amber-950/90 sm:grid-cols-2">
                        <div>
                            <dt class="text-[11px] font-semibold uppercase tracking-wider text-amber-800/70">{{ __('store.order_confirmation.bank') }}</dt>
                            <dd class="mt-0.5 font-semibold">{{ $order->bankAccount->bank_name }}</dd>
                        </div>
                        @if($order->bankAccount->branch)
                            <div>
                                <dt class="text-[11px] font-semibold uppercase tracking-wider text-amber-800/70">{{ __('store.checkout.branch') }}</dt>
                                <dd class="mt-0.5 font-medium">{{ $order->bankAccount->branch }}</dd>
                            </div>
                        @endif
                        @if($order->bankAccount->iban)
                            <div class="sm:col-span-2">
                                <dt class="text-[11px] font-semibold uppercase tracking-wider text-amber-800/70">{{ __('store.order_confirmation.iban') }}</dt>
                                <dd class="mt-0.5 break-all font-semibold">{{ $order->bankAccount->iban }}</dd>
                            </div>
                        @endif
                        @if($order->bankAccount->account_holder)
                            <div>
                                <dt class="text-[11px] font-semibold uppercase tracking-wider text-amber-800/70">{{ __('store.order_confirmation.holder') }}</dt>
                                <dd class="mt-0.5 font-medium">{{ $order->bankAccount->account_holder }}</dd>
                            </div>
                        @endif
                        @if($order->bankAccount->currency)
                            <div>
                                <dt class="text-[11px] font-semibold uppercase tracking-wider text-amber-800/70">{{ __('store.order_confirmation.currency') }}</dt>
                                <dd class="mt-0.5 font-medium">{{ $order->bankAccount->currency }}</dd>
                            </div>
                        @endif
                        <div class="sm:col-span-2 border-t border-amber-200/80 pt-3">
                            <dt class="text-[11px] font-semibold uppercase tracking-wider text-amber-800/70">{{ __('store.order_confirmation.reference_label') }}</dt>
                            <dd class="mt-0.5 font-medium">{{ __('store.order_confirmation.transfer_note', ['number' => $order->order_number]) }}</dd>
                        </div>
                    </dl>
                @elseif(config('store.bank_transfer.enabled') && config('store.bank_transfer.iban'))
                    <dl class="space-y-2 text-sm text-amber-950/90">
                        <div>
                            <dt class="text-[11px] font-semibold uppercase tracking-wider text-amber-800/70">{{ __('store.order_confirmation.bank') }}</dt>
                            <dd class="mt-0.5 font-semibold">{{ config('store.bank_transfer.bank_name') }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-semibold uppercase tracking-wider text-amber-800/70">{{ __('store.order_confirmation.iban') }}</dt>
                            <dd class="mt-0.5 break-all font-semibold">{{ config('store.bank_transfer.iban') }}</dd>
                        </div>
                        @if(config('store.bank_transfer.account_holder'))
                            <div>
                                <dt class="text-[11px] font-semibold uppercase tracking-wider text-amber-800/70">{{ __('store.order_confirmation.holder') }}</dt>
                                <dd class="mt-0.5 font-medium">{{ config('store.bank_transfer.account_holder') }}</dd>
                            </div>
                        @endif
                        <div class="border-t border-amber-200/80 pt-3">
                            <dt class="text-[11px] font-semibold uppercase tracking-wider text-amber-800/70">{{ __('store.order_confirmation.reference_label') }}</dt>
                            <dd class="mt-0.5 font-medium">{{ config('store.bank_transfer.description') }} <strong>{{ $order->order_number }}</strong></dd>
                        </div>
                    </dl>
                @else
                    <p class="text-sm leading-relaxed text-amber-950/90">{{ __('store.order_confirmation.email_bank_info', ['email' => $order->customer_email]) }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-amber-950/90">{{ __('store.order_confirmation.email_receipt') }}</p>
                @endif
            </div>

            <aside class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="space-y-3 px-5 py-5 text-sm">
                    <div class="flex items-center justify-between gap-3 text-slate-600">
                        <span>{{ __('store.checkout.subtotal') }}</span>
                        <span class="font-medium text-slate-900">{{ $selectedCurrency->format($selectedCurrency->convertFromTRY($itemsSubtotal)) }}</span>
                    </div>
                    @if($order->shipping_method_id)
                        <div class="flex items-center justify-between gap-3 text-slate-600">
                            <span>{{ __('store.order_confirmation.shipping') }}@if($order->shippingMethod?->name) ({{ $order->shippingMethod->name }})@endif</span>
                            <span class="font-medium text-slate-900">{{ (float) $order->shipping_cost > 0 ? $selectedCurrency->format($selectedCurrency->convertFromTRY((float) $order->shipping_cost)) : __('store.checkout.free') }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between gap-3 border-t border-slate-200 pt-3 text-base font-semibold text-slate-900">
                        <span>{{ __('store.order_confirmation.total') }}</span>
                        <span>{{ $selectedCurrency->format($selectedCurrency->convertFromTRY($order->total)) }}</span>
                    </div>
                </div>
                <div class="flex flex-col gap-2 border-t border-slate-100 bg-slate-50/70 px-5 py-4">
                    <button type="button" disabled class="inline-flex cursor-not-allowed items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm font-medium text-slate-400">{{ __('store.order_confirmation.download_pdf') }} — {{ __('store.account.documents_preparing') }}</button>
                    <button type="button" disabled class="inline-flex cursor-not-allowed items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm font-medium text-slate-400">{{ __('store.order_confirmation.download_order_form') }} — {{ __('store.account.documents_preparing') }}</button>
                    <button type="button" disabled class="inline-flex cursor-not-allowed items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm font-medium text-slate-400">{{ __('store.order_confirmation.download_excel') }} — {{ __('store.account.documents_preparing') }}</button>
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-primary-700">{{ __('store.order_confirmation.continue_shopping') }}</a>
                    <a href="{{ route('store.account') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-white">{{ __('store.account.title') }}</a>
                </div>
            </aside>
        </div>
    </div>
@endsection
