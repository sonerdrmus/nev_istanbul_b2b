@php
    /** @var \App\Models\SizeTable $sizeTable */
    $wrapClass = $wrapClass ?? 'hidden mt-4 first:mt-0 size-table-wrap';
    $wrapId = $wrapId ?? ($sizeTable->slug.'-size-table-wrap');
    $wrapExtraAttrs = $wrapExtraAttrs ?? '';
@endphp
<div id="{{ $wrapId }}" class="{{ $wrapClass }}" data-slug="{{ $sizeTable->slug }}" data-trigger-variation="{{ e($sizeTable->trigger_variation_name ?? '') }}" data-trigger-value="{{ e($sizeTable->trigger_option_value ?? '') }}" {!! $wrapExtraAttrs !!}>
    <p class="text-base sm:text-lg font-semibold text-slate-700 mb-3 sm:mb-4 flex items-center gap-2">
        <span class="h-px flex-1 max-w-[40px] rounded-full bg-primary-200"></span>
        {{ $sizeTable->localized_title ?: __('store.product.choose_sizes_default') }}
    </p>
    <div class="size-qty-mobile space-y-2 lg:hidden">
        @foreach($sizeTable->columns as $col)
            <label class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5">
                <span class="min-w-0 text-sm font-semibold text-slate-800">{{ $col->size_value }}</span>
                <input type="number" inputmode="numeric" name="{{ $sizeTable->slug }}_size_qty_{{ $col->size_value }}" data-size="{{ $col->size_value }}" data-price-multiplier="{{ number_format((float) ($col->price_multiplier ?? 1), 4, '.', '') }}" min="0" max="9999" value="0" class="size-table-input {{ $sizeTable->slug }}-size-input h-11 w-28 shrink-0 rounded-lg border border-slate-300 px-2 text-center text-base text-slate-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30">
            </label>
        @endforeach
    </div>
    <div class="size-qty-desktop hidden rounded-xl border border-slate-200 bg-slate-50/70 p-3 shadow-sm lg:block">
        <style>
            .size-qty-desktop input[type="number"]::-webkit-outer-spin-button,
            .size-qty-desktop input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
            .size-qty-desktop input[type="number"] { -moz-appearance: textfield; appearance: textfield; }
        </style>
        <div class="grid gap-2" style="grid-template-columns: repeat(6, minmax(0, 1fr));">
            @foreach($sizeTable->columns as $col)
                <label class="flex min-w-0 flex-col gap-1.5 rounded-xl border border-slate-200 bg-white px-1.5 py-2">
                    <span class="truncate text-center text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $col->size_value }}</span>
                    <input type="number" inputmode="numeric" name="{{ $sizeTable->slug }}_size_qty_{{ $col->size_value }}_desktop" data-size="{{ $col->size_value }}" data-price-multiplier="{{ number_format((float) ($col->price_multiplier ?? 1), 4, '.', '') }}" min="0" max="9999" value="0" class="size-table-input {{ $sizeTable->slug }}-size-input h-10 w-full min-w-0 rounded-lg border border-slate-300 px-0.5 text-center text-sm text-slate-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30">
                </label>
            @endforeach
        </div>
    </div>
    <div class="mt-3 flex flex-wrap items-center justify-center gap-3 rounded-xl border border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100/80 px-4 py-3 text-sm">
        <span class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-1.5 shadow-sm ring-1 ring-slate-200/80">
            <span class="text-slate-500 font-medium">{{ __('store.product.size_min_chip') }}</span>
            <span class="font-bold text-slate-800">{{ __('store.product.stock_units_fmt', ['count' => number_format($minOrder)]) }}</span>
        </span>
        <span class="text-slate-300">·</span>
        <span class="inline-flex items-center gap-2 rounded-lg bg-primary-50 px-3 py-1.5 shadow-sm ring-1 ring-primary-200/80">
            <span class="text-slate-600 font-medium">{{ __('store.product.size_entered_total') }}</span>
            <span id="{{ $sizeTable->slug }}-size-total" class="font-bold text-primary-700">0</span>
            <span class="text-slate-600">{{ __('store.product.units_suffix') }}</span>
        </span>
    </div>
</div>
