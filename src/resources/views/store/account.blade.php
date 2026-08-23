@extends('store.layout')

@section('title', __('store.account.title'))

@section('content')
    @php
        $statusLabel = fn (string $status) => __('store.account.status.'.$status);
        $statusClass = fn (string $status) => match ($status) {
            'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            'cancelled' => 'bg-red-50 text-red-700 border-red-100',
            default => 'bg-amber-50 text-amber-800 border-amber-100',
        };
    @endphp

    <div class="space-y-8">
        <header class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary-600">{{ __('store.account.kicker') }}</p>
                <h1 class="mt-2 text-2xl sm:text-3xl font-semibold tracking-tight text-slate-900">{{ __('store.account.hello', ['name' => $user->name]) }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ __('store.account.lead') }}</p>
            </div>
            <form action="{{ route('store.logout') }}" method="post">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    {{ __('store.account.logout') }}
                </button>
            </form>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
            <aside class="lg:col-span-4 space-y-4">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">{{ __('store.account.profile') }}</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-400">{{ __('store.account.name') }}</dt>
                            <dd class="mt-0.5 font-medium text-slate-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">{{ __('store.account.email') }}</dt>
                            <dd class="mt-0.5 font-medium text-slate-900">{{ $user->email }}</dd>
                        </div>
                        @if($user->company)
                            <div>
                                <dt class="text-slate-400">{{ __('store.account.company') }}</dt>
                                <dd class="mt-0.5 font-medium text-slate-900">{{ $user->company->name }}</dd>
                                @if($user->company->code)
                                    <dd class="mt-0.5 text-xs text-slate-500">{{ $user->company->code }}</dd>
                                @endif
                            </div>
                        @endif
                    </dl>
                </section>

                <a href="{{ route('home') }}" class="flex items-center justify-between gap-3 rounded-3xl border border-slate-200 bg-slate-900 px-5 py-4 text-white hover:bg-slate-800 transition-colors">
                    <span>
                        <span class="block text-sm font-semibold">{{ __('store.account.shop_cta') }}</span>
                        <span class="mt-1 block text-xs text-slate-300">{{ __('store.account.shop_hint') }}</span>
                    </span>
                    <svg class="w-5 h-5 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </aside>

            <section class="lg:col-span-8 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-slate-900">{{ __('store.account.orders') }}</h2>
                    <span class="text-xs font-medium text-slate-400">{{ __('store.account.orders_count', ['count' => $orders->count()]) }}</span>
                </div>

                @if($orders->isEmpty())
                    <div class="mt-8 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-5 py-10 text-center">
                        <p class="text-sm font-medium text-slate-700">{{ __('store.account.orders_empty') }}</p>
                        <a href="{{ route('home') }}" class="mt-4 inline-flex items-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">{{ __('store.account.shop_cta') }}</a>
                    </div>
                @else
                    <ul class="mt-5 divide-y divide-slate-100">
                        @foreach($orders as $order)
                            <li class="py-4 first:pt-0 last:pb-0">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-2xl px-3 py-2 -mx-3">
                                    <a href="{{ route('store.order-confirmation', $order) }}" class="min-w-0 flex-1 hover:opacity-80 transition-opacity">
                                        <p class="text-sm font-semibold text-slate-900">{{ $order->order_number }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $order->created_at?->format('d.m.Y H:i') }} · {{ __('store.account.items_count', ['count' => $order->items_count]) }}</p>
                                    </a>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $statusClass($order->status) }}">{{ $statusLabel($order->status) }}</span>
                                        <span class="text-sm font-semibold text-slate-900">{{ number_format((float) $order->total, 2, ',', '.') }} ₺</span>
                                        <a href="{{ route('store.proforma.pdf', $order) }}" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-50">{{ __('store.order_confirmation.download_pdf') }}</a>
                                        <a href="{{ route('store.proforma.excel', $order) }}" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-50">{{ __('store.order_confirmation.download_excel') }}</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
@endsection
